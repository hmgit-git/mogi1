<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COACHTECHフリマ</title>
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/items.css') }}">
    @yield('css')
</head>
@yield('js')

<body style="margin: 0; background-color: white; font-family: sans-serif;">

    <header class="app-header">
        <a href="{{ route('items.index') }}">
            <img src="{{ asset('css/logo.svg') }}" alt="ロゴ" class="logo">
        </a>

        <!-- ハンバーガーメニュー（スマホ用） -->
        <div class="hamburger" onclick="toggleMenu()">☰</div>



        <form action="{{ route('items.index') }}" method="GET" class="search-form">
            <input type="text" name="keyword" placeholder="なにをお探しですか？" value="{{ request('keyword') }}">
            @if (request('tab'))
            <input type="hidden" name="tab" value="{{ request('tab') }}">
            @endif
        </form>

        <!-- PC用メニュー -->
        <nav class="pc-menu">
            @auth
            <a href="{{ route('mypage') }}">マイページ</a>

            <form method="POST" action="{{ route('logout') }}">
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

    <!-- スマホ用メニュー（初期は非表示） -->
    <div class="mobile-menu" id="mobileMenu">
        @auth
        <a href="{{ route('mypage') }}">マイページ</a>
        <a href="{{ route('items.create') }}" class="sell-button">出品</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">ログアウト</button>
        </form>
        @else
        <a href="{{ route('login') }}">ログイン</a>
        <a href="{{ route('mypage') }}">マイページ</a>
        <a href="{{ route('login') }}" class="sell-button">出品</a>
        @endauth
    </div>



    <main class="main-content">
        @yield('content')
    </main>

</body>

<script>
    function toggleMenu() {
        document.getElementById('mobileMenu').classList.toggle('show');
    }
</script>


</html>