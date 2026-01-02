import 'package:flutter/material.dart';

import '../models.dart';

class ArticleForm extends StatefulWidget {
  const ArticleForm({
    super.key,
    this.initialDraft,
    required this.categories,
    required this.tags,
    required this.onSubmit,
    this.submitLabel = '記事を作成',
  });

  final ArticleDraft? initialDraft;
  final List<Category> categories;
  final List<Tag> tags;
  final ValueChanged<ArticleDraft> onSubmit;
  final String submitLabel;

  @override
  State<ArticleForm> createState() => _ArticleFormState();
}

class _ArticleFormState extends State<ArticleForm> {
  final _formKey = GlobalKey<FormState>();
  late ArticleDraft _draft;
  late final TextEditingController _titleController;
  late final TextEditingController _tableOfContentsController;
  late final TextEditingController _contentController;
  late final TextEditingController _notesController;
  late final TextEditingController _seoTitleController;
  late final TextEditingController _seoDescriptionController;
  late final TextEditingController _featuredImageController;

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
    _featuredImageController = TextEditingController(text: _draft.featuredImage);
  }

  @override
  void dispose() {
    _titleController.dispose();
    _tableOfContentsController.dispose();
    _contentController.dispose();
    _notesController.dispose();
    _seoTitleController.dispose();
    _seoDescriptionController.dispose();
    _featuredImageController.dispose();
    super.dispose();
  }

  void _submit() {
    if (!_formKey.currentState!.validate()) return;
    final draft = ArticleDraft(
      id: _draft.id,
      title: _titleController.text,
      content: _contentController.text,
      status: _draft.status,
      categoryId: _draft.categoryId,
      tableOfContents: _tableOfContentsController.text,
      notes: _notesController.text,
      seoTitle: _seoTitleController.text,
      seoDescription: _seoDescriptionController.text,
      featuredImage: _featuredImageController.text,
      tagIds: List<String>.from(_draft.tagIds),
    );
    widget.onSubmit(draft);
    if (widget.initialDraft == null) {
      setState(() {
        _draft = ArticleDraft();
        _titleController.clear();
        _tableOfContentsController.clear();
        _contentController.clear();
        _notesController.clear();
        _seoTitleController.clear();
        _seoDescriptionController.clear();
        _featuredImageController.clear();
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
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
                    value: _draft.status,
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
                  child: DropdownButtonFormField<String?>(
                    value: _draft.categoryId?.isEmpty ?? true
                        ? null
                        : _draft.categoryId,
                    decoration:
                        const InputDecoration(border: OutlineInputBorder()),
                    items: [
                      const DropdownMenuItem<String?>(
                        value: null,
                        child: Text('未選択'),
                      ),
                      ...widget.categories.map(
                        (cat) => DropdownMenuItem<String?>(
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
                _LabeledField(
                  label: 'タグ',
                  child: Wrap(
                    spacing: 8,
                    runSpacing: -8,
                    children: widget.tags
                        .map(
                          (tag) => FilterChip(
                            label: Text('#${tag.name}'),
                            selected: _draft.tagIds.contains(tag.id),
                            onSelected: (selected) {
                              setState(() {
                                if (selected) {
                                  _draft.tagIds.add(tag.id);
                                } else {
                                  _draft.tagIds.remove(tag.id);
                                }
                              });
                            },
                          ),
                        )
                        .toList(),
                  ),
                ),
                _LabeledField(
                  label: '目次',
                  child: TextFormField(
                    controller: _tableOfContentsController,
                    decoration:
                        const InputDecoration(border: OutlineInputBorder()),
                    maxLines: 3,
                  ),
                ),
                _LabeledField(
                  label: '本文',
                  child: TextFormField(
                    controller: _contentController,
                    maxLines: 5,
                    decoration: InputDecoration(
                      border: const OutlineInputBorder(),
                      helperText:
                          '${_contentController.text.characters.length} 文字',
                    ),
                    onChanged: (_) => setState(() {}),
                  ),
                ),
                _LabeledField(
                  label: 'メモ',
                  child: TextFormField(
                    controller: _notesController,
                    decoration:
                        const InputDecoration(border: OutlineInputBorder()),
                    maxLines: 3,
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 16),
          _Section(
            title: '🔍 SEO / 画像',
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
                _LabeledField(
                  label: 'アイキャッチ画像 URL',
                  child: TextFormField(
                    controller: _featuredImageController,
                    decoration:
                        const InputDecoration(border: OutlineInputBorder()),
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 20),
          SizedBox(
            width: double.infinity,
            child: ElevatedButton.icon(
              onPressed: _submit,
              icon: const Icon(Icons.send),
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
            color: Colors.indigo.withOpacity(0.05),
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
