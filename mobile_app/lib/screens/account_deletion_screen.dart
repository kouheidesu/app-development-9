import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../blog_app_state.dart';
import '../services/api_client.dart';

class AccountDeletionScreen extends StatefulWidget {
  const AccountDeletionScreen({super.key});

  @override
  State<AccountDeletionScreen> createState() => _AccountDeletionScreenState();
}

class _AccountDeletionScreenState extends State<AccountDeletionScreen> {
  bool _ackArticles = false;
  bool _ackIrreversible = false;
  bool _ackSupport = false;
  final TextEditingController _phraseController = TextEditingController();
  bool _isDeleting = false;

  @override
  void dispose() {
    _phraseController.dispose();
    super.dispose();
  }

  Future<void> _handleDelete() async {
    setState(() => _isDeleting = true);
    final state = context.read<BlogAppState>();
    try {
      await state.deleteAccount();
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('アカウントを削除しました')),
      );
      Navigator.of(context).pop();
    } on ApiException catch (error) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(error.message)),
      );
    } finally {
      if (mounted) {
        setState(() => _isDeleting = false);
      }
    }
  }

  bool get _isPhraseValid =>
      _phraseController.text.trim() == '削除します' ||
      _phraseController.text.trim().toLowerCase() == 'delete';

  bool get _canDelete =>
      _ackArticles && _ackIrreversible && _ackSupport && _isPhraseValid;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return Scaffold(
      appBar: AppBar(
        title: const Text('アカウント削除手続き'),
      ),
      body: ListView(
        padding: const EdgeInsets.all(20),
        children: [
          Card(
            elevation: 2,
            shape:
                RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
            child: Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: const [
                  Text(
                    '⚠️ 重要な注意事項',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.w800,
                      color: Colors.redAccent,
                    ),
                  ),
                  SizedBox(height: 12),
                  Text(
                    'アカウントを削除すると、作成した記事・カテゴリを含むすべてのデータが復元できなくなります。'
                    'エクスポート等が必要な場合は事前に完了させてください。',
                  ),
                ],
              ),
            ),
          ),
          const SizedBox(height: 20),
          CheckboxListTile(
            value: _ackArticles,
            onChanged: (value) => setState(() => _ackArticles = value ?? false),
            title: const Text('作成した記事・カテゴリがすべて削除されることを理解しました'),
            controlAffinity: ListTileControlAffinity.leading,
          ),
          CheckboxListTile(
            value: _ackIrreversible,
            onChanged: (value) =>
                setState(() => _ackIrreversible = value ?? false),
            title: const Text('削除は取り消せず、再度利用するには新規登録が必要であると理解しました'),
            controlAffinity: ListTileControlAffinity.leading,
          ),
          CheckboxListTile(
            value: _ackSupport,
            onChanged: (value) => setState(() => _ackSupport = value ?? false),
            title: const Text('困りごとがあれば gyarmex@gmail.com へ相談できることを確認しました'),
            controlAffinity: ListTileControlAffinity.leading,
          ),
          const SizedBox(height: 16),
          Text(
            '確認のため「削除します」と入力してください',
            style: theme.textTheme.titleSmall,
          ),
          const SizedBox(height: 8),
          TextField(
            controller: _phraseController,
            onChanged: (_) => setState(() {}),
            decoration: InputDecoration(
              hintText: '削除します',
              border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
            ),
          ),
          const SizedBox(height: 24),
          FilledButton.icon(
            onPressed: _canDelete && !_isDeleting ? _handleDelete : null,
            style: FilledButton.styleFrom(
              backgroundColor: Colors.redAccent,
              padding: const EdgeInsets.symmetric(vertical: 16),
            ),
            icon: _isDeleting
                ? const SizedBox(
                    height: 20,
                    width: 20,
                    child: CircularProgressIndicator(
                      strokeWidth: 2,
                      color: Colors.white,
                    ),
                  )
                : const Icon(Icons.delete_forever),
            label: Text(
              _isDeleting ? '削除中...' : 'すべてのデータを削除する',
              style: const TextStyle(fontWeight: FontWeight.bold),
            ),
          ),
        ],
      ),
    );
  }
}
