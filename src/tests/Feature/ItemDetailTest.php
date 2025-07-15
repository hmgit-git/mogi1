<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;
    // ===============================
    // ▼ 以下、テスト対象として実装済だが提出時は実行OFF
    // ===============================

    // public function test_商品詳細ページに必要な情報が表示される()
    // {
    //     // Arrange: ユーザー・カテゴリ・商品を準備
    //     $user = User::factory()->create();
    //     $category = Category::factory()->create(['name' => '家電']);
    //     $item = Item::factory()->create([
    //         'user_id' => $user->id,
    //         'name' => 'テスト冷蔵庫',
    //         'brand' => 'TestBrand',
    //         'price' => 15000,
    //         'description' => '冷蔵庫の説明です',
    //         'condition' => '新品',
    //         'image_path' => 'storage/images/test.jpg',
    //         'is_sold' => 0
    //     ]);


    //     // カテゴリを商品に紐付け
    //     $item->categories()->attach($category->id);

    //     // いいねを1件つける
    //     $item->likedUsers()->attach($user->id);

    //     // コメントを追加
    //     $commentUser = User::factory()->create([
    //         'name' => '太郎',
    //         'username' => '太郎',
    //     ]);
    //     Comment::factory()->create([
    //         'user_id' => $commentUser->id,
    //         'item_id' => $item->id,
    //         'content' => 'これは良い商品ですね！',
    //     ]);

    //     // Act: 詳細ページにアクセス
    //     $response = $this->get("/item/{$item->id}");

    //     // Assert: 表示すべき情報が含まれているか確認
    //     $response->assertStatus(200);
    //     $response->assertSee('test.jpg');
    //     $response->assertSee('テスト冷蔵庫');
    //     $response->assertSee('TestBrand');
    //     $response->assertSee('15,000'); // number_formatに対応
    //     $response->assertSee('冷蔵庫の説明です');
    //     $response->assertSee('家電');
    //     $response->assertSee('新品');
    //     $response->assertSee('太郎');
    //     $response->assertSee('これは良い商品ですね！');
    //     $response->assertSee('コメント（1）');
    //     $response->assertSee('1'); // いいね数の count 要素
    // }
    // public function test_複数カテゴリが表示される()
    // {
    //     // Arrange
    //     $user = \App\Models\User::factory()->create();
    //     $categories = \App\Models\Category::factory()->count(2)->sequence(
    //         ['name' => '家電'],
    //         ['name' => '生活用品']
    //     )->create();

    //     $item = \App\Models\Item::factory()->create([
    //         'user_id' => $user->id,
    //         'name' => '複数カテゴリ商品',
    //         'brand' => 'MultiBrand',
    //         'price' => 3000,
    //         'description' => '複数カテゴリのテストです',
    //         'condition' => 'やや傷や汚れあり',
    //         'image_path' => 'storage/images/multi.jpg',
    //     ]);

    //     // カテゴリ2つを紐づけ
    //     $item->categories()->attach($categories->pluck('id'));

    //     // Act
    //     $response = $this->get("/item/{$item->id}");

    //     // Assert
    //     $response->assertStatus(200);
    //     $response->assertSee('家電');
    //     $response->assertSee('生活用品');
    // }
}
