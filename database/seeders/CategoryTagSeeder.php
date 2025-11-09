<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class CategoryTagSeeder extends Seeder
{
    public function run(): void
    {
        // カテゴリの作成
        $categories = [
            ['name' => '技術記事', 'color' => '#3B82F6'],
            ['name' => 'チュートリアル', 'color' => '#10B981'],
            ['name' => 'ニュース', 'color' => '#F59E0B'],
            ['name' => 'レビュー', 'color' => '#8B5CF6'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // タグの作成
        $tags = [
            ['name' => 'Laravel'],
            ['name' => 'PHP'],
            ['name' => 'JavaScript'],
            ['name' => 'Vue.js'],
            ['name' => 'Tailwind CSS'],
        ];

        foreach ($tags as $tag) {
            Tag::create($tag);
        }
    }
}
