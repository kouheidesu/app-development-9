<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json([
            'categories' => Category::orderBy('name')
                ->get()
                ->map(fn (Category $category) => $this->formatCategory($category)),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'color' => ['nullable', 'string', 'regex:/^#?[0-9a-fA-F]{6}$/'],
        ]);

        $category = Category::create([
            'name' => $data['name'],
            'color' => $this->normalizeColor($data['color'] ?? '#6366f1'),
        ]);

        return response()->json([
            'category' => $this->formatCategory($category),
        ], 201);
    }

    public function destroy(Category $category)
    {
        Article::where('category_id', $category->id)->update(['category_id' => null]);
        $category->delete();

        return response()->json([
            'message' => 'カテゴリを削除しました',
        ]);
    }

    protected function formatCategory(Category $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'color' => $category->color,
        ];
    }

    protected function normalizeColor(string $color): string
    {
        $color = ltrim($color, '#');
        return '#' . strtolower($color);
    }
}
