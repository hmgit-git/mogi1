<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Purchase;

class PurchaseController extends Controller
{
    public function show(Item $item)
    {
        return view('purchase.show', [
            'item' => $item,
        ]);
    }

    public function store(Request $request, Item $item)
    {
        if ($item->is_sold) {
            return redirect()->route('items.index')->with('error', 'この商品はすでに購入されています。');
        }

        $paymentMethod = $request->input('payment_method');

        if ($paymentMethod === 'credit_card') {
            return redirect()->route('stripe.checkout', ['item_id' => $item->id]);
        }

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

    public function updateAddress(Request $request, Item $item)
    {
        session([
            'shipping_zip' => $request->zip,
            'shipping_address' => $request->address,
            'shipping_building' => $request->building,
        ]);

        return redirect()->route('purchase.show', $item->id);
    }
}
