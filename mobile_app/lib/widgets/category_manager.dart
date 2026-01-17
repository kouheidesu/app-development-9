import 'package:flutter/material.dart';

import '../models.dart';

class CategoryManager extends StatefulWidget {
  const CategoryManager({
    super.key,
    required this.categories,
    required this.onCreate,
    required this.onDelete,
  });

  final List<Category> categories;
  final Future<void> Function(String name) onCreate;
  final Future<void> Function(int id) onDelete;

  @override
  State<CategoryManager> createState() => _CategoryManagerState();
}

class _CategoryManagerState extends State<CategoryManager> {
  final TextEditingController _controller = TextEditingController();
  bool _isSubmitting = false;
  int? _deletingId;

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  Future<void> _handleSubmit() async {
    final name = _controller.text.trim();
    if (name.isEmpty || _isSubmitting) return;
    setState(() => _isSubmitting = true);
    try {
      await widget.onCreate(name);
      _controller.clear();
    } finally {
      if (mounted) {
        setState(() => _isSubmitting = false);
      }
    }
  }

  Future<void> _handleDelete(int categoryId) async {
    if (_deletingId != null) return;
    setState(() => _deletingId = categoryId);
    try {
      await widget.onDelete(categoryId);
    } finally {
      if (mounted) {
        setState(() => _deletingId = null);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: const Color(0xFFE2E8F0)),
        boxShadow: [
          BoxShadow(
            blurRadius: 20,
            color: _withAlpha(Colors.indigo, 0.05),
            offset: const Offset(0, 6),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            '📂 カテゴリ管理',
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.w800,
              color: Colors.indigo,
            ),
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              Expanded(
                child: TextField(
                  controller: _controller,
                  decoration: const InputDecoration(
                    hintText: 'カテゴリ名を入力',
                    border: OutlineInputBorder(),
                  ),
                  onSubmitted: (_) => _handleSubmit(),
                ),
              ),
              const SizedBox(width: 12),
              FilledButton(
                onPressed: _isSubmitting ? null : _handleSubmit,
                style: FilledButton.styleFrom(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 20,
                    vertical: 16,
                  ),
                  backgroundColor: Colors.indigo,
                ),
                child: _isSubmitting
                    ? const SizedBox(
                        width: 18,
                        height: 18,
                        child: CircularProgressIndicator(
                          strokeWidth: 2,
                          color: Colors.white,
                        ),
                      )
                    : const Text('追加'),
              ),
            ],
          ),
          const SizedBox(height: 16),
          if (widget.categories.isEmpty)
            const Text(
              'カテゴリがありません。作成してみましょう。',
              style: TextStyle(color: Colors.blueGrey),
            )
          else
            Wrap(
              spacing: 8,
              runSpacing: -8,
              children: widget.categories
                  .map(
                    (category) => InputChip(
                      label: Text(category.name),
                      labelStyle: TextStyle(
                        color: category.color,
                        fontWeight: FontWeight.w600,
                      ),
                      backgroundColor: _withAlpha(category.color, 0.12),
                      onDeleted: _deletingId == category.id
                          ? null
                          : () => _handleDelete(category.id),
                    ),
                  )
                  .toList(),
            ),
        ],
      ),
    );
  }
}

Color _withAlpha(Color color, double opacity) {
  final alpha = (opacity * 255).clamp(0, 255).round();
  return color.withAlpha(alpha);
}
