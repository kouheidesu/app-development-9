<?php
// nameを宣言するため？
namespace App\Models;
// 別のモデルを使用するため
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    // $fillable変数にnameを入れる
    protected $fillable = ['name'];
    // articlesメソッド宣言
    public function articles()
    {
        // 戻り値としてthisクラスのbelongsTomay
        return $this->belongsToMany(Article::class);
    }
}
