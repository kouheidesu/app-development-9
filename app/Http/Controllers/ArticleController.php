<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    // indexメソッド
    public function index()
    {
        // articles変数の中にcategoryDBのtagsテーブルから取ってくる？
        $articles = Article::with(['category', 'tags'])
            // created_atカラムを昇順表示する
            ->orderBy('created_at', 'desc')
            // 取得する
            ->get();
        // categories変数CategoryDBを全てを入れる
        $categories = Category::all();
        // tags変数にtagテーブルを全て入れる
        $tags = Tag::all();
        // view関数でarticles.indexを表示。配列も作成
        return view('articles.index', compact('articles', 'categories', 'tags'));
    }

    // store(保存)メソッド
    public function store(Request $request)
    {
        // request変数の値をvalidate関数の値を入れる
        $validated = $request->validate([
            // それぞれのクラスの入れるルールを決める
            // titleカラムは必須、文字型、255文字まで
            'title' => 'required|string|max:255',
            // contentカラムは値なしok、文字型
            'content' => 'nullable|string',
            // statusカラムは必須、？、？、？
            'status' => 'required|in:draft,in_progress,ready,published',
            // category_idカラムは必須、存在する、idで？
            'category_id' => 'nullable|exists:categories,id',
            // notesカラムは値なしok、文字型
            'notes' => 'nullable|string',
            // target_publish_dateカラムは値なしok、日付型
            'target_publish_date' => 'nullable|date',
            // tagsカラムは値なしok、配列
            'tags' => 'nullable|array',
        ]);
        // $articleにcreate関数の結果を入れる
        $article = Article::create($validated);
        // もし$requestがtagsカラムを持つなら$article変数tagsメソッドの結果でsysncメソッドを実行する
        if ($request->has('tags')) {
            $article->tags()->sync($request->tags);
        }
        // 戻り値としてredirect関数の戻り値でrouteメソッドをwithメソッドを実行する
        return redirect()->route('articles.index')->with('success', '記事が作成されました！');
    }

    // updateメソッドを定義。それぞれのパラメータを設定している
    public function update(Request $request, Article $article)
    {
        // $validated変数に$request変数の値でvalidateした結果を入れる
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'status' => 'required|in:draft,in_progress,ready,published',
            'category_id' => 'nullable|exists:categories,id',
            'notes' => 'nullable|string',
            'target_publish_date' => 'nullable|date',
            'tags' => 'nullable|array',
        ]);

        // $article変数の値でupdateメソッド実行
        $article->update($validated);
        // もし$request変数がtags絡むを持つなら
        if ($request->has('tags')) {
            // $article変数でtagsメソッドを実行その後syncメソッドも実行
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
