import 'package:flutter_test/flutter_test.dart';
import 'package:mobile_app/main.dart';

void main() {
  testWidgets('認証画面が表示される', (tester) async {
    await tester.pumpWidget(const BlogAssistantApp());
    expect(find.textContaining('Blog Assistant'), findsWidgets);
    expect(find.text('ログイン'), findsOneWidget);
    expect(find.text('アカウントをお持ちでない方はこちら'), findsOneWidget);
  });
}
