import 'package:flutter/material.dart';

import '../models.dart';

class ArticleForm extends StatefulWidget {
  const ArticleForm({
    super.key,
    this.initialDraft,
    required this.categories,
    required this.onSubmit,
    this.submitLabel = '記事を作成',
  });

  final ArticleDraft? initialDraft;
  final List<Category> categories;
  final Future<void> Function(ArticleDraft draft) onSubmit;
  final String submitLabel;

  @override
  State<ArticleForm> createState() => _ArticleFormState();
}

class _ArticleFormState extends State<ArticleForm> {
  final _formKey = GlobalKey<FormState>();
  late ArticleDraft _draft;
  bool _isSubmitting = false;
  late final TextEditingController _titleController;
  late final TextEditingController _tableOfContentsController;
  late final TextEditingController _contentController;
  late final TextEditingController _notesController;
  late final TextEditingController _seoTitleController;
  late final TextEditingController _seoDescriptionController;

  @override
  void initState() {
    super.initState();
    _draft = widget.initialDraft ?? ArticleDraft();
    _titleController = TextEditingController(text: _draft.title);
    _tableOfContentsController =
        TextEditingController(text: _draft.tableOfContents);
    _contentController = TextEditingController(text: _draft.content);
    _notesController = TextEditingController(text: _draft.notes);
    _seoTitleController = TextEditingController(text: _draft.seoTitle);
    _seoDescriptionController =
        TextEditingController(text: _draft.seoDescription);
  }

  @override
  void dispose() {
    _titleController.dispose();
    _tableOfContentsController.dispose();
    _contentController.dispose();
    _notesController.dispose();
    _seoTitleController.dispose();
    _seoDescriptionController.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate() || _isSubmitting) return;
    setState(() => _isSubmitting = true);
    final categoryId = _selectedCategoryId;
    final draft = ArticleDraft(
      id: _draft.id,
      title: _titleController.text,
      content: _contentController.text,
      status: _draft.status,
      categoryId: categoryId,
      tableOfContents: _tableOfContentsController.text,
      notes: _notesController.text,
      seoTitle: _seoTitleController.text,
      seoDescription: _seoDescriptionController.text,
    );
    try {
      await widget.onSubmit(draft);
      if (widget.initialDraft == null) {
        setState(() {
          _draft = ArticleDraft();
          _titleController.clear();
          _tableOfContentsController.clear();
          _contentController.clear();
          _notesController.clear();
          _seoTitleController.clear();
          _seoDescriptionController.clear();
        });
      }
    } finally {
      if (mounted) {
        setState(() => _isSubmitting = false);
      }
    }
  }

  Future<void> _openFullScreenEditor({
    required String title,
    required TextEditingController controller,
  }) async {
    FocusScope.of(context).unfocus();
    await showModalBottomSheet<void>(
      context: context,
      isScrollControlled: true,
      useSafeArea: true,
      builder: (ctx) => _FullScreenTextEditor(
        title: title,
        controller: controller,
      ),
    );
    if (mounted) {
      setState(() {});
    }
  }

  Widget _buildModalField({
    required String label,
    required TextEditingController controller,
  }) {
    return _LabeledField(
      label: label,
      child: GestureDetector(
        behavior: HitTestBehavior.opaque,
        onTap: () => _openFullScreenEditor(
          title: label,
          controller: controller,
        ),
        child: InputDecorator(
          decoration: const InputDecoration(border: OutlineInputBorder()),
          child: Text(
            controller.text.isEmpty ? '未入力' : controller.text,
            maxLines: 3,
            overflow: TextOverflow.ellipsis,
            style: TextStyle(
              color: controller.text.isEmpty
                  ? Colors.blueGrey
                  : const Color(0xFF0F172A),
            ),
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final selectedCategoryId = _selectedCategoryId;
    return Form(
      key: _formKey,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          _Section(
            title: '📝 新規記事',
            child: Column(
              children: [
                _LabeledField(
                  label: 'タイトル *',
                  child: TextFormField(
                    controller: _titleController,
                    decoration:
                        const InputDecoration(border: OutlineInputBorder()),
                    validator: (value) {
                      if (value == null || value.trim().isEmpty) {
                        return 'タイトルを入力してください';
                      }
                      return null;
                    },
                    onChanged: (_) => setState(() {}),
                  ),
                ),
                _LabeledField(
                  label: 'ステータス',
                  child: DropdownButtonFormField<ArticleStatus>(
                    key: ValueKey(_draft.status),
                    initialValue: _draft.status,
                    decoration: const InputDecoration(border: OutlineInputBorder()),
                    items: ArticleStatus.values
                        .map(
                          (status) => DropdownMenuItem<ArticleStatus>(
                            value: status,
                            child: Text(status.label),
                          ),
                        )
                        .toList(),
                    onChanged: (value) {
                      if (value == null) return;
                      setState(() {
                        _draft = _draft.copyWith(status: value);
                      });
                    },
                  ),
                ),
                _LabeledField(
                  label: 'カテゴリ',
                  child: DropdownButtonFormField<int?>(
                    key: ValueKey(selectedCategoryId),
                    initialValue: selectedCategoryId,
                    decoration:
                        const InputDecoration(border: OutlineInputBorder()),
                    items: [
                      const DropdownMenuItem<int?>(
                        value: null,
                        child: Text('未選択'),
                      ),
                      ...widget.categories.map(
                        (cat) => DropdownMenuItem<int?>(
                          value: cat.id,
                          child: Text(cat.name),
                        ),
                      ),
                    ],
                    onChanged: (value) {
                      setState(() {
                        _draft = _draft.copyWith(categoryId: value);
                      });
                    },
                  ),
                ),
                _buildModalField(
                  label: '目次',
                  controller: _tableOfContentsController,
                ),
                _buildModalField(
                  label: '本文',
                  controller: _contentController,
                ),
                _buildModalField(
                  label: 'メモ',
                  controller: _notesController,
                ),
              ],
            ),
          ),
          const SizedBox(height: 16),
          _Section(
            title: '🔍 SEO',
            child: Column(
              children: [
                _LabeledField(
                  label: 'SEO タイトル',
                  child: TextFormField(
                    controller: _seoTitleController,
                    decoration:
                        const InputDecoration(border: OutlineInputBorder()),
                  ),
                ),
                _LabeledField(
                  label: 'SEO ディスクリプション',
                  child: TextFormField(
                    controller: _seoDescriptionController,
                    decoration:
                        const InputDecoration(border: OutlineInputBorder()),
                    maxLines: 3,
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 20),
          SizedBox(
            width: double.infinity,
            child: ElevatedButton.icon(
              onPressed: _isSubmitting ? null : _submit,
              icon: _isSubmitting
                  ? const SizedBox(
                      width: 18,
                      height: 18,
                      child: CircularProgressIndicator(
                        strokeWidth: 2,
                        color: Colors.white,
                      ),
                    )
                  : const Icon(Icons.send),
              label: Text(
                widget.submitLabel,
                style: theme.textTheme.titleMedium?.copyWith(
                  color: Colors.white,
                ),
              ),
              style: ElevatedButton.styleFrom(
                padding: const EdgeInsets.symmetric(vertical: 16),
                backgroundColor: Colors.indigo,
              ),
            ),
          ),
        ],
      ),
    );
  }

  int? get _selectedCategoryId {
    final id = _draft.categoryId;
    if (id == null) return null;
    final exists = widget.categories.any((cat) => cat.id == id);
    return exists ? id : null;
  }
}

class _Section extends StatelessWidget {
  const _Section({required this.title, required this.child});

  final String title;
  final Widget child;

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: const Color(0xFFE2E8F0)),
        boxShadow: [
          BoxShadow(
            blurRadius: 24,
            color: _withAlpha(Colors.indigo, 0.05),
            offset: const Offset(0, 8),
          )
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            title,
            style: const TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.w900,
              color: Colors.indigo,
            ),
          ),
          const SizedBox(height: 16),
          child,
        ],
      ),
    );
  }
}

