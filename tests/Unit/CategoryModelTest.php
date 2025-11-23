<?php

// ？
namespace Tests\Unit;

// 別ファイルのクラスを使用する
use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// TestCaseクラスを拡張したクラスを作成
class CategoryModelTest extends TestCase
{
    // データベースの値を全て撮り直す
    use RefreshDatabase;
    // どこからでもアクセス可能なメソッドをit_can_create_a_categoryを定義
    /** @test */
    public function it_can_create_a_category()
    {
        // $categoryにCategoryクラスのcreateメソッド実行結果を代入。name項目には"テクノロジー"color項目には"3B82F6"を代入。
        $category = Category::create([
            'name' => 'テクノロジー',
            'color' => '#3B82F6',
        ]);

        // $categoryのassertInstanceOfメソッド実行。$categoryがCategoryクラスのインスタンスかを検証する。
        $this->assertInstanceOf(Category::class, $category);
        // $categoryのassertEqualsメソッド実行。$categoryのname項目の値が"テクノロジー"であるかを検証する。
        $this->assertEquals('テクノロジー', $category->name);
        // $categoryのassertEqualsメソッドを実行。$categoryのcolor項目の値が"3B82F6"であるかを検証する。
        $this->assertEquals('#3B82F6', $category->color);
    }

    // どこからでもアクセス可能なメソッド、it_has_many_articlesを定義
    /** @test */
    public function it_has_many_articles()
    {
        // $categoryにCategoryクラスのcreateメソッド実行結果を代入。name項目に"テクノロジー"、color項目に"3B82F6"を代入。
        $category = Category::create([
            'name' => 'テクノロジー',
            'color' => '#3B82F6',
        ]);

        // article1にArticleクラスのcreateメソッド実行結果を代入。title項目に"記事1"、status項目に"draft"、category_idに$categoryのid項目の値
        $article1 = Article::create([
            'title' => '記事1',
            'status' => 'draft',
            'category_id' => $category->id,
        ]);

        // article2にArticleクラスのcreateメソッド実行結果を代入。title項目に"記事2"、status項目に"draft"、category_id項目に$categoryのid項目の値
        $article2 = Article::create([
            'title' => '記事2',
            'status' => 'draft',
            'category_id' => $category->id,
        ]);

        // $article2のassertCountメソッド実行。$categoryのarticlesには2つの項目が入っているかを検証
        $this->assertCount(2, $category->articles);
        // $articleのassertTrueメソッド実行。パラメータとしては$categoryのarticlesでcontainsメソッド実行した戻り値を入れる。title項目に記事1が含まれているか判断。
        $this->assertTrue($category->articles->contains('title', '記事1'));
        // $categoryクラスのarticles項目のtitleから記事2を取ってこれるかを検証
        $this->assertTrue($category->articles->contains('title', '記事2'));
    }

    // どこでもアクセス可能なメソッド定義
    /** @test */
    public function it_has_fillable_attributes()
    {
        // $categoryにCategoryクラスで作成したインスタンスを代入
        $category = new Category();
        // $fillableに"name"と"color"を代入
        $fillable = ['name', 'color'];
        // $categoryのassertEqualsメソッド実行。$fillableの値が$categoryのgetFillableメソッド実行結果と同じか検証。getFillable()でどこかの値を取ってくる？
        $this->assertEquals($fillable, $category->getFillable());
    }

    // どこからでもアクセス可能なメソッド定義
    /** @test */
    public function deleting_category_sets_article_category_id_to_null()
    {
        // $categoryにCategoryクラスのcreateメソッド実行結果を代入。name項目に"テクノロジー"、color項目に"3B82F6"
        $category = Category::create([
            'name' => 'テクノロジー',
            'color' => '#3B82F6',
        ]);

        // $articleにArticleクラスのcreateメソッド実行結果を代入。title項目に"テスト記事"、status項目に"draft"、category_id項目に$categoryのid項目の値を代入。
        $article = Article::create([
            'title' => 'テスト記事',
            'status' => 'draft',
            'category_id' => $category->id,
        ]);

        // $articleのassertEqualsメソッド実行。$categoryのid項目の値が$articleのcategori_id項目の値と一致しているかを判定
        $this->assertEquals($category->id, $article->category_id);
        // $categoryの値を全て削除
        $category->delete();
        // $categoryクラスのassertNullメソッドを実行。$articleの値をクリアにしてcategory_idの値をとる。
        $this->assertNull($article->fresh()->category_id);
    }
}
