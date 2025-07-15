<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class LikeFeatureTest extends TestCase
{
    use RefreshDatabase;
    // ===============================
    // ▼ 以下、テスト対象として実装済だが提出時は実行OFF
    // ===============================

    // public function test_いいねアイコンを押下することによって、いいねした商品として登録することができる()
    // {
    //     // Arrange: ユーザーと商品を準備
    //     $user = User::factory()->create();
    //     $item = Item::factory()->create();

    //     // Act: ログイン状態にして、いいねPOST送信
    //     $this->actingAs($user);
    //     $response = $this->post("/item/{$item->id}/like");

    //     // Assert: ステータスとDB登録の確認
    //     $response->assertStatus(200);
    //     $this->assertDatabaseHas('likes', [
    //         'user_id' => $user->id,
    //         'item_id' => $item->id,
    //     ]);
    // }

    //いいねした商品として登録され、いいね合計値が増加表示さ//
    // public function test_いいね済みアイコンには_liked_クラスが付く()
    // {
    //     // Arrange
    //     $user = User::factory()->create();
    //     $item = Item::factory()->create();

    //     // 事前にいいね済にしておく
    //     $item->likedUsers()->attach($user->id);

    //     // Act
    //     $this->actingAs($user);
    //     $response = $this->get("/item/{$item->id}");

    //     // Assert
    //     $response->assertStatus(200);
    //     $response->assertSee('class="liked"', false); // HTML内に liked クラスがあるか
    // }

    //再度いいねアイコンを押下することによって、いいねを解除することができる。
    public function test_再度いいねボタンを押すといいねを解除できる()
    {
        // Arrange
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 事前にいいね済みにしておく
        $item->likedUsers()->attach($user->id);

        // Act
        $this->actingAs($user);
        $response = $this->post("/item/{$item->id}/like");

        // Assert
        $response->assertStatus(200);
        $response->assertJson([
            'liked' => false, // 解除されたこと
            'likes_count' => 0, // 合計いいね数が0になったこと
        ]);

        // DB上でも確認（pivotテーブルから削除されているか）
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }
}
