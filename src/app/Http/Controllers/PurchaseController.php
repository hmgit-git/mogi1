<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Purchase;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;


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

        // Stripe分岐：本番・開発時のみリダイレクト
        if ($paymentMethod === 'credit_card' && !app()->environment('testing')) {
            return redirect()->route('stripe.checkout', ['item_id' => $item->id]);
        }

        // DB保存（テスト時はこちらが実行される）
        Purchase::create([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'shipping_zip' => session('shipping_zip', Auth::user()->zip),
            'shipping_address' => session('shipping_address', Auth::user()->address),
            'shipping_building' => session('shipping_building', Auth::user()->building),
        ]);

        $item->update([
            'is_sold' => true,
        ]);

        session()->forget(['shipping_zip', 'shipping_address', 'shipping_building']);

        return redirect()->route('items.index')->with('message', '購入が完了しました！');
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
