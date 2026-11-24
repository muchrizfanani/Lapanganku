@extends('layouts.app')

@section('title', 'Booking Venue')

@section('content')
    <div class="page-heading">
        <div>
            <p class="eyebrow">BOOKING LAPANGAN</p>
            <h1>{{ $venue->name }}</h1>
            <p class="muted">{{ $venue->sport }} · {{ $venue->location }} · {{ $venue->address }}</p>
        </div>
        <a class="button secondary" href="{{ route('player.dashboard') }}">Kembali</a>
    </div>

    @if ($errors->any())
        <div class="notice error">{{ $errors->first() }}</div>
    @endif

    <section class="booking-panel">
        <div class="booking-rate">
            <span>Harga per jam</span>
            <strong>Rp {{ number_format($venue->price, 0, ',', '.') }}</strong>
            <small>Bayar langsung di venue setelah booking dikonfirmasi.</small>
        </div>

        <form method="GET" action="{{ route('bookings.create', $venue) }}" class="date-filter">
            <label for="bookingDate">Tanggal main</label>
            <input id="bookingDate" type="date" name="date" min="{{ now()->toDateString() }}" value="{{ $bookingDate }}" required>
            <button class="button secondary" type="submit">Cek slot</button>
        </form>

        <form method="POST" action="{{ route('bookings.store', $venue) }}" id="bookingForm">
            @csrf
            <input type="hidden" name="booking_date" value="{{ $bookingDate }}">
            <h2>Pilih slot jam</h2>
            <p class="muted">Slot yang sudah dipesan atau menunggu konfirmasi tidak bisa dipilih.</p>
            <div class="slot-grid">
                @foreach ($slots as $slot)
                    @php($isOccupied = in_array($slot, $occupiedSlots, true))
                    <label class="slot-option {{ $isOccupied ? 'occupied' : '' }}">
                        <input type="checkbox" name="slots[]" value="{{ $slot }}"
                            @checked(in_array($slot, old('slots', []), true))
                            @disabled($isOccupied)>
                        <span>{{ $slot }}</span>
                        @if ($isOccupied)
                            <small>Terisi</small>
                        @endif
                    </label>
                @endforeach
            </div>

            <div class="form-grid booking-details">
                <label>Nama pemesan
                    <input type="text" name="player_name" value="{{ old('player_name', auth()->user()->name) }}" maxlength="120" required>
                </label>
                <label>Nomor WhatsApp / HP
                    <input type="tel" name="player_phone" value="{{ old('player_phone', auth()->user()->phone) }}" maxlength="32" required>
                </label>
            </div>

            <div class="booking-summary">
                <span><span id="selectedCount">0</span> slot dipilih</span>
                <strong>Total: Rp <span id="bookingTotal">0</span></strong>
            </div>
            <button class="button primary" type="submit">Konfirmasi Booking (Bayar di Tempat)</button>
        </form>
    </section>
    <script>
        const bookingForm = document.getElementById('bookingForm');
        const slotInputs = bookingForm.querySelectorAll('input[name="slots[]"]');
        const selectedCount = document.getElementById('selectedCount');
        const bookingTotal = document.getElementById('bookingTotal');
        const hourlyPrice = {{ $venue->price }};

        function updateBookingTotal() {
            const count = bookingForm.querySelectorAll('input[name="slots[]"]:checked').length;
            selectedCount.textContent = count;
            bookingTotal.textContent = (count * hourlyPrice).toLocaleString('id-ID');
        }

        slotInputs.forEach((input) => input.addEventListener('change', updateBookingTotal));
        updateBookingTotal();
    </script>
@endsection
