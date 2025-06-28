<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>COACHTECHフリマ</title>
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/items.css') }}">
    @yield('css')
</head>


<body style="margin: 0; background-color: white; font-family: sans-serif;">

    <header class="app-header">
        <img src="{{ asset('css/logo.svg') }}" alt="ロゴ">

        <form action="{{ route('items.index') }}" method="GET" class="search-form">
            <input type="text" name="keyword" placeholder="なにをお探しですか？" value="{{ request('keyword') }}">
            @if (request('tab'))
            <input type="hidden" name="tab" value="{{ request('tab') }}">
            @endif
        </form>


        <nav>
            @auth
            <a href="{{ route('mypage') }}">マイページ</a>

            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit">ログアウト</button>
            </form>

            <a href="{{ route('items.create') }}" class="sell-button">出品</a>
            @else
            <a href="{{ route('login') }}">ログイン</a>
            <a href="{{ route('mypage') }}">マイページ</a>
            <a href="{{ route('login') }}" class="sell-button">出品</a>
            @endauth
        </nav>
    </header>

    <main class="main-content">
        @yield('content')
    </main>


</body>

</html>