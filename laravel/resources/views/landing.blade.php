<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LapanganKu · Booking Lapangan Olahraga</title>
    <meta name="description" content="Temukan venue futsal, badminton, dan basket di LapanganKu.">
    <link rel="stylesheet" href="{{ asset('css/lapanganku.css') }}">
</head>
<body class="landing-body">
    <header class="landing-header">
        <div class="landing-nav">
            <a class="brand" href="{{ route('home') }}"><span class="brand-icon">⚽</span>Lapangan<span>Ku</span></a>
            <nav class="landing-links">
                <a href="#kategori">Olahraga</a>
                <a href="#venue">Cari Venue</a>
            </nav>
            <div class="landing-actions">
                @auth
                    <a class="button secondary" href="{{ auth()->user()->role === 'pemilik' ? route('venues.index') : route('player.dashboard') }}">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="button danger" type="submit">Keluar</button></form>
                @else
                    <a class="button secondary landing-register" href="{{ route('register') }}">Daftar</a>
                    <a class="button login-button" href="{{ route('login') }}">Masuk</a>
                @endauth
            </div>
        </div>
    </header>

    <main>
        <section class="landing-hero">
            <div class="landing-hero-content">
                <span class="landing-pill">⚡ Booking mudah · Bayar langsung di venue</span>
                <h1>Cari &amp; Booking Lapangan Olahraga <span>Tanpa Ribet</span></h1>
                <p>Temukan venue futsal, badminton, dan basket. Pilih lokasi yang cocok dan rencanakan permainanmu.</p>

                <form class="landing-search" method="GET" action="{{ route('home') }}#venue">
                    <label>
                        <span>Jenis olahraga</span>
                        <select name="sport">
                            <option value="all" @selected($selectedSport === 'all')>Semua olahraga</option>
                            <option value="futsal" @selected($selectedSport === 'futsal')>⚽ Futsal</option>
                            <option value="badminton" @selected($selectedSport === 'badminton')>🏸 Badminton</option>
                            <option value="basket" @selected($selectedSport === 'basket')>🏀 Basket</option>
                        </select>
                    </label>
                    <label>
                        <span>Kota atau venue</span>
                        <input type="search" name="location" value="{{ $location }}" placeholder="Contoh: Bandung">
                    </label>
                    <button class="button primary" type="submit">Cari Venue <span aria-hidden="true">→</span></button>
                </form>

                <div class="landing-stats">
                    <div><strong>{{ $venueCount }}</strong><span>Venue terdaftar</span></div>
                    <div><strong>3</strong><span>Cabang olahraga</span></div>
                    <div><strong>{{ $averageRating ? number_format((float) $averageRating, 1) : '—' }}★</strong><span>Rating venue</span></div>
                </div>
            </div>
            <div class="hero-decoration" aria-hidden="true">
                <span>⚽</span><span>🏸</span><span>🏀</span>
            </div>
        </section>

        <section class="landing-section" id="kategori">
            <div class="section-title">
                <p class="eyebrow">PILIH PERMAINANMU</p>
                <h2>Cari berdasarkan olahraga</h2>
                <p class="muted">Pilih kategori dan temukan venue yang sesuai.</p>
            </div>
            <div class="category-grid">
                @foreach ($categories as $category)
                    <a class="category-tile" href="{{ route('home', ['sport' => $category['sport']]) }}#venue">
                        <span class="category-icon">{{ $category['icon'] }}</span>
                        <h3>{{ $category['name'] }}</h3>
                        <p>{{ $category['description'] }}</p>
                        <span class="category-link">Lihat venue <span aria-hidden="true">→</span></span>
                    </a>
                @endforeach
            </div>
        </section>

        <section class="landing-section venue-section" id="venue">
            <div class="section-title section-title-row">
                <div>
                    <p class="eyebrow">DIREKTORI LAPANGANKU</p>
                    <h2>Venue pilihan untukmu</h2>
                    <p class="muted">Daftar venue diambil dari database LapanganKu.</p>
                </div>
                @if ($selectedSport !== 'all' || $location !== '')
                    <a class="button secondary" href="{{ route('home') }}#venue">Reset pencarian</a>
                @endif
            </div>
            <div class="landing-venue-grid">
                @forelse ($venues as $venue)
                    @php
                        $statusLabel = match ($venue->status) {
                            'available' => 'Tersedia',
                            'limited' => 'Terbatas',
                            'full' => 'Penuh',
                            default => ucfirst($venue->status),
                        };
                    @endphp
                    <article class="landing-venue-card">
                        <div class="venue-art">
                            <span>{{ $venue->sport_badge ?: $venue->sport }}</span>
                            <span class="badge {{ $venue->status }}">{{ $statusLabel }}</span>
                        </div>
                        <div class="landing-venue-info">
                            <p class="venue-location">{{ $venue->location }} · {{ $venue->address }}</p>
                            <h3>{{ $venue->name }}</h3>
                            <p class="venue-facilities">{{ $venue->facilities ?: 'Informasi fasilitas belum tersedia.' }}</p>
                            <div class="landing-venue-bottom">
                                <strong>Rp {{ number_format($venue->price, 0, ',', '.') }} <small>/ jam</small></strong>
                                <span>⭐ {{ $venue->rating ? number_format((float) $venue->rating, 1) : '—' }} ({{ $venue->reviews }})</span>
                            </div>
                            @auth
                                @if (auth()->user()->role === 'pemain')
                                    <a class="button primary venue-book-button" href="{{ route('bookings.create', $venue) }}">Cek Slot &amp; Booking</a>
                                @else
                                    <a class="button secondary venue-book-button" href="{{ route('venues.index') }}">Kelola Venue</a>
                                @endif
                            @else
                                <a class="button primary venue-book-button" href="{{ route('bookings.create', $venue) }}">Cek Slot &amp; Booking</a>
                            @endauth
                        </div>
                    </article>
                @empty
                    <div class="empty-state landing-empty">Belum ada venue yang cocok dengan pencarian ini.</div>
                @endforelse
            </div>
        </section>

        <section class="owner-callout">
            <div>
                <p class="eyebrow">PUNYA VENUE OLAHRAGA?</p>
                <h2>Kelola venue-mu bersama LapanganKu</h2>
                <p>Masuk sebagai pemilik untuk mengelola informasi venue dan booking.</p>
            </div>
            <a class="button callout-button" href="{{ route('login') }}">Masuk Pemilik</a>
        </section>
    </main>

    <footer class="landing-footer">
        <a class="brand" href="{{ route('home') }}">Lapangan<span>Ku</span></a>
        <span>Temukan tempat bermain, dekat denganmu.</span>
    </footer>
</body>
</html>
