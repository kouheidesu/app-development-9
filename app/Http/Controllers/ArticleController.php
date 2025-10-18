<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::with(['category', 'tags'])
            ->orderBy('created_at', 'desc')
            ->get();
        $categories = Category::all();
        $tags = Tag::all();

        return view('articles.index', compact('articles', 'categories', 'tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'status' => 'required|in:draft,in_progress,ready,published',
            'category_id' => 'nullable|exists:categories,id',
            'notes' => 'nullable|string',
            'target_publish_date' => 'nullable|date',
            'tags' => 'nullable|array',
        ]);

        $article = Article::create($validated);

        if ($request->has('tags')) {
            $article->tags()->sync($request->tags);
        }

        return redirect()->route('articles.index')->with('success', '記事が作成されました！');
    }

    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'status' => 'required|in:draft,in_progress,ready,published',
            'category_id' => 'nullable|exists:categories,id',
            'notes' => 'nullable|string',
            'target_publish_date' => 'nullable|date',
            'tags' => 'nullable|array',
        ]);

        $article->update($validated);

        if ($request->has('tags')) {
            $article->tags()->sync($request->tags);
        }

        return redirect()->route('articles.index')->with('success', '記事が更新されました！');
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('articles.index')->with('success', '記事が削除されました！');
    }
}
