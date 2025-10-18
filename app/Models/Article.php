<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'title',
        'content',
        'status',
        'category_id',
        'notes',
        'seo_title',
        'seo_description',
        'featured_image',
        'word_count',
        'target_publish_date',
    ];

    protected $casts = [
        'target_publish_date' => 'date',
    ];

    // リレーション
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    // 文字数を自動計算
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($article) {
            if ($article->content) {
                $article->word_count = mb_strlen(strip_tags($article->content));
            }
        });
    }
}
