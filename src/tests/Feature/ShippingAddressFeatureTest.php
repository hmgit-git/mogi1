<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class ShippingAddressFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_送付先住所変更後に購入画面に反映される()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();

        $newZip = '987-6543';
        $newAddress = '東京都新宿区西新宿1-1-1';
        $newBuilding = 'テストビル101';

        $response = $this->actingAs($user)->post("/purchase/{$item->id}/address", [
            'zip' => $newZip,
            'address' => $newAddress,
            'building' => $newBuilding,
        ]);

        $session = $response->baseResponse->getSession()->all();

        $response = $this->withSession($session)->actingAs($user)->get("/purchase/{$item->id}");

        $response->assertSee($newZip);
        $response->assertSee($newAddress);
        $response->assertSee($newBuilding);
    }

    public function test_購入した商品に送付先住所が紐づいて登録される()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();

        $newZip = '987-6543';
        $newAddress = '東京都渋谷区桜丘町1-1';
        $newBuilding = 'サンプルビル302';

        $this->actingAs($user)
            ->withSession([
                'shipping_zip' => $newZip,
                'shipping_address' => $newAddress,
                'shipping_building' => $newBuilding,
            ])
            ->post("/purchase/{$item->id}", [
                'payment_method' => 'convenience_store',
            ]);
            
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'shipping_zip' => $newZip,
            'shipping_address' => $newAddress,
            'shipping_building' => $newBuilding,
        ]);
    }
}
