<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $articles = Article::with('category')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'articles' => $articles->map(fn (Article $article) => $this->formatArticle($article)),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $article = Article::create(array_merge($data, [
            'user_id' => $request->user()->id,
        ]));
        $article->load('category');

        return response()->json([
            'article' => $this->formatArticle($article),
        ], 201);
    }

    public function update(Request $request, Article $article)
    {
        $this->ensureOwnership($request, $article);
        $data = $this->validatedData($request);
        $article->update($data);
        $article->load('category');

        return response()->json([
            'article' => $this->formatArticle($article),
        ]);
    }

    public function destroy(Request $request, Article $article)
    {
        $this->ensureOwnership($request, $article);
        $article->delete();

        return response()->json([
            'message' => '記事を削除しました',
        ]);
    }

    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'category_id' => ['nullable', 'exists:categories,id'],
            'table_of_contents' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
        ]);
    }

    protected function ensureOwnership(Request $request, Article $article): void
    {
        if ($article->user_id !== $request->user()->id) {
            abort(403, 'この記事を操作する権限がありません');
        }
    }

    protected function formatArticle(Article $article): array
    {
        return [
            'id' => $article->id,
            'user_id' => $article->user_id,
            'title' => $article->title,
            'content' => $article->content ?? '',
            'status' => $article->status,
            'category_id' => $article->category_id,
            'category' => $article->category ? [
                'id' => $article->category->id,
                'name' => $article->category->name,
                'color' => $article->category->color,
            ] : null,
            'table_of_contents' => $article->table_of_contents ?? '',
            'notes' => $article->notes ?? '',
            'seo_title' => $article->seo_title ?? '',
            'seo_description' => $article->seo_description ?? '',
            'word_count' => $article->word_count ?? 0,
            'created_at' => $article->created_at?->toIso8601String(),
        ];
    }
}
