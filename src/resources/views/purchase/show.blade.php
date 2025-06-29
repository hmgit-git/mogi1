@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="purchase-container">
    <h2>購入確認</h2>

    <div class="item-details">
        <img src="{{ $item->image_path }}" alt="{{ $item->name }}" class="item-image">

        <div class="item-info">
            <p><strong>商品名：</strong>{{ $item->name }}</p>
            <p><strong>価格：</strong>¥{{ number_format($item->price) }}</p>
            <p><strong>お届け先住所：</strong>
                {{ session('shipping_zip', Auth::user()->zip) }}
                {{ session('shipping_address', Auth::user()->address) }}
                {{ session('shipping_building', Auth::user()->building) }}
            </p>

            <a href="{{ route('purchase.address.edit', $item->id) }}">送付先を変更する</a>
        </div>
    </div>

    {{-- 🔽 支払い方法プルダウン追加 --}}
    <form method="POST" action="{{ route('purchase.store', $item->id) }}">
        @csrf

        <div class="payment-method">
            <label for="payment_method"><strong>支払方法：</strong></label>
            <select id="payment_method" name="payment_method" onchange="updatePaymentMethod()">
                <option value="convenience_store">コンビニ支払い</option>
                <option value="credit_card">カード支払い</option>
            </select>
        </div>

        <div id="payment_info" class="payment-info">
            支払方法: コンビニ支払い
        </div>

        <button type="submit" class="purchase-button">購入を確定</button>
    </form>
</div>

<script>
    function updatePaymentMethod() {
        const value = document.getElementById('payment_method').value;
        const label = value === 'credit_card' ? 'カード支払い' : 'コンビニ支払い';
        document.getElementById('payment_info').innerText = '支払方法: ' + label;
    }
</script>
@endsection