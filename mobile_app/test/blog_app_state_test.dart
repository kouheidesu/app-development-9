import 'package:flutter_test/flutter_test.dart';
import 'package:http/http.dart' as http;
import 'package:mobile_app/blog_app_state.dart';
import 'package:mobile_app/services/api_client.dart';

import 'helpers/memory_token_store.dart';

void main() {
  group('BlogAppState', () {
    late FakeApiClient api;
    late BlogAppState state;

    setUp(() {
      api = FakeApiClient();
      state = BlogAppState(apiClient: api);
    });

    test('login loads profile, categories, and articles', () async {
      await state.login(email: 'user@example.com', password: 'secret');

      expect(state.isAuthenticated, isTrue);
      expect(state.user?.name, 'Tester');
      expect(state.categories.length, 1);
      expect(state.articles.length, 1);
    });

    test('deleteAccount logs out and clears local state', () async {
      await state.login(email: 'user@example.com', password: 'secret');
      await state.deleteAccount();

      expect(api.deleteAccountCalled, isTrue);
      expect(state.isAuthenticated, isFalse);
      expect(state.articles, isEmpty);
      expect(state.categories, isEmpty);
    });
  });
}

class FakeApiClient extends ApiClient {
  FakeApiClient()
      : super(
          baseUrl: 'https://example.com',
          httpClient: _NoopClient(),
          tokenStore: MemoryTokenStore(),
        );

  bool deleteAccountCalled = false;

  Map<String, dynamic> get _user => {
        'id': 1,
        'name': 'Tester',
        'email': 'user@example.com',
      };

  Map<String, dynamic> get _category => {
        'id': 1,
        'name': 'General',
        'color': '#4338ca',
      };

  Map<String, dynamic> get _article => {
        'id': 1,
        'user_id': 1,
        'title': '初めての記事',
        'content': '本文',
        'status': 'draft',
        'category_id': 1,
        'table_of_contents': '',
        'notes': '',
        'seo_title': '',
        'seo_description': '',
        'created_at': DateTime.now().toIso8601String(),
      };

  @override
  Future<Map<String, dynamic>> login({
    required String email,
    required String password,
  }) async {
    return {'token': 'token', 'user': _user};
  }

  @override
  Future<Map<String, dynamic>> fetchProfile() async {
    return {'user': _user};
  }

  @override
  Future<Map<String, dynamic>> fetchCategories() async {
    return {
      'categories': [_category],
    };
  }

  @override
  Future<Map<String, dynamic>> fetchArticles() async {
    return {
      'articles': [_article],
    };
  }

  @override
  Future<void> deleteAccount() async {
    deleteAccountCalled = true;
  }

  @override
  Future<void> logout() async {}
}

class _NoopClient extends http.BaseClient {
  @override
  Future<http.StreamedResponse> send(http.BaseRequest request) {
    throw UnimplementedError();
  }
}
