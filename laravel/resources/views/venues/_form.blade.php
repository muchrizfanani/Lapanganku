@php($value = fn (string $field, mixed $default = '') => old($field, $venue?->{$field} ?? $default))
<form method="POST" action="{{ $action }}" class="form-grid">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif
    @if ($errors->any())
        <div class="notice error form-wide">{{ $errors->first() }}</div>
    @endif
    <label>Nama venue
        <input name="name" value="{{ $value('name') }}" maxlength="150" required>
    </label>
    <label>Olahraga
        <input name="sport" value="{{ $value('sport') }}" maxlength="80" placeholder="Futsal, Badminton, Basket" required>
    </label>
    <label>Harga per jam (Rp)
        <input type="number" name="price" value="{{ $value('price', 0) }}" min="0" required>
    </label>
    <label>Kota / lokasi
        <input name="location" value="{{ $value('location') }}" maxlength="150" required>
    </label>
    <label class="form-wide">Alamat
        <input name="address" value="{{ $value('address') }}" maxlength="255" required>
    </label>
    <label class="form-wide">Fasilitas
        <textarea name="facilities" rows="3" maxlength="2000">{{ $value('facilities') }}</textarea>
    </label>
    <label>Rating (0–5)
        <input type="number" name="rating" value="{{ $value('rating') }}" min="0" max="5" step="0.01">
    </label>
    <label>Jumlah ulasan
        <input type="number" name="reviews" value="{{ $value('reviews', 0) }}" min="0">
    </label>
    <label>Status
        <select name="status" required>
            @foreach (['available' => 'Tersedia', 'limited' => 'Terbatas', 'full' => 'Penuh'] as $status => $label)
                <option value="{{ $status }}" @selected($value('status', 'available') === $status)>{{ $label }}</option>
            @endforeach
        </select>
    </label>
    <label>Label olahraga
        <input name="sport_badge" value="{{ $value('sport_badge') }}" maxlength="100">
    </label>
    <div class="form-actions form-wide">
        <a class="button secondary" href="{{ route('venues.index') }}">Batal</a>
        <button class="button primary" type="submit">Simpan Venue</button>
    </div>
</form>
