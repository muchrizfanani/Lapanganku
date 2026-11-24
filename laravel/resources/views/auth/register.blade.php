@extends('layouts.app')

@section('title', 'Daftar')

@section('content')
    <section class="auth-card">
        <p class="eyebrow">MULAI BERSAMA LAPANGANKU</p>
        <h1>Buat akun</h1>
        <p class="muted">Daftar sebagai pemain atau pemilik venue.</p>
        @if ($errors->any())
            <div class="notice error">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('register.store') }}" class="form-grid" id="registerForm">
            @csrf
            <label class="form-wide">Daftar sebagai
                <select name="role" id="accountRole" required>
                    <option value="pemain" @selected(old('role', 'pemain') === 'pemain')>Pemain</option>
                    <option value="pemilik" @selected(old('role') === 'pemilik')>Pemilik venue</option>
                </select>
            </label>
            <label class="form-wide">Nama lengkap
                <input type="text" name="name" value="{{ old('name') }}" required maxlength="120" autocomplete="name">
            </label>
            <label class="form-wide" id="venueNameField">Nama venue
                <input type="text" name="venue_name" value="{{ old('venue_name') }}" maxlength="150">
            </label>
            <label class="form-wide">Nomor WhatsApp / HP
                <input type="tel" name="phone" value="{{ old('phone') }}" maxlength="32" autocomplete="tel">
            </label>
            <label class="form-wide">Email
                <input type="email" name="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email">
            </label>
            <label>Password
                <input type="password" name="password" required minlength="8" autocomplete="new-password">
            </label>
            <label>Ulangi password
                <input type="password" name="password_confirmation" required minlength="8" autocomplete="new-password">
            </label>
            <button class="button primary form-wide" type="submit">Daftar</button>
        </form>
        <p class="auth-switch">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
    </section>
    <script>
        const accountRole = document.getElementById('accountRole');
        const venueNameField = document.getElementById('venueNameField');
        const venueNameInput = venueNameField.querySelector('input');

        function updateVenueField() {
            const isOwner = accountRole.value === 'pemilik';
            venueNameField.hidden = !isOwner;
            venueNameInput.required = isOwner;
        }

        accountRole.addEventListener('change', updateVenueField);
        updateVenueField();
    </script>
@endsection
