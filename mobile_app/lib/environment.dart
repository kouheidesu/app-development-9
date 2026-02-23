enum AppEnvironment {
  development,
  production,
}

class EnvironmentConfig {
  EnvironmentConfig._();

  static const String _envFlag =
      String.fromEnvironment('APP_ENV', defaultValue: 'development');

  static const String _manualBase =
      String.fromEnvironment('API_BASE_URL', defaultValue: '');

  static const String _developmentBase =
      'https://app-development-9-production.up.railway.app/api';
  static const String _productionBase =
      'https://app-development-9-copy-production.up.railway.app/api';

  static AppEnvironment get environment {
    switch (_envFlag.toLowerCase()) {
      case 'production':
      case 'prod':
        return AppEnvironment.production;
      default:
        return AppEnvironment.development;
    }
  }

  static String get apiBaseUrl {
    if (_manualBase.isNotEmpty) {
      return _manualBase;
    }
    return environment == AppEnvironment.production
        ? _productionBase
        : _developmentBase;
  }
}