class _LabeledField extends StatelessWidget {
  const _LabeledField({
    required this.label,
    required this.child,
  });

  final String label;
  final Widget child;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            label,
            style: const TextStyle(
              fontWeight: FontWeight.w600,
              color: Colors.blueGrey,
            ),
          ),
          const SizedBox(height: 8),
          child,
        ],
      ),
    );
  }
}

class _FullScreenTextEditor extends StatelessWidget {
  const _FullScreenTextEditor({
    required this.title,
    required this.controller,
  });

  final String title;
  final TextEditingController controller;

  @override
  Widget build(BuildContext context) {
    final mediaQuery = MediaQuery.of(context);
    return Material(
      color: Colors.white,
      child: SizedBox(
        height: mediaQuery.size.height * 0.95,
        child: Padding(
          padding: EdgeInsets.only(
            left: 24,
            right: 24,
            top: 24,
            bottom: mediaQuery.viewInsets.bottom + 16,
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Expanded(
                    child: Text(
                      title,
                      style: const TextStyle(
                        fontSize: 20,
                        fontWeight: FontWeight.w800,
                        color: Colors.indigo,
                      ),
                    ),
                  ),
                  IconButton(
                    onPressed: () => Navigator.of(context).pop(),
                    icon: const Icon(Icons.close),
                  ),
                ],
              ),
              const SizedBox(height: 16),
              Expanded(
                child: TextField(
                  controller: controller,
                  autofocus: true,
                  expands: true,
                  maxLines: null,
                  minLines: null,
                  textAlignVertical: TextAlignVertical.top,
                  textAlign: TextAlign.start,
                  decoration: const InputDecoration(
                    border: OutlineInputBorder(),
                    alignLabelWithHint: true,
                  ),
                ),
              ),
              const SizedBox(height: 12),
              Align(
                alignment: Alignment.centerRight,
                child: ValueListenableBuilder<TextEditingValue>(
                  valueListenable: controller,
                  builder: (context, value, _) {
                    final count = value.text.characters.length;
                    return Text(
                      '$count 文字',
                      style: const TextStyle(
                        fontWeight: FontWeight.w600,
                        color: Colors.blueGrey,
                      ),
                    );
                  },
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

Color _withAlpha(Color color, double opacity) {
  final alpha = (opacity * 255).clamp(0, 255).round();
  return color.withAlpha(alpha);
}
