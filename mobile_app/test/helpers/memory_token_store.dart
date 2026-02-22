import 'package:mobile_app/services/token_store.dart';

class MemoryTokenStore implements TokenStore {
  String? _token;

  @override
  Future<void> delete() async {
    _token = null;
  }

  @override
  Future<String?> read() async {
    return _token;
  }

  @override
  Future<void> write(String token) async {
    _token = token;
  }
}
