<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    // Modelクラス内しか使用できない変数、fillableを定義
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'status',
        'category_id',
        'table_of_contents',
        'notes',
        'seo_title',
        'seo_description',
        'featured_image',
        'word_count',
        'target_publish_date',
    ];

    protected $casts = [
        //target_publish_dateカラムはdateカラムとして使用する
        'target_publish_date' => 'date',
    ];

    // リレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        // このクラスのbelongsToメソッドを実行？パラメータはCategoryのclass？
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        // このクラスのbelongsTomanyメソッドを実行。パラメータはTagのclass
        return $this->belongsToMany(Tag::class);
    }

    // 文字数を自動計算
    protected static function boot()
    {
        // 親クラスのbootを実行？
        parent::boot();
        // インスタンスを作らずにsavingメソッドを実行。もし$article変数にcontentがあるなら$article変数のworld_countにmb_strlen関数の実行結果を入れる
        static::saving(function ($article) {
            if ($article->content) {
                $article->word_count = mb_strlen(strip_tags($article->content));
            }
        });
    }
}
