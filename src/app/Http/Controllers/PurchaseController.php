<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Purchase;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;
use App\Models\Conversation;
use Illuminate\Support\Carbon;


class PurchaseController extends Controller
{
    public function show(Item $item)
    {
        return view('purchase.show', [
            'item' => $item,
        ]);
    }

    public function store(PurchaseRequest $request, Item $item)
    {
        if ($item->is_sold) {
            return redirect()->route('items.index')->with('error', 'この商品はすでに購入されています。');
        }

        $paymentMethod = $request->input('payment_method');

        // クレカ / コンビニ払いは Stripe 側で購入確定 → success 側で同様の処理を入れること（※下にメモ）
        if (in_array($paymentMethod, ['credit_card', 'convenience_store']) && !app()->environment('testing')) {
            return redirect()->route('stripe.checkout', ['item_id' => $item->id]);
        }

        // 1) 購入レコード作成
        $purchase = Purchase::create([
            'user_id'          => Auth::id(),
            'item_id'          => $item->id,
            'shipping_zip'     => session('shipping_zip', Auth::user()->zip),
            'shipping_address' => session('shipping_address', Auth::user()->address),
            'shipping_building' => session('shipping_building', Auth::user()->building),
        ]);

        // 2) 商品を売却済みに
        $item->update(['is_sold' => true]);

        // 3) 取引用の会話を自動生成（なければ作る）
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

        // 入力一時保存のクリア
        session()->forget(['shipping_zip', 'shipping_address', 'shipping_building']);

        // 4) 取引チャットへ遷移（＝「取引中の商品」にも即反映される）
        return redirect()->route('conversations.show', $conv)
            ->with('message', '購入が完了しました。取引メッセージを始めましょう！');
    }

    public function editAddress(Item $item)
    {
        $user = Auth::user();
        return view('purchase.edit_address', compact('item', 'user'));
    }

    public function updateAddress(AddressRequest $request, Item $item)
    {
        session([
            'shipping_zip' => $request->zip,
            'shipping_address' => $request->address,
            'shipping_building' => $request->building,
        ]);


        return redirect()->route('purchase.show', ['item' => $item->id]);
    }
}
