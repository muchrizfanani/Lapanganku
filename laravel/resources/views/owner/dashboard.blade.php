@extends('layouts.app')

@section('title', 'Dashboard Pemilik')

@section('content')
    <div class="page-heading">
        <div>
            <p class="eyebrow">PANEL MITRA VENUE</p>
            <h1>Dashboard Pengelola Venue</h1>
            <p class="muted">Halo, {{ auth()->user()->name }}. Kelola booking yang masuk ke venue.</p>
        </div>
        <a class="button primary" href="{{ route('venues.index') }}">Kelola Venue</a>
    </div>

    <section class="owner-metrics">
        <article class="owner-metric">
            <span>Total booking</span>
            <strong>{{ $bookings->count() }}</strong>
        </article>
        <article class="owner-metric pending-metric">
            <span>Perlu konfirmasi</span>
            <strong>{{ $pendingCount }}</strong>
        </article>
        <article class="owner-metric confirmed-metric">
            <span>Booking diterima</span>
            <strong>{{ $confirmedCount }}</strong>
        </article>
        <article class="owner-metric">
            <span>Estimasi pembayaran COD</span>
            <strong>Rp {{ number_format($estimatedRevenue, 0, ',', '.') }}</strong>
        </article>
    </section>

    <section class="booking-history owner-bookings">
        <div class="section-heading">
            <p class="eyebrow">AKTIVITAS VENUE</p>
            <h2>Daftar Booking Masuk</h2>
            <p class="muted">Terima atau tolak booking yang masih menunggu konfirmasi.</p>
        </div>
        @forelse ($bookings as $booking)
            @php
                $statusLabel = match ($booking->status) {
                    'confirmed' => 'Diterima',
                    'rejected' => 'Ditolak',
                    'completed' => 'Selesai',
                    default => 'Menunggu konfirmasi',
                };
            @endphp
            <article class="owner-booking-card">
                <div class="owner-booking-heading">
                    <div>
                        <span class="booking-id">{{ $booking->legacy_id ?: 'Booking #'.$booking->id }}</span>
                        <h3>{{ $booking->player_name }}</h3>
                        <p class="muted">{{ $booking->player_phone }}</p>
                    </div>
                    <span class="badge {{ $booking->status }}">{{ $statusLabel }}</span>
                </div>
                <div class="owner-booking-details">
                    <div><span>Venue</span><strong>{{ $booking->venue_name }}</strong></div>
                    <div><span>Olahraga</span><strong>{{ $booking->sport }}</strong></div>
                    <div><span>Tanggal main</span><strong>{{ $booking->booking_date->format('d M Y') }}</strong></div>
                    <div><span>Slot jam</span><strong>{{ implode(', ', $booking->slots ?? []) }}</strong></div>
                    <div><span>Total pembayaran</span><strong>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</strong></div>
                    <div><span>Metode</span><strong>{{ $booking->payment_method }}</strong></div>
                </div>
                @if ($booking->status === 'pending')
                    <div class="owner-booking-actions">
                        <form method="POST" action="{{ route('bookings.status', $booking) }}">
                            @csrf
                            <input type="hidden" name="status" value="confirmed">
                            <button class="button primary" type="submit">✓ Terima Booking</button>
                        </form>
                        <form method="POST" action="{{ route('bookings.status', $booking) }}">
                            @csrf
                            <input type="hidden" name="status" value="rejected">
                            <button class="button danger" type="submit">✕ Tolak</button>
                        </form>
                    </div>
                @else
                    <p class="muted booking-processed">Booking telah diproses.</p>
                @endif
            </article>
        @empty
            <div class="empty-state">Belum ada booking yang masuk.</div>
        @endforelse
    </section>
@endsection
