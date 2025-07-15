<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UserProfileFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_プロフィールページに必要な情報が表示される()
    {
        // 1. ユーザー作成（プロフィール画像あり）
        Storage::fake('public');
        $user = User::factory()->create([
            'username' => 'テストユーザー',
            'profile_image' => 'images/profiles/test.jpg', // 画像パスは正しい場所に合わせて
        ]);

        // 画像ファイルを仮置き
        Storage::disk('public')->put('images/profiles/test.jpg', UploadedFile::fake()->image('test.jpg'));

        // 2. 出品商品を3つ作成
        $listedItems = Item::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        // 3. 購入商品を2つ作成（別ユーザーの商品を購入）
        $seller = User::factory()->create();
        $purchasedItems = Item::factory()->count(2)->create([
            'user_id' => $seller->id,
            'is_sold' => true,
        ]);
        foreach ($purchasedItems as $item) {
            $user->purchases()->create(['item_id' => $item->id]);
        }

        // 4. 出品商品タブの確認
        $responseListed = $this->actingAs($user)->get('/mypage?tab=listed');
        $responseListed->assertStatus(200);
        $responseListed->assertSee('テストユーザー');
        $responseListed->assertSee('images/profiles/test.jpg'); // 画像パス確認
        foreach ($listedItems as $item) {
            $responseListed->assertSee($item->name);
        }

        // 5. 購入商品タブの確認
        $responsePurchased = $this->actingAs($user)->get('/mypage?tab=purchased');
        $responsePurchased->assertStatus(200);
        foreach ($purchasedItems as $item) {
            $responsePurchased->assertSee($item->name);
        }
    }
}
