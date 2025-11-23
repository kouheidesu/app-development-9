<?php
// 名前の宣言
namespace Tests\Unit;
// 他ファイルクラスを引用可能に設定
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// TestCaseクラスを拡張
class ArticleModelTest extends TestCase
{
    // RefreshDatabaseクラスを使用
    use RefreshDatabase;
    // どこでも使用可能なメソッド、it_can_create_an_articleを定義
    /** @test */
    public function it_can_create_an_article()
    {
        // $articleにArticleクラスのcreateメソッド実行した結果を入れる。title項目には"テスト記事"、contents項目には"テスト本文"、status項目には"draft"
        $article = Article::create([
            'title' => 'テスト記事',
            'content' => 'テスト本文',
            'status' => 'draft',
        ]);
        // $articleのassertInstanceOfメソッド実行。Articleのclassメソッドで$articleを作成
        $this->assertInstanceOf(Article::class, $article);
        // $articleのassertEqualsメソッド実行。"テスト記事"が$articleのtitle項目と一致するか
        $this->assertEquals('テスト記事', $article->title);
        // $articleのassertEqualsメソッド実行。"テスト本文"が$articleのcontent項目と一致するか
        $this->assertEquals('テスト本文', $article->content);
        // $articleのassertEqualsメソッド実行。"draft"が$articleのstatus項目と一致するか
        $this->assertEquals('draft', $article->status);
    }
    // どこからでも使用可能なメソッド、it_belongs_to_a_categoryを定義
    /** @test */
    public function it_belongs_to_a_category()
    {
        // $categoryにCategoryクラスのcreateメソッド実行結果を代入。name項目に"テストカテゴリ"、color項目に"#FF0000"を入れる。
        $category = Category::create([
            'name' => 'テストカテゴリ',
            'color' => '#FF0000',
        ]);

        // 以下も同じようにそれぞれの項目に値を代入する
        $article = Article::create([
            'title' => 'テスト記事',
            'content' => 'テスト本文',
            'status' => 'draft',
            'category_id' => $category->id,
        ]);

        // $articleクラスのassertInstanceOfメソッドを実行。Categoryクラスで$articleのcategory項目作成可能か
        $this->assertInstanceOf(Category::class, $article->category);
        // $articleのassertEqualsメソッド実行。"テストカテゴリ"と$articleのcategory項目の名前と一致するか
        $this->assertEquals('テストカテゴリ', $article->category->name);
    }


    /** @test */
    public function it_belongs_to_many_tags()
    {
        $article = Article::create([
            'title' => 'テスト記事',
            'content' => 'テスト本文',
            'status' => 'draft',
        ]);

        // $tag1にTagクラスのcreateメソッドの結果を代入。name項目に"PHP"を代入。
        $tag1 = Tag::create(['name' => 'PHP']);
        // $tag1にTagクラスのcreateメソッドの結果を代入。name項目に"Laravel"を代入。
        $tag2 = Tag::create(['name' => 'Laravel']);
        // $articleにtagsメソッドのattachメソッド結果を代入。$tag1にidの値を代入。$tag2にidの値を代入。
        $article->tags()->attach([$tag1->id, $tag2->id]);
        // $tagsのassertCount実行。$articleのtags項目の値が2個か
        $this->assertCount(2, $article->tags);
        // $tagのassertTrueメソッドを実行。$articleのtags項目のcontainsメソッドを実行。name項目にに"PHP"が存在する確認。
        $this->assertTrue($article->tags->contains('name', 'PHP'));
        // $tagのassertTrueメソッドを実行。$articleのtags項目のcontainsメソッドを実行。name項目にに"Laravel"が存在する確認。
        $this->assertTrue($article->tags->contains('name', 'Laravel'));
    }

    /** @test */
    public function it_automatically_calculates_word_count()
    {
        $article = Article::create([
            'title' => 'テスト記事',
            'content' => 'これはテスト本文です。',
            'status' => 'draft',
        ]);

        // 「これはテスト本文です。」= 11文字
        $this->assertEquals(11, $article->word_count);
    }

    /** @test */
    public function it_strips_html_tags_when_calculating_word_count()
    {
        $article = Article::create([
            'title' => 'テスト記事',
            'content' => '<h2>見出し</h2><p>本文テスト</p>',
            'status' => 'draft',
        ]);

        // HTMLタグを除いた「見出し本文テスト」= 8文字（スペースなし）
        $this->assertEquals(8, $article->word_count);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        // $articleという新しいオブジェクトを作成
        $article = new Article();
        // 配列方変数、$fillableに値を入れる。
        $fillable = [
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

        $this->assertEquals($fillable, $article->getFillable());
    }

    /** @test */
    public function it_can_be_created_without_optional_fields()
    {
        $article = Article::create([
            'title' => 'タイトルのみ',
            'status' => 'draft',
        ]);

        // $articleのassertNullメソッドを実行。$articleのcontent項目がnullでないかを確認する。4行のコードでそれぞれの項目がnullでないかを確認。
        $this->assertNull($article->content);
        $this->assertNull($article->category_id);
        $this->assertNull($article->notes);
        $this->assertNull($article->table_of_contents);
    }

    /** @test */
    public function it_casts_target_publish_date_as_date()
    {
        $article = Article::create([
            'title' => 'テスト記事',
            'status' => 'draft',
            'target_publish_date' => '2025-12-31',
        ]);

        // $articleのassertInstanceOfメソッドを実行。Carbonクラスに$articleのtarget_publish_dateがあるかを確認
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $article->target_publish_date);
    }

    /** @test */
    public function it_updates_word_count_when_content_changes()
    {
        $article = Article::create([
            'title' => 'テスト記事',
            'content' => '短い',
            'status' => 'draft',
        ]);

        // $articleのassertEqualsメソッドを実行。$articleのword_count項目が2であるか
        $this->assertEquals(2, $article->word_count);
        // $articleのupdateメソッド実行。content項目の内容を"もっと長い本文になりました"に更新する
        $article->update(['content' => 'もっと長い本文になりました']);
        // $articleのassertEqualsメソッド実行。13と$articleを取り直したword_countが一致するか判定
        $this->assertEquals(13, $article->fresh()->word_count);
    }
}
