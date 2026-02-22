import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:mobile_app/main.dart';
import 'package:shared_preferences/shared_preferences.dart';

void main() {
  TestWidgetsFlutterBinding.ensureInitialized();

  setUp(() {
    SharedPreferences.setMockInitialValues({});
    FlutterSecureStorage.setMockInitialValues({});
  });

  testWidgets('認証画面が表示される', (tester) async {
    await tester.pumpWidget(const BlogAssistantApp());
    await tester.pump();
    expect(find.textContaining('Blog Assistant'), findsWidgets);
    expect(find.text('ログイン'), findsOneWidget);
    expect(find.text('アカウントをお持ちでない方はこちら'), findsOneWidget);
  });
}
