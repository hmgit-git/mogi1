@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item-create.css') }}">
@endsection

@section('content')
<div class="sell-form">
    <!-- タイトル -->
    <h2 class="sell-title">商品の出品</h2>

    <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf

        <!-- 商品画像 -->
        <div>
            <label class="image-label">商品画像</label>
            <div class="image-upload">
                <label class="image-button">
                    画像を選択する
                    <input type="file" name="image" style="display: none;">
                    @error('image')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                </label>
            </div>
        </div>

        <!-- 商品の詳細 -->
        <h2 class="section-title">商品の詳細</h2>
        <div class="section-divider"></div>

        <!-- カテゴリー -->
        <div class="form-block">
            <label class="field-label">カテゴリー</label>
            <div class="category-buttons">
                @foreach($categories as $category)
                <label class="category-button">
                    <input type="checkbox" name="categories[]" value="{{ $category->id }}" style="display: none;">
                    {{ $category->name }}
                </label>
                @endforeach
            </div>
            @error('categories')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- 商品の状態 -->
        <div class="form-block">
            <label class="form-label">商品の状態</label><br>
            <select name="condition" class="condition-select">

                <option value="">選択してください</option>
                @foreach ($conditions as $condition)
                <option value="{{ $condition }}">{{ $condition }}</option>
                @endforeach
            </select>
            @error('condition')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>


        <!-- 商品名と説明 -->
        <div class="form-section">
            <h3 class="section-title">商品名と説明</h3>
        </div>

        <!-- 商品名 -->
        <div class="form-block">
            <label class="form-label">商品名</label><br>
            <input type="text" name="name" class="text-input" value="{{ old('name') }}">
            @error('name')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- ブランド名 -->
        <div class="form-block">
            <label class="form-label">ブランド名</label><br>
            <input type="text" name="brand" class="text-input" value="{{ old('brand') }}">
        </div>

        <!-- 商品の説明 -->
        <div class="form-block">
            <label class="form-label">商品の説明</label><br>
            <textarea name="description" class="textarea-input" rows="5">{{ old('description') }}</textarea>
            @error('description')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- 販売価格 -->
        <div class="form-block">
            <label class="form-label">販売価格</label><br>
            <div class="price-input-wrapper">
                <span class="yen-inside">￥</span>
                <input type="number" name="price" class="text-input with-yen" value="{{ old('price') }}">
            </div>
            @error('price')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>


        <!-- 出品ボタン -->
        <button type="submit" class="submit-btn">出品する</button>

    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categoryButtons = document.querySelectorAll('.category-button');

        categoryButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const input = this.querySelector('input');
                input.checked = !input.checked;
                this.classList.toggle('selected', input.checked);
            });
        });
    });
</script>

@endsection