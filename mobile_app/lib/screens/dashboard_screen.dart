import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../blog_app_state.dart';
import '../models.dart';
import '../services/api_client.dart';
import '../widgets/article_card.dart';
import '../widgets/article_form.dart';
import '../widgets/category_manager.dart';

class DashboardScreen extends StatelessWidget {
  const DashboardScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final state = context.watch<BlogAppState>();
    final user = state.user!;
    return Scaffold(
      backgroundColor: const Color(0xFFF1F5F9),
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
          child: LayoutBuilder(
            builder: (context, constraints) {
              final isWide = constraints.maxWidth > 900;
              final content = isWide
                  ? Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        SizedBox(
                          width: constraints.maxWidth * 0.32,
                          child: SingleChildScrollView(
                            child: _FormPanel(state: state),
                          ),
                        ),
                        const SizedBox(width: 24),
                        Expanded(
                          child: _ArticleList(
                            state: state,
                            scrollable: true,
                          ),
                        )
                      ],
                    )
                  : SingleChildScrollView(
                      child: Column(
                        children: [
                          _FormPanel(state: state),
                          const SizedBox(height: 24),
                          _ArticleList(
                            state: state,
                            scrollable: false,
                          ),
                        ],
                      ),
                    );
              return Column(
                children: [
                  _Header(userName: user.name, articleCount: state.articles.length),
                  if (state.isLoading)
                    const Padding(
                      padding: EdgeInsets.only(top: 12),
                      child: LinearProgressIndicator(
                        minHeight: 4,
                        backgroundColor: Colors.transparent,
                      ),
                    ),
                  const SizedBox(height: 20),
                  Expanded(child: content),
                ],
              );
            },
          ),
        ),
      ),
    );
  }
}

class _Header extends StatelessWidget {
  const _Header({required this.userName, required this.articleCount});

  final String userName;
  final int articleCount;

  @override
  Widget build(BuildContext context) {
    final state = context.read<BlogAppState>();
    return Card(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(28)),
      elevation: 6,
      child: Padding(
        padding: const EdgeInsets.all(20),
        child: Row(
          children: [
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    '✍️ Blog Assistant',
                    style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                          fontWeight: FontWeight.w900,
                          color: const Color(0xFF4338CA),
                        ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    '$userNameさん、おかえりなさい！',
                    style: const TextStyle(color: Color(0xFF475569)),
                  ),
                ],
              ),
            ),
            Column(
              crossAxisAlignment: CrossAxisAlignment.end,
              children: [
                Text(
                  '記事数',
                  style: Theme.of(context)
                      .textTheme
                      .labelLarge
                      ?.copyWith(color: Colors.blueGrey),
                ),
                Text(
                  '$articleCount件',
                  style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                        color: Colors.indigo,
                        fontWeight: FontWeight.bold,
                      ),
                ),
                TextButton.icon(
                  onPressed: () => state.logout(),
                  icon: const Icon(Icons.logout),
                  label: const Text('ログアウト'),
                ),
              ],
            )
          ],
        ),
      ),
    );
  }
}

class _FormPanel extends StatelessWidget {
  const _FormPanel({required this.state});

  final BlogAppState state;

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        ArticleForm(
          categories: state.categories,
          onSubmit: (draft) async {
            try {
              await state.createArticle(draft);
              if (context.mounted) {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(content: Text('記事を作成しました')),
                );
              }
            } on ApiException catch (error) {
              if (!context.mounted) return;
              ScaffoldMessenger.of(context).showSnackBar(
                SnackBar(content: Text(error.message)),
              );
            }
          },
        ),
        const SizedBox(height: 16),
        CategoryManager(
          categories: state.categories,
          onCreate: (name) async {
            try {
              await state.addCategory(name);
            } on ApiException catch (error) {
              if (!context.mounted) return;
              ScaffoldMessenger.of(context).showSnackBar(
                SnackBar(content: Text(error.message)),
              );
            }
          },
          onDelete: (categoryId) async {
            try {
              await state.deleteCategory(categoryId);
            } on ApiException catch (error) {
              if (!context.mounted) return;
              ScaffoldMessenger.of(context).showSnackBar(
                SnackBar(content: Text(error.message)),
              );
            }
          },
        ),
      ],
    );
  }
}

class _ArticleList extends StatelessWidget {
  const _ArticleList({
    required this.state,
    required this.scrollable,
  });

  final BlogAppState state;
  final bool scrollable;

  @override
  Widget build(BuildContext context) {
    final articles = state.articles;
    if (articles.isEmpty) {
      return Padding(
        padding: const EdgeInsets.only(top: 40),
        child: Column(
          children: const [
            Icon(Icons.menu_book_outlined, size: 64, color: Colors.blueGrey),
            SizedBox(height: 12),
            Text('まだ記事がありません'),
            Text('左のフォームから作成しましょう'),
          ],
        ),
      );
    }
    final cards = List<Widget>.generate(articles.length, (index) {
      final article = articles[index];
      final category = state.categoryById(article.categoryId);
      return ArticleCard(
        article: article,
        category: category,
        onEdit: () => _openEditor(context, article),
        onDelete: () => _confirmDelete(context, article.id),
      );
    });

    if (scrollable) {
      return Scrollbar(
        child: ListView.separated(
          itemCount: cards.length,
          separatorBuilder: (context, _) => const SizedBox(height: 16),
          itemBuilder: (context, index) => cards[index],
        ),
      );
    }

    return Column(
      children: List<Widget>.generate(cards.length * 2 - 1, (i) {
        if (i.isOdd) return const SizedBox(height: 16);
        return cards[i ~/ 2];
      }),
    );
  }

  void _openEditor(BuildContext context, Article article) {
    final state = context.read<BlogAppState>();
    showModalBottomSheet<void>(
      context: context,
      isScrollControlled: true,
      builder: (ctx) {
        return Padding(
          padding: EdgeInsets.only(
            bottom: MediaQuery.of(ctx).viewInsets.bottom,
            left: 16,
            right: 16,
            top: 24,
          ),
          child: SingleChildScrollView(
            child: ArticleForm(
              initialDraft: article.toDraft(),
              categories: state.categories,
              submitLabel: '更新する',
              onSubmit: (draft) async {
                try {
                  await state.updateArticle(article.id, draft);
                  if (context.mounted) {
                    Navigator.of(ctx).pop();
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(content: Text('記事を更新しました')),
                    );
                  }
                } on ApiException catch (error) {
                  if (!context.mounted) return;
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(content: Text(error.message)),
                  );
                }
              },
            ),
          ),
        );
      },
    );
  }

  void _confirmDelete(BuildContext context, int articleId) {
    final state = context.read<BlogAppState>();
    showDialog<void>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('削除しますか？'),
        content: const Text('この記事を完全に削除します。よろしいですか？'),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(ctx).pop(),
            child: const Text('キャンセル'),
          ),
          FilledButton(
            onPressed: () async {
              try {
                await state.deleteArticle(articleId);
                if (context.mounted) {
                  Navigator.of(ctx).pop();
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(content: Text('記事を削除しました')),
                  );
                }
              } on ApiException catch (error) {
                if (!context.mounted) return;
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(content: Text(error.message)),
                );
              }
            },
            style: FilledButton.styleFrom(backgroundColor: Colors.redAccent),
            child: const Text('削除する'),
          ),
        ],
      ),
    );
  }
}
