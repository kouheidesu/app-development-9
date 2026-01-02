import 'package:flutter/material.dart';
import 'package:uuid/uuid.dart';

enum ArticleStatus {
  draft(
    label: '📋 下書き',
    color: Color(0xFF94A3B8),
    background: Color(0xFFE2E8F0),
  ),
  inProgress(
    label: '🛠 執筆中',
    color: Color(0xFFF59E0B),
    background: Color(0xFFFEF3C7),
  ),
  ready(
    label: '🧩 入稿準備',
    color: Color(0xFF2563EB),
    background: Color(0xFFDBEAFE),
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
  const BlogUser({required this.id, required this.name, required this.email});

  final String id;
  final String name;
  final String email;
}

class Category {
  const Category({required this.id, required this.name, required this.color});

  final String id;
  final String name;
  final Color color;
}

class Tag {
  const Tag({required this.id, required this.name});

  final String id;
  final String name;
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
    this.featuredImage = '',
    List<String>? tagIds,
  }) : tagIds = tagIds ?? <String>[];

  final String? id;
  String title;
  String content;
  ArticleStatus status;
  String? categoryId;
  String tableOfContents;
  String notes;
  String seoTitle;
  String seoDescription;
  String featuredImage;
  final List<String> tagIds;

  ArticleDraft copyWith({
    String? id,
    String? title,
    String? content,
    ArticleStatus? status,
    String? categoryId,
    String? tableOfContents,
    String? notes,
    String? seoTitle,
    String? seoDescription,
    String? featuredImage,
    List<String>? tagIds,
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
      featuredImage: featuredImage ?? this.featuredImage,
      tagIds: tagIds ?? List<String>.from(this.tagIds),
    );
  }
}

class Article {
  Article({
    String? id,
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
    this.featuredImage = '',
    List<String>? tagIds,
  })  : id = id ?? const Uuid().v4(),
        tagIds = tagIds ?? <String>[];

  final String id;
  final String userId;
  String title;
  String content;
  ArticleStatus status;
  String? categoryId;
  String tableOfContents;
  String notes;
  String seoTitle;
  String seoDescription;
  String featuredImage;
  final List<String> tagIds;
  DateTime createdAt;

  int get wordCount => content.trim().isEmpty
      ? 0
      : content
          .replaceAll(RegExp(r'\\s+'), '')
          .characters
          .length;

  Article copyWith({
    String? title,
    String? content,
    ArticleStatus? status,
    String? categoryId,
    String? tableOfContents,
    String? notes,
    String? seoTitle,
    String? seoDescription,
    String? featuredImage,
    List<String>? tagIds,
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
      featuredImage: featuredImage ?? this.featuredImage,
      tagIds: tagIds ?? List<String>.from(this.tagIds),
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
        featuredImage: featuredImage,
        tagIds: List<String>.from(tagIds),
      );
}
