<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>My Laravel App</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('css')
</head>


<body style="margin: 0; background-color: white; font-family: sans-serif;">

    <header style="background-color: #000; padding: 10px 20px;">
        <img src="{{ asset('css/logo.svg') }}" alt="ロゴ" style="height: 40px;">

        @auth
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="background: none; border: none; color: white; cursor: pointer;">ログアウト</button>
        </form>
        @endauth
    </header>

    {{-- メインコンテンツ --}}
    <main style="padding: 40px 20px;">
        @yield('content')
    </main>

</body>

</html>