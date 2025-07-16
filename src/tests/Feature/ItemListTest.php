<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;

class ItemListTest extends TestCase
{
    use RefreshDatabase;
    public function test_全商品を取得できる()
    {
        // Arrange: 商品を3つ作成
        $items = Item::factory()->count(3)->create();

        // Act: 商品一覧ページにアクセス
        $response = $this->get('/items');

        // Assert: ステータスコードと商品名が含まれているか確認
        $response->assertStatus(200);

        foreach ($items as $item) {
            $response->assertSee($item->name);
        }
    }

    public function test_購入済み商品には_Sold_ラベルが表示される()
    {
        // Arrange: 購入済みの商品を1つ作成
        $item = \App\Models\Item::factory()->create([
            'is_sold' => true,
            'name' => '購入済みのテスト商品',
        ]);

        // Act: 商品一覧ページにアクセス
        $response = $this->get('/items');

        // Assert: 商品名と一緒に "Sold" の表示があるかを確認
        $response->assertStatus(200);
        $response->assertSee($item->name);
        $response->assertSee('Sold');
    }
    
    public function test_自分が出品した商品は表示されない()
    {
        // Arrange: ログインユーザーと商品を作成
        $user = \App\Models\User::factory()->create();
        $ownItem = \App\Models\Item::factory()->create([
            'user_id' => $user->id,
            'name' => '自分の商品',
        ]);
        $otherItem = \App\Models\Item::factory()->create([
            'name' => '他人の商品',
        ]);

        // Act: ログイン状態で商品一覧ページにアクセス
        $response = $this->actingAs($user)->get('/items');

        // Assert: 自分の商品は表示されない、他人の商品は表示される
        $response->assertStatus(200);
        $response->assertDontSee('自分の商品');
        $response->assertSee('他人の商品');
    }
}
