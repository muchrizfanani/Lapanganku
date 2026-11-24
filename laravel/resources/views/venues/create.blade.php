@extends('layouts.app')

@section('title', 'Tambah Venue')

@section('content')
    <section class="form-card">
        <p class="eyebrow">VENUE BARU</p>
        <h1>Tambah Venue</h1>
        <p class="muted">Lengkapi detail venue olahraga yang akan ditampilkan di LapanganKu.</p>
        @include('venues._form', ['action' => route('venues.store'), 'method' => 'POST', 'venue' => null])
    </section>
@endsection
