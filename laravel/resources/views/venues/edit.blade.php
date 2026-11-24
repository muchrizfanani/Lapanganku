@extends('layouts.app')

@section('title', 'Edit Venue')

@section('content')
    <section class="form-card">
        <p class="eyebrow">PERBARUI DATA</p>
        <h1>Edit Venue</h1>
        <p class="muted">Perbarui informasi {{ $venue->name }}.</p>
        @include('venues._form', ['action' => route('venues.update', $venue), 'method' => 'PUT', 'venue' => $venue])
    </section>
@endsection
