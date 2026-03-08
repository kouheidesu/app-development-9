import 'dart:convert';

import 'package:flutter_test/flutter_test.dart';
import 'package:http/http.dart' as http;
import 'package:http/testing.dart';
import 'package:mobile_app/blog_app_state.dart';
import 'package:mobile_app/models.dart';
import 'package:mobile_app/services/api_client.dart';
import 'package:mobile_app/services/token_store.dart';

void main() {
  group('BlogAppState', () {
    late _FakeApiServer server;
    late ApiClient apiClient;
    late _InMemoryTokenStore tokenStore;

    setUp(() {
      server = _FakeApiServer();
      tokenStore = _InMemoryTokenStore();
      apiClient = ApiClient(
        httpClient: MockClient(server.handle),
        baseUrl: 'https://example.com',
        tokenStore: tokenStore,
      );
    });

    test('ログイン成功でユーザーと記事が読み込まれる', () async {
      final state = BlogAppState(apiClient: apiClient);
      await state.initialized;

      await state.login(email: 'demo@example.com', password: 'password');

      expect(state.isAuthenticated, isTrue);
      expect(state.user?.name, 'Demo User');
      expect(state.articles, isNotEmpty);
      expect(state.categories, isNotEmpty);
      expect(tokenStore.value, 'token_123');
    });

    test('記事作成で最新記事が先頭に追加される', () async {
      final state = BlogAppState(apiClient: apiClient);
      await state.initialized;
      await state.login(email: 'demo@example.com', password: 'password');
      final initialLength = state.articles.length;

      await state.createArticle(
        ArticleDraft(title: '新規記事', content: '本文を追加'),
      );

      expect(state.articles.length, initialLength + 1);
      expect(state.articles.first.title, '新規記事');
    });
  });
}

class _FakeApiServer {
  final List<Map<String, dynamic>> _articles = <Map<String, dynamic>>[
    {
      'id': 1,
      'user_id': 1,
      'title': '最初の記事',
      'content': '本文',
      'status': 'draft',
      'category_id': 1,
      'table_of_contents': '',
      'notes': '',
      'seo_title': '',
      'seo_description': '',
      'created_at': '2024-01-01T00:00:00.000Z',
    },
  ];

  final List<Map<String, dynamic>> _categories = <Map<String, dynamic>>[
    {'id': 1, 'name': 'テスト', 'color': '#6366f1'},
  ];

  int _nextArticleId = 2;

  Future<http.Response> handle(http.Request request) async {
    final path = request.url.path;
    switch ((request.method, path)) {
      case ('POST', '/login'):
        return http.Response(
          jsonEncode({
            'token': 'token_123',
            'user': {
              'id': 1,
              'name': 'Demo User',
              'email': 'demo@example.com',
            },
          }),
          200,
          headers: {'content-type': 'application/json'},
        );
      case ('GET', '/articles'):
        return http.Response(
          jsonEncode({'articles': _articles}),
          200,
          headers: {'content-type': 'application/json'},
        );
      case ('GET', '/categories'):
        return http.Response(
          jsonEncode({'categories': _categories}),
          200,
          headers: {'content-type': 'application/json'},
        );
      case ('POST', '/articles'):
        final payload = request.body.isEmpty
            ? <String, dynamic>{}
            : jsonDecode(request.body) as Map<String, dynamic>;
        final article = <String, dynamic>{
          'id': _nextArticleId++,
          'user_id': 1,
          'title': payload['title'] ?? '',
          'content': payload['content'] ?? '',
          'status': payload['status'] ?? 'draft',
          'category_id': payload['category_id'],
          'table_of_contents': payload['table_of_contents'] ?? '',
          'notes': payload['notes'] ?? '',
          'seo_title': payload['seo_title'] ?? '',
          'seo_description': payload['seo_description'] ?? '',
          'created_at': '2024-01-02T00:00:00.000Z',
        };
        _articles.insert(0, article);
        return http.Response(
          jsonEncode({'article': article}),
          200,
          headers: {'content-type': 'application/json'},
        );
      default:
        return http.Response('Not Found', 404);
    }
  }
}

class _InMemoryTokenStore implements TokenStore {
  String? value;

  @override
  Future<void> delete() async {
    value = null;
  }

  @override
  Future<String?> read() async => value;

  @override
  Future<void> write(String token) async {
    value = token;
  }
}
