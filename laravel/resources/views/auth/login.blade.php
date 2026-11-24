@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
    <section class="auth-card">
        <p class="eyebrow">AKUN LAPANGANKU</p>
        <h1>Masuk ke LapanganKu</h1>
        <p class="muted">Masuk sebagai pemain untuk booking lapangan, atau sebagai pemilik untuk mengelola venue.</p>
        @if ($errors->any())
            <div class="notice error">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('login.store') }}" class="form-grid">
            @csrf
            <label>Email
                <input type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
            </label>
            <label>Password
                <input type="password" name="password" required autocomplete="current-password">
            </label>
            <button class="button primary" type="submit">Masuk</button>
        </form>
        <p class="auth-switch">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
    </section>
@endsection
