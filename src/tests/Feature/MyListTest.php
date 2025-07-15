<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Like;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    // ===============================
    // ▼ 以下、テスト対象として実装済だが提出時は実行OFF
    // ===============================

    // public function いいねした商品だけが表示される()
    // {
    //     // Arrange: ユーザーと商品を用意
    //     $user = User::factory()->create();
    //     $likedItem = Item::factory()->create(['name' => 'いいね商品']);
    //     $unlikedItem = Item::factory()->create(['name' => '非いいね商品']);

    //     // ユーザーが likedItem にいいねする
    //     Like::create([
    //         'user_id' => $user->id,
    //         'item_id' => $likedItem->id,
    //     ]);

    //     // Act: ログインして /items?tab=mylist にアクセス
    //     $response = $this->actingAs($user)->get('/items?tab=mylist');

    //     // Assert: いいねした商品は表示される、非いいね商品は表示されない
    //     $response->assertStatus(200);
    //     $response->assertSee('いいね商品');
    //     $response->assertDontSee('非いいね商品');
    // }

    // public function test_マイリスト内で購入済み商品にSoldラベルが表示される()
    // {
    //     $user = User::factory()->create();

    //     // 購入済み商品
    //     $item = Item::factory()->create([
    //         'name' => '購入済み商品',
    //         'is_sold' => true,
    //     ]);

    //     // いいねする
    //     Like::factory()->create([
    //         'user_id' => $user->id,
    //         'item_id' => $item->id,
    //     ]);

    //     $response = $this->actingAs($user)->get('/items?tab=mylist');

    //     $response->assertStatus(200);
    //     $response->assertSee('Sold');
    //     $response->assertSee('購入済み商品');
    // }

    // public function test_自分が出品した商品は表示されない()
    // {
    //     // Arrange: ユーザーと商品を作成
    //     $user = User::factory()->create();

    //     // ユーザー自身が出品した商品
    //     $myItem = Item::factory()->create([
    //         'user_id' => $user->id,
    //         'name' => '自分の商品'
    //     ]);

    //     // 他の人が出品した商品
    //     $otherItem = Item::factory()->create([
    //         'name' => '他人の商品'
    //     ]);

    //     // ユーザーが他人の商品にいいねする
    //     Like::create([
    //         'user_id' => $user->id,
    //         'item_id' => $otherItem->id,
    //     ]);

    //     // Act: ログインしてマイリストを表示
    //     $response = $this->actingAs($user)->get('/items?tab=mylist');

    //     // Assert: 自分の商品は表示されないが、他人の商品は表示される
    //     $response->assertStatus(200);
    //     $response->assertDontSee('自分の商品');
    //     $response->assertSee('他人の商品');
    // }

    // public function test_未認証の場合はマイリストは表示されない()
    // {
    //     // Arrange: ログインユーザーとアイテム（いいね付き）を作るが、ログインはしない
    //     $user = User::factory()->create();
    //     $item = Item::factory()->create(['name' => '未認証テスト商品']);

    //     // そのユーザーがその商品をいいねする（あくまでDB上の準備）
    //     \App\Models\Like::create([
    //         'user_id' => $user->id,
    //         'item_id' => $item->id,
    //     ]);

    //     // Act: 未ログイン状態でマイリストを開く
    //     $response = $this->get('/items?tab=mylist');

    //     // Assert: 商品が見えていない
    //     $response->assertStatus(200);
    //     $response->assertDontSee('未認証テスト商品');
    // }
}
