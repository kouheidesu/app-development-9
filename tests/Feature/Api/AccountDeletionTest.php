<?php

namespace Tests\Feature\Api;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountDeletionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_deletes_the_authenticated_user_and_related_articles()
    {
        $user = User::factory()->create();
        $token = $user->generateApiToken();

        Article::create([
            'user_id' => $user->id,
            'title' => '削除対象の記事',
            'status' => 'draft',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->deleteJson('/api/account');

        $response->assertOk()
            ->assertJson([
                'message' => 'アカウントを削除しました',
            ]);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('articles', ['user_id' => $user->id]);
    }

    /** @test */
    public function it_requires_authentication_to_delete_the_account()
    {
        $response = $this->deleteJson('/api/account');

        $response->assertStatus(401)
            ->assertJson([
                'message' => '認証が必要です',
            ]);
    }
}
