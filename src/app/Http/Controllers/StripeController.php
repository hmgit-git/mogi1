<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Purchase;
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

        // Stripeキー設定
        Stripe::setApiKey(config('services.stripe.secret'));

        // セッション作成
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

        // 購入レコードを作成（セッション or ユーザーの住所を保存）
        Purchase::create([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'shipping_zip' => session('shipping_zip', Auth::user()->zip),
            'shipping_address' => session('shipping_address', Auth::user()->address),
            'shipping_building' => session('shipping_building', Auth::user()->building),
        ]);

        $item->update(['is_sold' => true]);

        session()->forget(['shipping_zip', 'shipping_address', 'shipping_building']);
        return redirect()->route('items.index')->with('message', 'カード決済が完了しました！');
    }

    public function cancel()
    {
        return redirect()->route('items.index')->with('error', 'カード決済がキャンセルされました。');
    }
}
