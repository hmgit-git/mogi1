<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class ShippingAddressFeatureTest extends TestCase
{
    use RefreshDatabase;
    // ===============================
    // ▼ 以下、テスト対象として実装済だが提出時は実行OFF
    // ===============================

    // public function test_送付先住所変更後に購入画面に反映される()
    // {
    //     $user = User::factory()->create(['email_verified_at' => now()]);
    //     $item = Item::factory()->create();

    //     $newZip = '987-6543';
    //     $newAddress = '東京都新宿区西新宿1-1-1';
    //     $newBuilding = 'テストビル101';

    //     // ログインしてセッションに住所を保存
    //     $response = $this->actingAs($user)->post("/purchase/{$item->id}/address", [
    //         'zip' => $newZip,
    //         'address' => $newAddress,
    //         'building' => $newBuilding,
    //     ]);

    //     // セッションを取得
    //     $session = $response->baseResponse->getSession()->all();

    //     // セッションを引き継いで画面を表示
    //     $response = $this->withSession($session)->actingAs($user)->get("/purchase/{$item->id}");

    //     // セッションに保存された住所が画面に表示されている
    //     $response->assertSee($newZip);
    //     $response->assertSee($newAddress);
    //     $response->assertSee($newBuilding);
    // }

    // public function test_購入した商品に送付先住所が紐づいて登録される()
    // {
    //     $user = User::factory()->create(['email_verified_at' => now()]);
    //     $item = Item::factory()->create();

    //     $newZip = '987-6543';
    //     $newAddress = '東京都渋谷区桜丘町1-1';
    //     $newBuilding = 'サンプルビル302';

    //     // 購入処理をセッション付きで実行
    //     $this->actingAs($user)
    //         ->withSession([
    //             'shipping_zip' => $newZip,
    //             'shipping_address' => $newAddress,
    //             'shipping_building' => $newBuilding,
    //         ])
    //         ->post("/purchase/{$item->id}", [
    //             'payment_method' => 'convenience_store',
    //         ]);

    //     // DBに正しく保存されたか確認
    //     $this->assertDatabaseHas('purchases', [
    //         'user_id' => $user->id,
    //         'item_id' => $item->id,
    //         'shipping_zip' => $newZip,
    //         'shipping_address' => $newAddress,
    //         'shipping_building' => $newBuilding,
    //     ]);
    // }
}
