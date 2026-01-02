import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import 'blog_app_state.dart';
import 'screens/auth_screen.dart';
import 'screens/dashboard_screen.dart';

// ?
void main() {
  runApp(const BlogAssistantApp());
}

// ?
class BlogAssistantApp extends StatelessWidget {
  const BlogAssistantApp({super.key});

// ?
  @override
  Widget build(BuildContext context) {
    return ChangeNotifierProvider(
        // それぞれのプロパティを設定。あとでプロパティを使うために設定している？
      create: (_) => BlogAppState(),
      child: MaterialApp(
        title: 'Blog Assistant',
        debugShowCheckedModeBanner: false,
        theme: ThemeData(
          useMaterial3: true,
          colorSchemeSeed: Colors.indigo,
          scaffoldBackgroundColor: const Color(0xFFF8FAFC),
          inputDecorationTheme: InputDecorationTheme(
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(16),
              borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
            ),
            focusedBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(16),
              borderSide: const BorderSide(
                color: Color(0xFF6366F1),
                width: 2,
              ),
            ),
            filled: true,
            fillColor: Colors.white,
            contentPadding: const EdgeInsets.symmetric(
              horizontal: 16,
              vertical: 14,
            ),
          ),
          snackBarTheme: const SnackBarThemeData(
            behavior: SnackBarBehavior.floating,
          ),
        ),
        home: const _AuthGate(),
      ),
    );
  }
}

class _AuthGate extends StatelessWidget {
  const _AuthGate();

  @override
  Widget build(BuildContext context) {
    final state = context.watch<BlogAppState>();
    if (state.isAuthenticated) {
      return const DashboardScreen();
    }
    return const AuthScreen();
  }
}
