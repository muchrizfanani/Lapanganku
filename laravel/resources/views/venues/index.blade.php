@extends('layouts.app')

@section('title', 'Manajemen Venue')

@section('content')
    <div class="page-heading">
        <div>
            <p class="eyebrow">DASHBOARD PEMILIK</p>
            <h1>Manajemen Venue</h1>
            <p class="muted">Kelola venue olahraga dan informasi lapangannya.</p>
        </div>
        <a class="button primary" href="{{ route('venues.create') }}">+ Tambah Venue</a>
    </div>

    @if ($errors->any())
        <div class="notice error">{{ $errors->first() }}</div>
    @endif

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
                    <a class="button secondary" href="{{ route('venues.show', $venue) }}">Detail</a>
                    <a class="button secondary" href="{{ route('venues.edit', $venue) }}">Edit</a>
                    <form method="POST" action="{{ route('venues.destroy', $venue) }}" onsubmit="return confirm('Hapus venue ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="button danger" type="submit">Hapus</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="empty-state">Belum ada venue. Tambahkan venue pertama untuk mulai mengelola lapangan.</div>
        @endforelse
    </section>
@endsection
