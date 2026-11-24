@extends('layouts.app')

@section('title', 'Masuk Pemilik')

@section('content')
    <section class="auth-card">
        <p class="eyebrow">DASHBOARD MITRA</p>
        <h1>Masuk ke LapanganKu</h1>
        <p class="muted">Gunakan akun pemilik venue untuk mengelola daftar lapangan.</p>
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
    </section>
@endsection
