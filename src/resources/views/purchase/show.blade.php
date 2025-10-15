@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<form method="POST" action="{{ route('purchase.store', $item->id) }}" novalidate>
    @csrf

    <div class="purchase-container">
        <!-- 左カラム -->
        <div class="item-details">
            <div class="item-top">
                <img src="{{ asset($item->image_path) }}" alt="{{ $item->name }}" class="item-image">

                <div class="item-info">
                    <p><strong>商品名：</strong>{{ $item->name }}</p>
                    <p><strong>価格：</strong>¥{{ number_format($item->price) }}</p>
                </div>
            </div>

            <!-- 支払方法セクション -->
            <div class="payment-method-wrapper">
                <div class="payment-method">
                    <label for="payment_method"><strong>支払方法：</strong></label>
                    <select id="payment_method" name="payment_method" required onchange="updatePaymentMethod()">
                        <option value="" disabled selected>選択してください</option>
                        <option value="convenience_store">コンビニ支払い</option>
                        <option value="credit_card">カード支払い</option>
                    </select>
                    @error('payment_method')
                    <div class="error" style="color: red;">{{ $message }}</div>
                    @enderror

                </div>
            </div>

            <!-- お届け先住所表示 -->
            <div class="shipping-address">
                <p><strong>お届け先住所：</strong>
                    {{ session('shipping_zip', Auth::user()->zip) }}<br>
                    {{ session('shipping_address', Auth::user()->address) }}<br>
                    {{ session('shipping_building', Auth::user()->building) }}
                </p>
                <a href="{{ route('purchase.address.edit', $item->id) }}">送付先を変更する</a>
            </div>



        </div>

        <!-- 右カラム：購入ボタン -->
        <div class="purchase-summary">
            <div class="purchase-summary-info">
                <p><strong>商品代金：</strong>¥{{ number_format($item->price) }}</p>
                <p id="payment_info">
                    <strong>支払方法：</strong>
                    @if (session('payment_method') === 'credit_card')
                    カード支払い
                    @elseif (session('payment_method') === 'convenience_store')
                    コンビニ支払い
                    @else
                    未選択
                    @endif
                </p>
            </div>
            <button type="submit" class="purchase-button">購入を確定</button>
        </div>

    </div>

</form>

<script>
    function updatePaymentMethod() {
        const value = document.getElementById('payment_method').value;
        const label = value === 'credit_card' ? 'カード支払い' : 'コンビニ支払い';
        document.getElementById('payment_info').innerText = '支払方法: ' + label;
    }
</script>
@endsection