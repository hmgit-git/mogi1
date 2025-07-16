<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class PaymentMethodFeatureTest extends TestCase
{
    use RefreshDatabase;

    // 小計画面で変更が反映される(小計画面＝右カラム) ブラウザでのテストが必要。
    public function test_payment_method_updates_summary()
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create(['email_verified_at' => now()]);
            $item = Item::factory()->create();

            $browser->loginAs($user)
                ->visit("/purchase/{$item->id}")
                ->select('payment_method', 'convenience_store')
                ->assertSee('支払方法: コンビニ支払い');
        });
    }
}
