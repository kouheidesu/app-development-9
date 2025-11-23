<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_the_articles_index_page()
    {
        $response = $this->get(route('articles.index'));

        $response->assertStatus(200);
        $response->assertViewIs('articles.index');
        $response->assertViewHas(['articles', 'categories', 'tags']);
    }

    /** @test */
    public function it_displays_articles_with_their_relations()
    {
        $category = Category::create(['name' => 'テスト', 'color' => '#FF0000']);
        $tag = Tag::create(['name' => 'PHP']);

        $article = Article::create([
            'title' => 'テスト記事',
            'content' => 'テスト本文',
            'status' => 'published',
            'category_id' => $category->id,
        ]);

        $article->tags()->attach($tag->id);

        $response = $this->get(route('articles.index'));

        $response->assertSee('テスト記事');
        $response->assertSee('テスト');
        $response->assertSee('PHP');
    }

    /** @test */
    public function it_can_create_an_article()
    {
        $category = Category::create(['name' => 'テスト', 'color' => '#FF0000']);

        $articleData = [
            'title' => '新しい記事',
            'content' => '新しい本文',
            'status' => 'draft',
            'category_id' => $category->id,
            'notes' => 'テストメモ',
            'table_of_contents' => '1. はじめに\n2. 本文',
        ];

        $response = $this->post(route('articles.store'), $articleData);

        $response->assertRedirect(route('articles.index'));
        $response->assertSessionHas('success', '記事が作成されました！');

        $this->assertDatabaseHas('articles', [
            'title' => '新しい記事',
            'content' => '新しい本文',
            'status' => 'draft',
            'notes' => 'テストメモ',
            'table_of_contents' => '1. はじめに\n2. 本文',
        ]);
    }

    /** @test */
    public function it_can_create_an_article_with_tags()
    {
        $tag1 = Tag::create(['name' => 'Laravel']);
        $tag2 = Tag::create(['name' => 'PHP']);

        $articleData = [
            'title' => '新しい記事',
            'status' => 'draft',
            'tags' => [$tag1->id, $tag2->id],
        ];

        $response = $this->post(route('articles.store'), $articleData);

        $article = Article::where('title', '新しい記事')->first();

        $this->assertCount(2, $article->tags);
        $this->assertTrue($article->tags->contains('name', 'Laravel'));
        $this->assertTrue($article->tags->contains('name', 'PHP'));
    }

    /** @test */
    public function it_can_create_an_article_with_seo_fields()
    {
        $articleData = [
            'title' => 'SEOテスト記事',
            'status' => 'draft',
            'seo_title' => 'SEOタイトル',
            'seo_description' => 'SEO説明文',
            'featured_image' => 'https://example.com/image.jpg',
        ];

        $response = $this->post(route('articles.store'), $articleData);

        $this->assertDatabaseHas('articles', [
            'title' => 'SEOテスト記事',
            'seo_title' => 'SEOタイトル',
            'seo_description' => 'SEO説明文',
            'featured_image' => 'https://example.com/image.jpg',
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating()
    {
        $response = $this->post(route('articles.store'), [
            'content' => 'コンテンツのみ',
        ]);

        $response->assertSessionHasErrors(['title', 'status']);
    }

    /** @test */
    public function it_validates_status_field_values()
    {
        $response = $this->post(route('articles.store'), [
            'title' => 'テスト',
            'status' => 'invalid_status',
        ]);

        $response->assertSessionHasErrors('status');
    }

    /** @test */
    public function it_can_update_an_article()
    {
        $article = Article::create([
            'title' => '元のタイトル',
            'content' => '元の本文',
            'status' => 'draft',
        ]);

        $updateData = [
            'title' => '更新されたタイトル',
            'content' => '更新された本文',
            'status' => 'published',
            'notes' => '更新メモ',
        ];

        $response = $this->put(route('articles.update', $article), $updateData);

        $response->assertRedirect(route('articles.index'));
        $response->assertSessionHas('success', '記事が更新されました！');

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => '更新されたタイトル',
            'content' => '更新された本文',
            'status' => 'published',
            'notes' => '更新メモ',
        ]);
    }

    /** @test */
    public function it_can_update_article_tags()
    {
        $article = Article::create([
            'title' => 'テスト記事',
            'status' => 'draft',
        ]);

        $tag1 = Tag::create(['name' => 'Tag1']);
        $tag2 = Tag::create(['name' => 'Tag2']);
        $tag3 = Tag::create(['name' => 'Tag3']);

        $article->tags()->attach([$tag1->id, $tag2->id]);

        $updateData = [
            'title' => 'テスト記事',
            'status' => 'draft',
            'tags' => [$tag2->id, $tag3->id],
        ];

        $response = $this->put(route('articles.update', $article), $updateData);

        $article->refresh();

        $this->assertCount(2, $article->tags);
        $this->assertFalse($article->tags->contains('name', 'Tag1'));
        $this->assertTrue($article->tags->contains('name', 'Tag2'));
        $this->assertTrue($article->tags->contains('name', 'Tag3'));
    }

    /** @test */
    public function it_can_delete_an_article()
    {
        $article = Article::create([
            'title' => '削除される記事',
            'status' => 'draft',
        ]);

        $response = $this->delete(route('articles.destroy', $article));

        $response->assertRedirect(route('articles.index'));
        $response->assertSessionHas('success', '記事が削除されました！');

        $this->assertDatabaseMissing('articles', [
            'id' => $article->id,
        ]);
    }

    /** @test */
    public function it_deletes_article_tag_relationships_when_article_is_deleted()
    {
        $article = Article::create([
            'title' => '削除される記事',
            'status' => 'draft',
        ]);

        $tag = Tag::create(['name' => 'TestTag']);
        $article->tags()->attach($tag->id);

        $this->assertDatabaseHas('article_tag', [
            'article_id' => $article->id,
            'tag_id' => $tag->id,
        ]);

        $article->delete();

        $this->assertDatabaseMissing('article_tag', [
            'article_id' => $article->id,
        ]);
    }

    /** @test */
    public function it_can_handle_null_category_id()
    {
        $articleData = [
            'title' => 'カテゴリなし記事',
            'status' => 'draft',
            'category_id' => null,
        ];

        $response = $this->post(route('articles.store'), $articleData);

        $this->assertDatabaseHas('articles', [
            'title' => 'カテゴリなし記事',
            'category_id' => null,
        ]);
    }

    /** @test */
    public function it_validates_category_exists()
    {
        $response = $this->post(route('articles.store'), [
            'title' => 'テスト',
            'status' => 'draft',
            'category_id' => 9999, // 存在しないID
        ]);

        $response->assertSessionHasErrors('category_id');
    }

    /** @test */
    public function it_displays_articles_ordered_by_created_at_desc()
    {
        // 明示的にタイムスタンプを設定して作成
        $article1 = new Article([
            'title' => '最初の記事',
            'status' => 'published',
        ]);
        $article1->created_at = now()->subDays(2);
        $article1->save();

        $article2 = new Article([
            'title' => '最新の記事',
            'status' => 'published',
        ]);
        $article2->created_at = now();
        $article2->save();

        $article3 = new Article([
            'title' => '中間の記事',
            'status' => 'published',
        ]);
        $article3->created_at = now()->subDay();
        $article3->save();

        $response = $this->get(route('articles.index'));

        $articles = $response->viewData('articles');

        $this->assertEquals('最新の記事', $articles[0]->title);
        $this->assertEquals('中間の記事', $articles[1]->title);
        $this->assertEquals('最初の記事', $articles[2]->title);
    }

    /** @test */
    public function it_counts_words_when_creating_article()
    {
        $articleData = [
            'title' => 'テスト記事',
            'content' => 'これはテストです。文字数をカウントします。',
            'status' => 'draft',
        ];

        $this->post(route('articles.store'), $articleData);

        $article = Article::where('title', 'テスト記事')->first();

        // 「これはテストです。文字数をカウントします。」= 21文字
        $this->assertEquals(21, $article->word_count);
    }
}
