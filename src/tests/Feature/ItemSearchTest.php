<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemSearchTest extends TestCase
{
    use RefreshDatabase;

    // ===============================
    // ▼ 以下、テスト対象として実装済だが提出時は実行OFF
    // ===============================

    // public function test_商品名で部分一致検索ができる()
    // {
    //     // Arrange: 商品データを用意
    //     Item::factory()->create(['name' => '赤い靴']);
    //     Item::factory()->create(['name' => '青い帽子']);
    //     Item::factory()->create(['name' => '赤いシャツ']);

    //     // Act: 「赤」で検索する
    //     $response = $this->get('/items?keyword=赤');

    //     // Assert: 「赤い靴」「赤いシャツ」が表示され、「青い帽子」は表示されない
    //     $response->assertStatus(200);
    //     $response->assertSee('赤い靴');
    //     $response->assertSee('赤いシャツ');
    //     $response->assertDontSee('青い帽子');
    // }

    // public function test_検索状態がマイリストでも保持されている()
    // {
    //     // Arrange: ユーザーと商品を準備
    //     $user = User::factory()->create();
    //     $item1 = Item::factory()->create(['name' => 'テストりんご']);
    //     $item2 = Item::factory()->create(['name' => 'バナナ']);

    //     // item1 にだけ「いいね」
    //     $user->likedItems()->attach($item1->id);

    //     // Act: ログインしてマイリストタブで「りんご」で検索
    //     $response = $this->actingAs($user)->get('/items?tab=mylist&keyword=りんご');

    //     // Assert: item1（りんご）は表示され、item2（バナナ）は非表示
    //     $response->assertStatus(200);
    //     $response->assertSee('テストりんご');
    //     $response->assertDontSee('バナナ');
    // }
}
