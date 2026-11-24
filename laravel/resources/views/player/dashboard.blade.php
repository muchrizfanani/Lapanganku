@extends('layouts.app')

@section('title', 'Beranda Pemain')

@section('content')
    <div class="page-heading">
        <div>
            <p class="eyebrow">AREA PEMAIN</p>
            <h1>Booking Saya</h1>
            <p class="muted">Halo, {{ auth()->user()->name }}. Cari venue dan pantau status booking-mu.</p>
        </div>
        <a class="button secondary" href="{{ route('home') }}#venue">Cari Venue</a>
    </div>

    <section class="venue-list">
        @forelse ($venues as $venue)
            <article class="venue-card">
                <div class="venue-mark">{{ strtoupper(substr($venue->sport, 0, 1)) }}</div>
                <div class="venue-info">
                    <div class="venue-title-row">
                        <h2>{{ $venue->name }}</h2>
                        <span class="badge {{ $venue->status }}">{{ ucfirst($venue->status) }}</span>
                    </div>
                    <p>{{ $venue->sport }} · {{ $venue->location }}</p>
                    <p class="muted">{{ $venue->address }}</p>
                    <strong class="price">Rp {{ number_format($venue->price, 0, ',', '.') }} <small>/ jam</small></strong>
                </div>
                <div class="actions">
                    <a class="button primary" href="{{ route('bookings.create', $venue) }}">Cek Slot &amp; Booking</a>
                </div>
            </article>
        @empty
            <div class="empty-state">Belum ada venue yang tersedia.</div>
        @endforelse
    </section>

    <section class="booking-history">
        <div class="section-heading">
            <div>
                <p class="eyebrow">AKTIVITAS</p>
                <h2>Booking Saya</h2>
            </div>
        </div>
        @forelse ($bookings as $booking)
            <article class="booking-row">
                <div>
                    <strong>{{ $booking->venue_name }}</strong>
                    <p class="muted">{{ $booking->booking_date->format('d M Y') }} · {{ implode(', ', $booking->slots ?? []) }}</p>
                    <p class="muted">{{ $booking->player_name }} · {{ $booking->player_phone }}</p>
                    <p class="price">Rp {{ number_format($booking->total_price, 0, ',', '.') }} · {{ $booking->payment_method }}</p>
                </div>
                <div class="booking-row-status">
                    @php
                        $statusLabel = match ($booking->status) {
                            'confirmed' => 'Dikonfirmasi',
                            'rejected' => 'Ditolak',
                            'completed' => 'Selesai',
                            default => 'Menunggu konfirmasi',
                        };
                    @endphp
                    <span class="badge {{ $booking->status }}">{{ $statusLabel }}</span>
                    <small>{{ $booking->legacy_id ?: 'Booking #'.$booking->id }}</small>
                </div>
            </article>
        @empty
            <div class="empty-state">Belum ada riwayat booking untuk akun ini.</div>
        @endforelse
    </section>
@endsection
