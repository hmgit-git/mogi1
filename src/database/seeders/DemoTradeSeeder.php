<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{User, Item, Conversation, Message, Review};

class DemoTradeSeeder extends Seeder
{
    public function run()
    {
        // 1) 固定の購入者/出品者を用意（ログインしやすいように）
        $buyer = User::first() ?: User::factory()->create([
            'email'              => 'buyer@example.test',
            'username'           => 'buyer_demo',
            'email_verified_at'  => now(),
            'password'           => bcrypt('password'),
        ]);
        $seller = User::where('id', '<>', $buyer->id)->first() ?: User::factory()->create([
            'email'              => 'seller@example.test',
            'username'           => 'seller_demo',
            'email_verified_at'  => now(),
            'password'           => bcrypt('password'),
        ]);

        // 2) 出品者の商品
        $item = Item::firstOrCreate(
            ['user_id' => $seller->id, 'name' => 'テスト商品'],
            [
                'description' => 'ダミー説明',
                'price'       => 15000,
                'image_path'  => 'storage/images/clock.jpg',
                'condition'   => '良好',
                'is_sold'     => 0,
            ]
        );

        // 3) 会話（open） + 購入紐付け
        $convOpen = Conversation::factory()->withPurchase()->create([
            'item_id'  => $item->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'status'   => 'open',
        ]);

        // メッセージを2〜4通
        Message::factory()->forConversation($convOpen)->count(2)->create();
        $convOpen->update(['last_message_at' => now()]);

        // 4) 会話（completed）+ レビュー作成済み（平均☆の見た目確認用）
        $convDone = Conversation::factory()->withPurchase()->completed()->create([
            'item_id'  => $item->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
        ]);

        // 双方1通ずつメッセージ
        Message::factory()->forConversation($convDone)->count(2)->create();

        // レビュー（購入者→出品者）
        $purchase = \App\Models\Purchase::find($convDone->purchase_id);
        if ($purchase) {
            Review::create([
                'purchase_id' => $purchase->id,
                'reviewer_id' => $buyer->id,
                'reviewee_id' => $seller->id,
                'rating'      => 4,
                'comment'     => 'スムーズな取引でした！',
            ]);
        }

        $this->command->info('Demo data seeded: buyer=' . $buyer->email . ' / seller=' . $seller->email);
        $this->command->info('Open conversation id=' . $convOpen->id . ' / Completed conversation id=' . $convDone->id);
    }
}
