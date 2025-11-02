<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // fillable変数(配列型)に値をそれぞれ入れる
    protected $fillable = ['name', 'color'];

    public function articles()
    {
        // articlesメソッドの戻り値は、thisクラスのhasManyメソッドをArticleのclassにする。
        return $this->hasMany(Article::class);
    }
}
