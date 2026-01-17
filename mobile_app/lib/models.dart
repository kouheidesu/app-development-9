import 'package:flutter/material.dart';

enum ArticleStatus {
  draft(
    label: '📋 下書き',
    color: Color(0xFF94A3B8),
    background: Color(0xFFE2E8F0),
  ),
  published(
    label: '🚀 公開済み',
    color: Color(0xFF16A34A),
    background: Color(0xFFD1FAE5),
  );

  const ArticleStatus({
    required this.label,
    required this.color,
    required this.background,
  });

  final String label;
  final Color color;
  final Color background;

  static ArticleStatus fromValue(String value) {
    return ArticleStatus.values.firstWhere(
      (s) => s.name == value,
      orElse: () => ArticleStatus.draft,
    );
  }
}

class BlogUser {
  const BlogUser({
    required this.id,
    required this.name,
    required this.email,
  });

  final int id;
  final String name;
  final String email;

  factory BlogUser.fromJson(Map<String, dynamic> json) => BlogUser(
        id: json['id'] as int,
        name: json['name'] as String? ?? '',
        email: json['email'] as String? ?? '',
      );
}

class Category {
  const Category({required this.id, required this.name, required this.color});

  final int id;
  final String name;
  final Color color;

  factory Category.fromJson(Map<String, dynamic> json) {
    return Category(
      id: json['id'] as int,
      name: json['name'] as String? ?? '',
      color: _colorFromHex(json['color'] as String? ?? '#6366f1'),
    );
  }
}

class ArticleDraft {
  ArticleDraft({
    this.id,
    this.title = '',
    this.content = '',
    this.status = ArticleStatus.draft,
    this.categoryId,
    this.tableOfContents = '',
    this.notes = '',
    this.seoTitle = '',
    this.seoDescription = '',
  });

  final int? id;
  String title;
  String content;
  ArticleStatus status;
  int? categoryId;
  String tableOfContents;
  String notes;
  String seoTitle;
  String seoDescription;

  ArticleDraft copyWith({
    int? id,
    String? title,
    String? content,
    ArticleStatus? status,
    int? categoryId,
    String? tableOfContents,
    String? notes,
    String? seoTitle,
    String? seoDescription,
  }) {
    return ArticleDraft(
      id: id ?? this.id,
      title: title ?? this.title,
      content: content ?? this.content,
      status: status ?? this.status,
      categoryId: categoryId ?? this.categoryId,
      tableOfContents: tableOfContents ?? this.tableOfContents,
      notes: notes ?? this.notes,
      seoTitle: seoTitle ?? this.seoTitle,
      seoDescription: seoDescription ?? this.seoDescription,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'title': title,
      'content': content,
      'status': status.name,
      'category_id': categoryId,
      'table_of_contents': tableOfContents,
      'notes': notes,
      'seo_title': seoTitle,
      'seo_description': seoDescription,
    };
  }
}

class Article {
  Article({
    required this.id,
    required this.userId,
    required this.title,
    required this.status,
    required this.createdAt,
    this.content = '',
    this.categoryId,
    this.tableOfContents = '',
    this.notes = '',
    this.seoTitle = '',
    this.seoDescription = '',
  });

  final int id;
  final int userId;
  String title;
  String content;
  ArticleStatus status;
  int? categoryId;
  String tableOfContents;
  String notes;
  String seoTitle;
  String seoDescription;
  DateTime createdAt;

  int get wordCount => content.trim().isEmpty
      ? 0
      : content
          .replaceAll(RegExp(r'\s+'), '')
          .characters
          .length;

  factory Article.fromJson(Map<String, dynamic> json) {
    return Article(
      id: json['id'] as int,
      userId: json['user_id'] as int? ?? 0,
      title: json['title'] as String? ?? '',
      content: json['content'] as String? ?? '',
      status: ArticleStatus.fromValue(json['status'] as String? ?? 'draft'),
      categoryId: json['category_id'] as int?,
      tableOfContents: json['table_of_contents'] as String? ?? '',
      notes: json['notes'] as String? ?? '',
      seoTitle: json['seo_title'] as String? ?? '',
      seoDescription: json['seo_description'] as String? ?? '',
      createdAt: DateTime.tryParse(json['created_at'] as String? ?? '') ??
          DateTime.now(),
    );
  }

  Article copyWith({
    String? title,
    String? content,
    ArticleStatus? status,
    int? categoryId,
    String? tableOfContents,
    String? notes,
    String? seoTitle,
    String? seoDescription,
  }) {
    return Article(
      id: id,
      userId: userId,
      title: title ?? this.title,
      content: content ?? this.content,
      status: status ?? this.status,
      categoryId: categoryId ?? this.categoryId,
      tableOfContents: tableOfContents ?? this.tableOfContents,
      notes: notes ?? this.notes,
      seoTitle: seoTitle ?? this.seoTitle,
      seoDescription: seoDescription ?? this.seoDescription,
      createdAt: createdAt,
    );
  }

  ArticleDraft toDraft() => ArticleDraft(
        id: id,
        title: title,
        content: content,
        status: status,
        categoryId: categoryId,
        tableOfContents: tableOfContents,
        notes: notes,
        seoTitle: seoTitle,
        seoDescription: seoDescription,
      );
}

Color _colorFromHex(String hex) {
  final normalized = hex.replaceFirst('#', '');
  final value = int.tryParse(normalized, radix: 16) ?? 0x6366f1;
  return Color(0xFF000000 | value);
}
