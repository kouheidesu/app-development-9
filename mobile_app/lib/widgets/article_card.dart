import 'package:flutter/material.dart';

import '../models.dart';

class ArticleCard extends StatelessWidget {
  const ArticleCard({
    super.key,
    required this.article,
    required this.category,
    required this.onEdit,
    required this.onDelete,
  });

  final Article article;
  final Category? category;
  final VoidCallback onEdit;
  final VoidCallback onDelete;

  @override
  Widget build(BuildContext context) {
    final status = article.status;
    final theme = Theme.of(context);
    return Card(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
      margin: EdgeInsets.zero,
      elevation: 3,
      child: Padding(
        padding: const EdgeInsets.all(18),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        article.title,
                        style: theme.textTheme.titleMedium?.copyWith(
                          fontWeight: FontWeight.bold,
                          color: const Color(0xFF0F172A),
                        ),
                      ),
                      const SizedBox(height: 8),
                      Wrap(
                        crossAxisAlignment: WrapCrossAlignment.center,
                        spacing: 8,
                        runSpacing: 4,
                        children: [
                          if (category != null)
                            Chip(
                              label: Text(category!.name),
                              visualDensity: VisualDensity.compact,
                              backgroundColor:
                                  _withAlpha(category!.color, 0.15),
                              labelStyle: TextStyle(
                                color: category!.color,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          Container(
                            padding: const EdgeInsets.symmetric(
                              horizontal: 8,
                              vertical: 4,
                            ),
                            decoration: BoxDecoration(
                              color: status.background,
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: Text(
                              status.label,
                              style: TextStyle(
                                color: status.color,
                                fontSize: 12,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          ),
                          if (article.wordCount > 0)
                            Text(
                              '${article.wordCount}文字',
                              style: const TextStyle(
                                fontSize: 12,
                                color: Colors.blueGrey,
                              ),
                            ),
                          Text(
                            MaterialLocalizations.of(context)
                                .formatShortDate(article.createdAt),
                            style: const TextStyle(
                              fontSize: 12,
                              color: Colors.blueGrey,
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
                Column(
                  children: [
                    IconButton(
                      icon: const Icon(Icons.edit_outlined),
                      color: Colors.indigo,
                      onPressed: onEdit,
                    ),
                    IconButton(
                      icon: const Icon(Icons.delete_outline),
                      color: Colors.redAccent,
                      onPressed: onDelete,
                    ),
                  ],
                ),
              ],
            ),
            if (article.tableOfContents.isNotEmpty) ...[
              const SizedBox(height: 12),
              _InfoTile(
                label: '📑 目次',
                value: article.tableOfContents,
                background: const Color(0xFFF8FAFC),
              ),
            ],
            if (article.content.isNotEmpty) ...[
              const SizedBox(height: 12),
              Text(
                article.content,
                style: const TextStyle(color: Color(0xFF475569)),
                maxLines: 3,
                overflow: TextOverflow.ellipsis,
              ),
            ],
            if (article.notes.isNotEmpty) ...[
              const SizedBox(height: 12),
              _InfoTile(
                label: '💡 メモ',
                value: article.notes,
                background: const Color(0xFFFFF7ED),
              ),
            ],
          ],
        ),
      ),
    );
  }
}

Color _withAlpha(Color color, double opacity) {
  final alpha = (opacity * 255).clamp(0, 255).round();
  return color.withAlpha(alpha);
}

class _InfoTile extends StatelessWidget {
  const _InfoTile({
    required this.label,
    required this.value,
    required this.background,
  });

  final String label;
  final String value;
  final Color background;

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: background,
        borderRadius: BorderRadius.circular(16),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            label,
            style: const TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.bold,
              color: Color(0xFF475569),
            ),
          ),
          const SizedBox(height: 6),
          Text(
            value,
            style: const TextStyle(color: Color(0xFF475569)),
          ),
        ],
      ),
    );
  }
}
