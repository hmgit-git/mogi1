<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\UploadedFile;

class ItemCreateFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_item()
    {
        // Arrange: ユーザーとカテゴリを準備
        $user = User::factory()->create(['email_verified_at' => now()]);
        $categories = Category::factory()->count(2)->create();

        // 商品情報入力データを用意（POSTデータは先に作る）
        $postData = [
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => '商品の説明です',
            'condition' => '新品',
            'price' => 10000,
            'categories' => $categories->pluck('id')->toArray(),
            'image' => UploadedFile::fake()->image('test.jpg'),
        ];

        // Act: ログインして商品出品画面へアクセス＆POST送信
        $response = $this->actingAs($user)->post('/sell', $postData);

        // Assert: リダイレクトされる
        $response->assertRedirect();

        // DBに正しく保存されていることを確認
        $this->assertDatabaseHas('items', [
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => '商品の説明です',
            'condition' => '新品',
            'price' => 10000,
            'user_id' => $user->id,
        ]);

        // 中間テーブルでカテゴリも紐づいているかチェック
        $item = Item::where('name', 'テスト商品')->first();
        foreach ($categories as $category) {
            $this->assertDatabaseHas('item_category', [
                'item_id' => $item->id,
                'category_id' => $category->id,
            ]);
        }
    }
}
