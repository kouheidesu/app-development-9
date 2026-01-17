import 'dart:convert';

import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class ApiException implements Exception {
  const ApiException(this.message, {this.statusCode});

  final String message;
  final int? statusCode;

  @override
  String toString() => 'ApiException($statusCode): $message';
}

class ApiClient {
  ApiClient({http.Client? httpClient})
      : _httpClient = httpClient ?? http.Client();

  static const String _baseUrl =
      'https://app-development-9-production.up.railway.app/api';
  static const String _tokenKey = 'api_token';

  final http.Client _httpClient;
  SharedPreferences? _prefs;
  String? _token;

  Future<void> _ensurePrefs() async {
    _prefs ??= await SharedPreferences.getInstance();
    _token ??= _prefs!.getString(_tokenKey);
  }

  Future<String?> loadSavedToken() async {
    await _ensurePrefs();
    return _token;
  }

  Future<void> persistToken(String token) async {
    await _ensurePrefs();
    _token = token;
    await _prefs!.setString(_tokenKey, token);
  }

  Future<void> clearToken() async {
    await _ensurePrefs();
    _token = null;
    await _prefs!.remove(_tokenKey);
  }

  Future<Map<String, dynamic>> login({
    required String email,
    required String password,
  }) {
    return _post(
      '/login',
      body: {'email': email, 'password': password},
      auth: false,
    );
  }

  Future<Map<String, dynamic>> register({
    required String name,
    required String email,
    required String password,
  }) {
    return _post(
      '/register',
      body: {'name': name, 'email': email, 'password': password},
      auth: false,
    );
  }

  Future<Map<String, dynamic>> fetchProfile() {
    return _get('/me');
  }

  Future<void> logout() async {
    await _post('/logout', body: const {}, auth: true);
  }

  Future<Map<String, dynamic>> fetchArticles() {
    return _get('/articles');
  }

  Future<Map<String, dynamic>> createArticle(Map<String, dynamic> payload) {
    return _post('/articles', body: payload);
  }

  Future<Map<String, dynamic>> updateArticle(
    int id,
    Map<String, dynamic> payload,
  ) {
    return _put('/articles/$id', body: payload);
  }

  Future<void> deleteArticle(int id) async {
    await _delete('/articles/$id');
  }

  Future<Map<String, dynamic>> fetchCategories() {
    return _get('/categories');
  }

  Future<Map<String, dynamic>> createCategory(Map<String, dynamic> payload) {
    return _post('/categories', body: payload);
  }

  Future<void> deleteCategory(int id) async {
    await _delete('/categories/$id');
  }

  Future<Map<String, dynamic>> _get(String path) {
    return _request('GET', path);
  }

  Future<Map<String, dynamic>> _post(
    String path, {
    required Map<String, dynamic> body,
    bool auth = true,
  }) {
    return _request('POST', path, body: body, auth: auth);
  }

  Future<Map<String, dynamic>> _put(
    String path, {
    required Map<String, dynamic> body,
  }) {
    return _request('PUT', path, body: body);
  }

  Future<Map<String, dynamic>> _delete(String path) {
    return _request('DELETE', path);
  }

  Future<Map<String, dynamic>> _request(
    String method,
    String path, {
    Map<String, dynamic>? body,
    bool auth = true,
  }) async {
    await _ensurePrefs();
    final uri = Uri.parse('$_baseUrl$path');
    final headers = <String, String>{
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    };
    if (auth && _token != null) {
      headers['Authorization'] = 'Bearer $_token';
    }

    http.Response response;
    final encodedBody = body != null ? jsonEncode(body) : null;

    switch (method) {
      case 'GET':
        response = await _httpClient.get(uri, headers: headers);
        break;
      case 'POST':
        response =
            await _httpClient.post(uri, headers: headers, body: encodedBody);
        break;
      case 'PUT':
        response =
            await _httpClient.put(uri, headers: headers, body: encodedBody);
        break;
      case 'DELETE':
        response = await _httpClient.delete(uri, headers: headers);
        break;
      default:
        throw ArgumentError('Unsupported method $method');
    }

    if (response.statusCode >= 200 && response.statusCode < 300) {
      if (response.body.isEmpty) {
        return const {};
      }
      return jsonDecode(response.body) as Map<String, dynamic>;
    }

    throw ApiException(
      _errorMessage(response),
      statusCode: response.statusCode,
    );
  }

  String _errorMessage(http.Response response) {
    try {
      final decoded = jsonDecode(response.body) as Map<String, dynamic>;
      if (decoded['message'] is String) {
        return decoded['message'] as String;
      }
      if (decoded['errors'] is Map<String, dynamic>) {
        final errors = decoded['errors'] as Map<String, dynamic>;
        final first = errors.values.cast<List?>().firstWhere(
              (value) => value != null && value.isNotEmpty,
              orElse: () => null,
            );
        if (first != null) {
          return first.first?.toString() ?? 'サーバーでエラーが発生しました';
        }
      }
    } catch (_) {
      // ignore parse errors and fall back to status text
    }
    return 'サーバーでエラーが発生しました';
  }
}
