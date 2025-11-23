<?php

// 名前をつける
namespace Tests\Unit;

// 活用するクラスを記載
use App\Models\Article;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// TestCaseクラスを活用してクラスを作成
class TagModelTest extends TestCase
{
    // RefreshDatabaseクラスを使用
    use RefreshDatabase;

    /** @test */
    // it_can_create_a_tagメソッドを作成
    public function it_can_create_a_tag()
    {
        // Tagクラスのcreateメソッドを実行し戻り値を$tagに代入。nameをLaravelに変更
        $tag = Tag::create(['name' => 'Laravel']);
        // TagModelTestクラスのassertInstanceOfメソッドを実行
        $this->assertInstanceOf(Tag::class, $tag);
        // TagModelTestクラスのassertEqualsメソッドを実行
        $this->assertEquals('Laravel', $tag->name);
    }

    /** @test */
    // it_belongs_to_many_articlesメソッドを実行
    public function it_belongs_to_many_articles()
    {
        // $tag変数にTagクラスのcreateメソッド実行した戻り値を代入
        $tag = Tag::create(['name' => 'PHP']);
        // $article1にArticleクラスのcreateメソッド実行した値を入れる。titleに記事1、statusにdraftを代入
        $article1 = Article::create([
            'title' => '記事1',
            'status' => 'draft',
        ]);
        // $article2にArticleクラスのcreateメソッド実行した値を入れる。titleに記事1、statusにdraftを代入
        $article2 = Article::create([
            'title' => '記事2',
            'status' => 'draft',
        ]);
        // $tag変数の値でarticlesメソッド実行、その戻り値でattachメソッド実行。$article1の値はidに$article2の値もidに入れる。
        $tag->articles()->attach([$article1->id, $article2->id]);
        // $tagのassertCountメソッドを実行。
        $this->assertCount(2, $tag->articles);
        // $tagのassertTrueメソッドを実行。パラメータは$tag変数のarticles変数のcontainsメソッド。
        $this->assertTrue($tag->articles->contains('title', '記事1'));
        // $tagのassertTrueメソッドを実行。
        $this->assertTrue($tag->articles->contains('title', '記事2'));
    }

    /** @test */
    // it_has_fillable_attributesメソッドを定義
    public function it_has_fillable_attributes()
    {
        // $tag変数にTagクラスから作成したオブジェクトを代入
        $tag = new Tag();
        // $fillable変数(配列？)にn”ame"を代入
        $fillable = ['name'];
        // $tag変数のassertEqualsメソッドを実行。$fillableの値が$tag変数のgetFillableメソッド実行の値と一緒かを判断する。
        $this->assertEquals($fillable, $tag->getFillable());
    }

    /** @test */
    // multiple_articles_can_have_same_tagメソッドを定義
    public function multiple_articles_can_have_same_tag()
    {
        // $tag変数にTagクラスのcreateメソッド実行した結果を入れる。name列に"WordPress"を代入する
        $tag = Tag::create(['name' => 'WordPress']);
        // $article1にArticleクラスのcreateメソッド実行結果を入れる。title項目に"WordPress入門"、status項目に"published"を代入。
        $article1 = Article::create([
            'title' => 'WordPress入門',
            'status' => 'published',
        ]);
        // 上記と同じようにする
        $article2 = Article::create([
            'title' => 'WordPressカスタマイズ',
            'status' => 'published',
        ]);
        // 上記と同じようにする
        $article3 = Article::create([
            'title' => 'WordPressプラグイン',
            'status' => 'draft',
        ]);
        // $article1変数のtagsメソッド実行。戻り値でattachメソッドを実行。$tag変数にidを追加。それを$article2,$article3でも同じことをやる
        $article1->tags()->attach($tag->id);
        $article2->tags()->attach($tag->id);
        $article3->tags()->attach($tag->id);
        // $tagのassertCountを実行。
        $this->assertCount(3, $tag->fresh()->articles);
    }
}
