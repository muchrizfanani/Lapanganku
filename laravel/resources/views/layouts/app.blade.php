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
        <a class="brand" href="{{ route('home') }}">Lapangan<span>Ku</span></a>
        <nav>
            @auth
                @if (auth()->user()->role === 'pemilik')
                    <a class="nav-link" href="{{ route('owner.dashboard') }}">Dashboard</a>
                    <a class="nav-link" href="{{ route('venues.index') }}">Venue</a>
                @else
                    <a class="nav-link" href="{{ route('player.dashboard') }}">Booking Saya</a>
                @endif
                <span class="user-label">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="nav-button" type="submit">Keluar</button></form>
            @else
                <a class="nav-link" href="{{ route('home') }}">Beranda</a>
                <a class="nav-button" href="{{ route('login') }}">Masuk</a>
                <a class="button primary" href="{{ route('register') }}">Daftar</a>
            @endauth
        </nav>
    </header>
    <main class="container">
        @if (session('status'))
            <div class="notice success">{{ session('status') }}</div>
        @endif
        @yield('content')
    </main>
    <footer class="footer">Temukan venue olahraga dan kelola booking bersama LapanganKu.</footer>
</body>
</html>
