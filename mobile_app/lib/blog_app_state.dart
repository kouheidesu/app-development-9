import 'package:flutter/material.dart';
import 'package:uuid/uuid.dart';

import 'models.dart';


class BlogAppState extends ChangeNotifier {
  BlogUser? _user;
  final List<Article> _articles = <Article>[];

  List<Category> categories = const <Category>[
    Category(id: '1', name: 'SEO', color: Color(0xFF6366F1)),
    Category(id: '2', name: 'マーケ', color: Color(0xFFF97316)),
    Category(id: '3', name: 'ノウハウ', color: Color(0xFF0EA5E9)),
    Category(id: '4', name: 'レビュー', color: Color(0xFF10B981)),
  ];

  List<Tag> tags = const <Tag>[
    Tag(id: '1', name: 'AI'),
    Tag(id: '2', name: 'ライティング'),
    Tag(id: '3', name: 'WordPress'),
    Tag(id: '4', name: 'SNS'),
    Tag(id: '5', name: '集客'),
  ];

  BlogUser? get user => _user;
  List<Article> get articles => List<Article>.unmodifiable(_articles);

  bool get isAuthenticated => _user != null;

  void register({
    required String name,
    required String email,
    required String password,
  }) {
    // デモ実装なのでパスワードは保持しない
    _user = BlogUser(
      id: const Uuid().v4(),
      name: name.trim().isEmpty ? 'ゲスト' : name.trim(),
      email: email.trim(),
    );
    // 初期データがない場合のみサンプル記事を投入
    if (_articles.isEmpty) {
      _seedSampleArticles();
    }
    notifyListeners();
  }

  void login({
    required String email,
    required String password,
  }) {
    _user = BlogUser(
      id: const Uuid().v4(),
      name: email.split('@').first,
      email: email.trim(),
    );
    notifyListeners();
  }

  void logout() {
    _user = null;
    notifyListeners();
  }

  void createArticle(ArticleDraft draft) {
    if (_user == null) return;
    final article = Article(
      userId: _user!.id,
      title: draft.title.trim(),
      content: draft.content.trim(),
      status: draft.status,
      categoryId: draft.categoryId,
      tableOfContents: draft.tableOfContents.trim(),
      notes: draft.notes.trim(),
      seoTitle: draft.seoTitle.trim(),
      seoDescription: draft.seoDescription.trim(),
      featuredImage: draft.featuredImage.trim(),
      tagIds: List<String>.from(draft.tagIds),
      createdAt: DateTime.now(),
    );
    _articles.insert(0, article);
    notifyListeners();
  }

  void updateArticle(String id, ArticleDraft draft) {
    final index = _articles.indexWhere((a) => a.id == id);
    if (index == -1) return;
    final existing = _articles[index];
    _articles[index] = existing.copyWith(
      title: draft.title.trim(),
      content: draft.content.trim(),
      status: draft.status,
      categoryId: draft.categoryId,
      tableOfContents: draft.tableOfContents.trim(),
      notes: draft.notes.trim(),
      seoTitle: draft.seoTitle.trim(),
      seoDescription: draft.seoDescription.trim(),
      featuredImage: draft.featuredImage.trim(),
      tagIds: List<String>.from(draft.tagIds),
    );
    notifyListeners();
  }

  void deleteArticle(String id) {
    _articles.removeWhere((a) => a.id == id);
    notifyListeners();
  }

  Category? categoryById(String? id) {
    if (id == null) return null;
    return categories.firstWhere(
      (c) => c.id == id,
      orElse: () => Category(id: id, name: '未設定', color: Colors.grey),
    );
  }

  List<Tag> resolveTags(List<String> ids) {
    return tags.where((t) => ids.contains(t.id)).toList();
  }

  void _seedSampleArticles() {
    if (_user == null) return;
    final draft = Article(
      userId: _user!.id,
      title: '2025年版 SEO ライティング完全ガイド',
      status: ArticleStatus.inProgress,
      createdAt: DateTime.now().subtract(const Duration(days: 1)),
      content: '最新のSEOトレンドとライティングの型を整理。E-E-A-T や Helpful Content Update 対応方針も明記する。',
      categoryId: categories.first.id,
      tableOfContents: '1. 2025年のSEO概況\\n2. キーワード調査テンプレ\\n3. 構成テンプレート\\n4. 執筆チェックリスト',
      notes: '事例は自社実績から2本ピックアップ。サムネ案も添付する。',
      tagIds: <String>['1', '2'],
    );
    _articles.add(draft);
  }
}
