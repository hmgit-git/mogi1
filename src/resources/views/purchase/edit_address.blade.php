@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="profile-container"> {{-- ←ここを追加！ --}}
    <h2>送付先住所を変更</h2>

    <form method="POST" action="{{ route('purchase.address.update', $item->id) }}">
        @csrf
        <div class="form-group">
            <label>郵便番号</label>
            <input type="text" name="zip" value="{{ old('zip', $user->zip) }}">
        </div>
        <div class="form-group">
            <label>住所</label>
            <input type="text" name="address" value="{{ old('address', $user->address) }}">
        </div>
        <div class="form-group">
            <label>建物名</label>
            <input type="text" name="building" value="{{ old('building', $user->building) }}">
        </div>
        <button type="submit" class="submit-button">この住所で戻る</button>
    </form>
</div> 
@endsection