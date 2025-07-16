<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class CommentFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_logged_in_user_can_post_comment(): void
    {
        // Arrange: ユーザーと商品を作成
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // Act: ユーザーとしてログインしてコメント送信
        $response = $this->actingAs($user)->post("/item/{$item->id}/comment", [
            'content' => 'とても良い商品です！',
        ]);

        // Assert: リダイレクト成功と、DBにコメントがあること
        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'とても良い商品です！',
        ]);
    }

    public function test_guest_user_cannot_post_comment(): void
    {
        // Arrange: 商品だけ用意（ユーザーはログインしない）
        $item = \App\Models\Item::factory()->create();

        // Act: ログインせずにコメントを送信
        $response = $this->post("/item/{$item->id}/comment", [
            'content' => 'ログインしてないけど送信！',
        ]);

        // Assert: ログイン画面にリダイレクトされる（未認証ユーザーは通常 /login に飛ばされる）
        $response->assertRedirect('/login');

        // コメントは保存されていないこと
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'content' => 'ログインしてないけど送信！',
        ]);
    }

    public function test_comment_validation_error_when_content_is_empty(): void
    {
        // Arrange: ユーザーと商品を作成
        $user = \App\Models\User::factory()->create();
        $item = \App\Models\Item::factory()->create();

        // Act: コメントなしで送信
        $response = $this->actingAs($user)->from("/items/{$item->id}")
            ->post("/item/{$item->id}/comment", [
                'content' => '',
            ]);

        // Assert: 元のページへリダイレクト & エラー表示
        $response->assertRedirect("/items/{$item->id}");
        $response->assertSessionHasErrors('content');
    }

    public function test_comment_validation_error_when_content_is_too_long(): void
    {
        // Arrange
        $user = \App\Models\User::factory()->create();
        $item = \App\Models\Item::factory()->create();

        // 256文字の文字列を生成
        $longComment = str_repeat('あ', 256);

        // Act
        $response = $this->actingAs($user)->from("/items/{$item->id}")
            ->post("/item/{$item->id}/comment", [
                'content' => $longComment,
            ]);

        // Assert
        $response->assertRedirect("/items/{$item->id}");
        $response->assertSessionHasErrors('content');
    }
}
