<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;

class PurchaseFeatureTest extends TestCase
{
    use RefreshDatabase;

    // 「購入する」ボタンを押下すると購入が完了する
    public function test_ログインユーザーは商品を購入できる()    {
        // Arrange: ユーザーと未購入の商品を作成
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $item = Item::factory()->create([
            'is_sold' => false,
        ]);

        // Act: ログインして購入リクエストを送信（支払方法: クレジットカード）
        $response = $this->actingAs($user)->post("/purchase/{$item->id}", [
            'payment_method' => 'credit_card',
        ]);

        // Assert: リダイレクトされること（購入後トップページなどへ）
        $response->assertRedirect();

        // 購入データがDBに保存されていること
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // 商品が「売却済み（is_sold = true）」になっていることを確認
        $this->assertTrue((bool) $item->fresh()->is_sold);
    }

    // 購入した商品は商品一覧画面にて「sold」と表示される
    public function test_購入済み商品は一覧画面で_sold_と表示される()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        // 出品者は別のユーザーにする
        $seller = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'is_sold' => false,
        ]);

        // 購入処理
        $this->actingAs($user)->post("/purchase/{$item->id}", [
            'payment_method' => 'credit_card',
        ]);

        // 商品一覧にアクセス
        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee('Sold');
    }

    //「プロフィール/購入した商品一覧」に追加されている
    public function test_購入した商品がプロフィール画面に表示される()
    {
        // Arrange: 購入者と出品者を作成
        $buyer = User::factory()->create(['email_verified_at' => now()]);
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'is_sold' => false,
            'name' => 'テスト商品',
        ]);

        // Act: 購入処理を実行
        $this->actingAs($buyer)->post("/purchase/{$item->id}", [
            'payment_method' => 'credit_card',
        ]);

        // Assert: プロフィールページにアクセスし、商品が表示されていることを確認
        $response = $this->actingAs($buyer)->get('/mypage?tab=purchased');

        $response->assertStatus(200);
        $response->assertSee('テスト商品'); // 商品名が表示されていること
    }
}
