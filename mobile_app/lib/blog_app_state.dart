import 'package:flutter/material.dart';

import 'models.dart';
import 'services/api_client.dart';

class BlogAppState extends ChangeNotifier {
  BlogAppState({ApiClient? apiClient})
      : _apiClient = apiClient ?? ApiClient() {
    _initialize();
  }

  final ApiClient _apiClient;
  BlogUser? _user;
  final List<Article> _articles = <Article>[];
  List<Category> _categories = <Category>[];
  bool _isLoading = false;
  bool _isInitializing = true;

  BlogUser? get user => _user;
  List<Article> get articles => List<Article>.unmodifiable(_articles);
  List<Category> get categories => List<Category>.unmodifiable(_categories);
  bool get isAuthenticated => _user != null;
  bool get isLoading => _isLoading;
  bool get isInitializing => _isInitializing;

  Future<void> register({
    required String name,
    required String email,
    required String password,
  }) async {
    final response = await _apiClient.register(
      name: name,
      email: email,
      password: password,
    );
    await _handleAuthenticatedResponse(response);
  }

  Future<void> login({
    required String email,
    required String password,
  }) async {
    final response = await _apiClient.login(
      email: email,
      password: password,
    );
    await _handleAuthenticatedResponse(response);
  }

  Future<void> logout() async {
    try {
      await _apiClient.logout();
    } finally {
      await _apiClient.clearToken();
      _clearSession();
    }
  }

  Future<void> createArticle(ArticleDraft draft) async {
    await _ensureAuthenticated();
    await _withLoading(() async {
      final response = await _apiClient.createArticle(draft.toJson());
      final article =
          Article.fromJson(response['article'] as Map<String, dynamic>);
      _articles.insert(0, article);
    });
  }

  Future<void> updateArticle(int id, ArticleDraft draft) async {
    await _ensureAuthenticated();
    await _withLoading(() async {
      final response = await _apiClient.updateArticle(id, draft.toJson());
      final updated =
          Article.fromJson(response['article'] as Map<String, dynamic>);
      final index = _articles.indexWhere((article) => article.id == id);
      if (index >= 0) {
        _articles[index] = updated;
      } else {
        _articles.insert(0, updated);
      }
    });
  }

  Future<void> deleteArticle(int id) async {
    await _ensureAuthenticated();
    await _withLoading(() async {
      await _apiClient.deleteArticle(id);
      _articles.removeWhere((article) => article.id == id);
    });
  }

  Future<void> addCategory(String name) async {
    await _ensureAuthenticated();
    final response = await _apiClient.createCategory({'name': name});
    final category =
        Category.fromJson(response['category'] as Map<String, dynamic>);
    _categories = <Category>[..._categories, category]
      ..sort((a, b) => a.name.compareTo(b.name));
    notifyListeners();
  }

  Future<void> deleteCategory(int id) async {
    await _ensureAuthenticated();
    await _apiClient.deleteCategory(id);
    _categories = _categories.where((category) => category.id != id).toList();
    for (final article in _articles) {
      if (article.categoryId == id) {
        article.categoryId = null;
      }
    }
    notifyListeners();
  }

  Category? categoryById(int? id) {
    if (id == null) return null;
    try {
      return _categories.firstWhere((category) => category.id == id);
    } catch (_) {
      return null;
    }
  }

  Future<void> _initialize() async {
    try {
      final token = await _apiClient.loadSavedToken();
      if (token != null) {
        await _refreshSession();
      }
    } catch (_) {
      await _apiClient.clearToken();
      _clearSession();
    } finally {
      _isInitializing = false;
      notifyListeners();
    }
  }

  Future<void> _refreshSession() async {
    final profile = await _apiClient.fetchProfile();
    final userJson = profile['user'] as Map<String, dynamic>?;
    if (userJson == null) return;
    _user = BlogUser.fromJson(userJson);
    await _refreshDashboardData();
  }

  Future<void> _refreshDashboardData() {
    return _withLoading(() async {
      await Future.wait<void>([
        _loadCategories(),
        _loadArticles(),
      ]);
    });
  }

  Future<void> _loadCategories() async {
    final response = await _apiClient.fetchCategories();
    final items = response['categories'] as List<dynamic>? ?? <dynamic>[];
    _categories = items
        .map((json) => Category.fromJson(json as Map<String, dynamic>))
        .toList();
  }

  Future<void> _loadArticles() async {
    final response = await _apiClient.fetchArticles();
    final items = response['articles'] as List<dynamic>? ?? <dynamic>[];
    _articles
      ..clear()
      ..addAll(
        items.map((json) => Article.fromJson(json as Map<String, dynamic>)),
      );
  }

  Future<void> _handleAuthenticatedResponse(
    Map<String, dynamic> response,
  ) async {
    final token = response['token'] as String?;
    final userJson = response['user'] as Map<String, dynamic>?;
    if (token == null || userJson == null) {
      throw const ApiException('認証情報の取得に失敗しました');
    }
    await _apiClient.persistToken(token);
    _user = BlogUser.fromJson(userJson);
    notifyListeners();
    await _refreshDashboardData();
  }

  Future<void> _ensureAuthenticated() async {
    if (_user != null) return;
    throw const ApiException('この操作を行うにはログインが必要です');
  }

  void _clearSession() {
    _user = null;
    _articles.clear();
    _categories = <Category>[];
    _isLoading = false;
    notifyListeners();
  }

  Future<void> _withLoading(Future<void> Function() task) async {
    _isLoading = true;
    notifyListeners();
    try {
      await task();
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }
}
