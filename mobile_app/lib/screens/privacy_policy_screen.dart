import 'package:flutter/material.dart';

class PrivacyPolicyScreen extends StatelessWidget {
  const PrivacyPolicyScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('プライバシーポリシー'),
      ),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: const [
          _Section(
            title: '1. 収集する情報',
            body:
                'メールアドレス、表示名、作成した記事・カテゴリの内容をサービスの提供目的で収集します。',
          ),
          _Section(
            title: '2. 利用目的',
            body:
                '認証・記事生成・サポート対応のためにのみ利用し、第三者に販売することはありません。',
          ),
          _Section(
            title: '3. 保存期間',
            body:
                'アカウント削除から30日以内に関連データを完全に削除します。トークンは端末上で暗号化して保存します。',
          ),
          _Section(
            title: '4. お問い合わせ',
            body:
                'privacy@example.com までお問い合わせください。法令に基づき適切に対応します。',
          ),
        ],
      ),
    );
  }
}

class _Section extends StatelessWidget {
  const _Section({required this.title, required this.body});

  final String title;
  final String body;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            title,
            style: Theme.of(context)
                .textTheme
                .titleMedium
                ?.copyWith(fontWeight: FontWeight.bold),
          ),
          const SizedBox(height: 8),
          Text(
            body,
            style: Theme.of(context).textTheme.bodyMedium,
          ),
        ],
      ),
    );
  }
}
