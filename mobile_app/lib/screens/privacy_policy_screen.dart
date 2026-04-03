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
            title: '提供者',
            body:
                '「ブログ記事作成アシスト」アプリは gyarmex（連絡先: gyarmex@gmail.com）が提供します。本ポリシーはモバイルアプリと Railway 上の API の双方に適用されます。',
          ),
          _Section(
            title: '1. 収集する情報',
            body:
                'アカウント登録時に氏名・メールアドレス・パスワード（サーバー側でハッシュ化保存）を取得し、ログイン用トークンは端末の flutter_secure_storage に暗号化格納します。ユーザーが作成する記事・カテゴリ・メモ・SEO関連フィールドなどのコンテンツデータ、並びにサーバーログとして IP アドレス・User-Agent・アクセス日時・エラー内容などを自動取得します。',
          ),
          _Section(
            title: '2. 利用目的',
            body:
                'アカウント認証、記事・カテゴリ管理機能の提供、サポート対応、セキュリティ監査と不正利用防止、法令遵守のためにのみ利用し、第三者への販売は行いません。',
          ),
          _Section(
            title: '3. 情報の共有',
            body:
                '上記目的の達成に必要な範囲で、インフラ/メール等の委託先（例: Railway、メール送信サービス）に提供する場合があります。法令に基づく開示要請がある場合を除き、ユーザーの同意なく第三者へ提供しません。',
          ),
          _Section(
            title: '4. 保存期間',
            body:
                'アカウントが有効な間はデータを保持し、ユーザーがアカウント削除を行った場合は関連データを即時削除、バックアップ等も30日以内に消去します。端末内トークンはログアウトまたは削除時にクリアします。',
          ),
          _Section(
            title: '5. ユーザーの権利',
            body:
                '開示・訂正・利用停止・削除等の要望、ならびに同意撤回は gyarmex@gmail.com までご連絡ください。本人確認のうえ、適切な期間内に対応します。',
          ),
          _Section(
            title: '6. セキュリティ対策',
            body:
                '通信は HTTPS で暗号化し、API トークンは Bearer 認証と端末の安全なストレージに保存、パスワードはハッシュ化するなど適切な措置を講じています。',
          ),
          _Section(
            title: '7. 未成年の利用',
            body:
                '本サービスは13歳未満を対象としておらず、該当する個人情報を意図的に取得しません。誤って取得した場合は速やかに削除しますのでご連絡ください。',
          ),
          _Section(
            title: '8. Cookie / トラッキング',
            body:
                'アプリ自体は Cookie を利用せず、サーバーは標準的なアクセスログのみ収集します。広告目的のトラッキング SDK は組み込んでいません。',
          ),
          _Section(
            title: '9. 改定',
            body:
                '運用変更や法令改正に応じて本ポリシーを更新する場合があります。重要な変更はアプリ内告知またはメールでお知らせし、更新後のサービス利用をもって同意したものとみなします。',
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
