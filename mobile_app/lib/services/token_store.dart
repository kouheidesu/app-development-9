import 'package:flutter_secure_storage/flutter_secure_storage.dart';

abstract class TokenStore {
  Future<String?> read();
  Future<void> write(String token);
  Future<void> delete();
}

class SecureTokenStore implements TokenStore {
  SecureTokenStore({FlutterSecureStorage? storage})
      : _storage = storage ?? const FlutterSecureStorage();

  final FlutterSecureStorage _storage;
  static const String _tokenKey = 'api_token';

  @override
  Future<void> delete() {
    return _storage.delete(key: _tokenKey);
  }

  @override
  Future<String?> read() {
    return _storage.read(key: _tokenKey);
  }

  @override
  Future<void> write(String token) {
    return _storage.write(key: _tokenKey, value: token);
  }
}
