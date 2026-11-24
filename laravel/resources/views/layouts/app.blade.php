<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'LapanganKu') · LapanganKu</title>
    <link rel="stylesheet" href="{{ asset('css/lapanganku.css') }}">
</head>
<body>
    <header class="topbar">
        <a class="brand" href="{{ route('venues.index') }}">Lapangan<span>Ku</span></a>
        <nav>
            @auth
                <span class="user-label">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="nav-button" type="submit">Keluar</button></form>
            @else
                <a class="nav-button" href="{{ route('login') }}">Masuk Pemilik</a>
            @endauth
        </nav>
    </header>
    <main class="container">
        @if (session('status'))
            <div class="notice success">{{ session('status') }}</div>
        @endif
        @yield('content')
    </main>
    <footer class="footer">Kelola venue dan booking dengan LapanganKu.</footer>
</body>
</html>
