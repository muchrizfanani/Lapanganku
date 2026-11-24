@extends('layouts.app')

@section('title', $venue->name)

@section('content')
    <section class="form-card">
        <p class="eyebrow">{{ strtoupper($venue->sport) }} · {{ strtoupper($venue->location) }}</p>
        <h1>{{ $venue->name }}</h1>
        <p class="muted">{{ $venue->address }}</p>
        <dl class="details">
            <dt>Harga</dt><dd>Rp {{ number_format($venue->price, 0, ',', '.') }} / jam</dd>
            <dt>Status</dt><dd>{{ ucfirst($venue->status) }}</dd>
            <dt>Fasilitas</dt><dd>{{ $venue->facilities ?: 'Belum dicantumkan' }}</dd>
            <dt>Rating</dt><dd>{{ $venue->rating ?? 'Belum ada' }} ({{ $venue->reviews }} ulasan)</dd>
        </dl>
        <div class="form-actions">
            <a class="button secondary" href="{{ route('venues.index') }}">Kembali</a>
            <a class="button primary" href="{{ route('venues.edit', $venue) }}">Edit Venue</a>
        </div>
    </section>
@endsection
