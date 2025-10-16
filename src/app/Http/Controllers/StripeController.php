<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Conversation;
use Illuminate\Support\Carbon;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeController extends Controller
{
    public function checkout($item_id)
    {
        $item = Item::findOrFail($item_id);

        // 売り切れチェック
        if ($item->is_sold) {
            return redirect()->route('items.index')->with('error', 'この商品はすでに売り切れています。');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'unit_amount' => $item->price,
                    'product_data' => [
                        'name' => $item->name,
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('stripe.success', ['item_id' => $item_id]),
            'cancel_url' => route('stripe.cancel'),
        ]);

        return redirect($session->url);
    }

    public function success($item_id)
    {
        $item = Item::findOrFail($item_id);

        if ($item->is_sold) {
            return redirect()->route('items.index')->with('error', 'この商品はすでに売り切れています。');
        }

        // 1) 購入レコードの作成
        $purchase = Purchase::create([
            'user_id'           => Auth::id(),
            'item_id'           => $item->id,
            'shipping_zip'      => session('shipping_zip', Auth::user()->zip),
            'shipping_address'  => session('shipping_address', Auth::user()->address),
            'shipping_building' => session('shipping_building', Auth::user()->building),
        ]);

        // 2) 売却フラグ
        $item->update(['is_sold' => true]);

        // 3) 会話を自動作成（既にあれば再利用）
        $conv = Conversation::firstOrCreate(
            ['purchase_id' => $purchase->id],
            [
                'item_id'         => $item->id,
                'buyer_id'        => $purchase->user_id,
                'seller_id'       => $item->user_id,
                'status'          => 'open',
                'last_message_at' => Carbon::now(),
            ]
        );

        // 一時保存の住所をクリア
        session()->forget(['shipping_zip', 'shipping_address', 'shipping_building']);

        // 4) チャットへ遷移（＝「取引中の商品」にも即反映）
        return redirect()
            ->route('conversations.show', $conv)
            ->with('message', 'カード決済が完了しました。取引メッセージを始めましょう！');
    }

    public function cancel()
    {
        return redirect()->route('items.index')->with('error', 'カード決済がキャンセルされました。');
    }
}
