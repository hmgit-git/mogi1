<?php

namespace Database\Factories;

use App\Models\{Conversation, Item, User};
use Illuminate\Database\Eloquent\Factories\Factory;

class ConversationFactory extends Factory
{
    protected $model = Conversation::class;

    public function definition()
    {
        // 出品者・購入者・商品
        $seller = User::factory()->create();
        $buyer  = User::factory()->create();
        $item   = Item::factory()->create(['user_id' => $seller->id]);

        return [
            'item_id'         => $item->id,
            'buyer_id'        => $buyer->id,
            'seller_id'       => $seller->id,
            'status'          => 'open',
            'purchase_id'     => null,
            'last_message_at' => now(),
        ];
    }

    /**
     * 購入作成して会話に紐付け
     */
    public function withPurchase()
    {
        return $this->afterCreating(function (Conversation $conv) {
            $cols = \Schema::getColumnListing('purchases');

            $data = [
                'user_id' => $conv->buyer_id,
                'item_id' => $conv->item_id,
            ];
            $price = optional($conv->item)->price ?? 15000;
            if (in_array('price', $cols, true))        $data['price'] = $price;
            elseif (in_array('amount', $cols, true))   $data['amount'] = $price;
            elseif (in_array('total_price', $cols, true)) $data['total_price'] = $price;
            elseif (in_array('total', $cols, true))      $data['total'] = $price;
            if (in_array('status', $cols, true))         $data['status'] = 'paid';

            \App\Models\Purchase::unguard();
            $p = \App\Models\Purchase::create($data);
            \App\Models\Purchase::reguard();

            $conv->update(['purchase_id' => $p->id]);
        });
    }

    /**
     * 完了済み
     */
    public function completed()
    {
        return $this->state(fn() => ['status' => 'completed']);
    }
}
