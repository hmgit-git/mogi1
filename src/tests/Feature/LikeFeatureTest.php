<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class LikeFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_いいねアイコンを押下することによって、いいねした商品として登録することができる()
    {
        // Arrange: ユーザーと商品を準備
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // Act: ログイン状態にして、いいねPOST送信
        $this->actingAs($user);
        $response = $this->post("/item/{$item->id}/like");

        // Assert: ステータスとDB登録の確認
        $response->assertStatus(200);
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    public function test_いいね済みアイコンには_liked_クラスが付く()
    {
        // Arrange
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 事前にいいね済にしておく
        $item->likedUsers()->attach($user->id);

        // Act
        $this->actingAs($user);
        $response = $this->get("/item/{$item->id}");

        // Assert
        $response->assertStatus(200);
        $response->assertSee('class="liked"', false); // HTML内に liked クラスがあるか
    }

    public function test_再度いいねボタンを押すといいねを解除できる()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 事前にいいね済みにしておく
        $item->likedUsers()->attach($user->id);

        $this->actingAs($user);
        $response = $this->post("/item/{$item->id}/like");

        $response->assertStatus(200);
        $response->assertJson([
            'liked' => false, // 解除されたこと
            'likes_count' => 0, // 合計いいね数が0になったこと
        ]);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }
}
