enum AppEnvironment {
  development,
  production,
}

class EnvironmentConfig {
  EnvironmentConfig._();

  static const bool _isReleaseBuild =
      bool.fromEnvironment('dart.vm.product', defaultValue: false);

  static const String _envFlag =
      String.fromEnvironment('APP_ENV', defaultValue: 'auto');

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
      case 'development':
      case 'dev':
        return AppEnvironment.development;
      default:
        return _isReleaseBuild
            ? AppEnvironment.production
            : AppEnvironment.development;
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
