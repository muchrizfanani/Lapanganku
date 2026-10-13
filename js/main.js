/**
 * LapanganKu — Main Script
 * Sprint 02: Event Handling, DOM Manipulation
 * Sprint 03: Connect to API, Display Data, Handle Loading & Error State
 */

document.addEventListener('DOMContentLoaded', () => {

  // ============================================
  // 1. MOBILE NAV TOGGLE
  // ============================================
  const mobileToggle = document.getElementById('mobileToggle');
  const navMenu = document.getElementById('navMenu');

  if (mobileToggle && navMenu) {
    mobileToggle.addEventListener('click', () => {
      navMenu.classList.toggle('active');
    });
    navMenu.querySelectorAll('.nav-link').forEach(link => {
      link.addEventListener('click', () => navMenu.classList.remove('active'));
    });
  }

  // ============================================
  // 2. UTILITIES
  // ============================================
  function formatRupiah(number) {
    return 'Rp ' + Number(number).toLocaleString('id-ID');
  }

  // ============================================
  // 3. API CONFIG & STATE
  // ============================================
  // Ganti dengan URL REST API nyata kapan saja, contoh:
  // const API_URL = 'https://api.npoint.io/xxxxx';
  const API_URL = 'venues.json';

  const apiLoadingEl      = document.getElementById('apiLoading');
  const apiErrorEl        = document.getElementById('apiError');
  const apiErrorMessageEl = document.getElementById('apiErrorMessage');
  const btnRetryApi       = document.getElementById('btnRetryApi');
  const venueGrid         = document.getElementById('venueGrid');
  const noResults         = document.getElementById('noResults');
  const resetFilterBtn    = document.getElementById('resetFilterBtn');

  /**
   * Atur tampilan UI sesuai status API.
   * @param {'loading'|'success'|'error'} state
   * @param {string} [message]
   */
  function setApiState(state, message) {
    if (apiLoadingEl) {
      apiLoadingEl.classList.toggle('hidden', state !== 'loading');
      apiLoadingEl.classList.toggle('flex',  state === 'loading');
    }
    if (apiErrorEl) {
      apiErrorEl.classList.toggle('hidden', state !== 'error');
      apiErrorEl.classList.toggle('flex',   state === 'error');
    }
    if (state === 'error' && apiErrorMessageEl && message) {
      apiErrorMessageEl.textContent = message;
    }
  }

  // ============================================
  // 4. RENDER VENUE CARD
  // ============================================
  function createVenueCard(venue) {
    const article = document.createElement('article');
    article.className = 'venue-card flex flex-col overflow-hidden rounded-[18px] border border-slate-200 bg-white transition hover:-translate-y-1.5 hover:shadow-[0_12px_24px_-4px_rgba(0,0,0,0.12)]';
    article.setAttribute('data-sport',    venue.sport || '');
    article.setAttribute('data-name',     venue.name || '');
    article.setAttribute('data-price',    venue.price || 0);
    article.setAttribute('data-location', venue.location || '');

    const gradient    = venue.badgeGradient || 'from-slate-700 to-slate-900';
    const statusClass = venue.statusClass || 'bg-emerald-500 text-white';
    const sportBadge  = venue.sportBadge  || venue.sport || 'Olahraga';

    article.innerHTML = `
      <div class="relative flex h-[180px] w-full flex-col items-start justify-between bg-gradient-to-br ${gradient} p-4">
        <span class="rounded-full bg-white/90 px-[0.65rem] py-[0.3rem] text-xs font-bold text-slate-900 backdrop-blur-sm">${sportBadge}</span>
        <span class="self-end rounded-full ${statusClass} px-[0.65rem] py-[0.3rem] text-xs font-bold">${venue.statusLabel || 'Tersedia'}</span>
      </div>
      <div class="flex grow flex-col p-5">
        <div class="mb-[0.6rem] flex items-center justify-between text-[0.8rem] text-slate-500">
          <span>${venue.locationFull || venue.location || ''}</span>
          <span class="font-bold text-slate-900">⭐ ${venue.rating ?? '-'} (${venue.reviewsCount ?? 0} Ulasan)</span>
        </div>
        <h3 class="mb-[0.4rem] text-[1.15rem] font-bold text-slate-900">${venue.name || 'Venue'}</h3>
        <p class="mb-5 text-[0.85rem] text-slate-500">${venue.facilities || ''}</p>
        <div class="mt-auto flex items-center justify-between border-t border-dashed border-slate-200 pt-4">
          <div>
            <span class="text-[1.15rem] font-extrabold text-emerald-600">${formatRupiah(venue.price || 0)}</span>
            <span class="text-[0.8rem] text-slate-500">/ jam</span>
          </div>
          <button type="button"
            class="btn-open-modal inline-flex items-center justify-center gap-2 rounded-lg border-2 border-transparent bg-emerald-600 px-4 py-2 text-[0.85rem] font-semibold text-white transition hover:-translate-y-0.5 hover:bg-emerald-700 hover:shadow-lg hover:shadow-emerald-600/30"
            data-name="${venue.name || ''}"
            data-price="${venue.price || 0}"
            data-location="${venue.location || ''}"
            data-sport="${sportBadge}">
            Cek Slot & Booking
          </button>
        </div>
      </div>
    `;
    return article;
  }

  function renderVenues(venues) {
    if (!venueGrid) return;
    venueGrid.innerHTML = '';
    const fragment = document.createDocumentFragment();
    venues.forEach(v => fragment.appendChild(createVenueCard(v)));
    venueGrid.appendChild(fragment);
    applyCurrentFilter();
  }

  // ============================================
  // 5. FETCH API (Task 01 + Task 03)
  // ============================================
  async function loadVenues() {
    setApiState('loading');
    if (venueGrid) venueGrid.innerHTML = '';

    try {
      const response = await fetch(API_URL, { cache: 'no-store' });

      if (!response.ok) {
        throw new Error(`Gagal mengambil data (HTTP ${response.status}).`);
      }

      const data = await response.json();

      if (!Array.isArray(data)) {
        throw new Error('Format data API tidak valid.');
      }

      renderVenues(data);
      setApiState('success');

    } catch (error) {
      console.error('[LapanganKu] API Error:', error);
      setApiState('error', 'Gagal memuat daftar venue. Silakan coba lagi.');
      if (venueGrid) venueGrid.innerHTML = '';
    }
  }

  // ============================================
  // 6. FILTER (Sprint 02)
  // ============================================
  const filterButtonsContainer = document.getElementById('filterButtons');
  const filterBtns = filterButtonsContainer
    ? filterButtonsContainer.querySelectorAll('.filter-btn')
    : [];

  function filterVenues(sportFilter = 'all', locationQuery = '') {
    const venueCards = document.querySelectorAll('#venueGrid .venue-card');
    let visibleCount = 0;

    venueCards.forEach(card => {
      const cardSport    = (card.getAttribute('data-sport') || '').toLowerCase();
      const cardLocation = (card.getAttribute('data-location') || '').toLowerCase();
      const cardName     = (card.getAttribute('data-name') || '').toLowerCase();

      const sportMatch = (sportFilter === 'all')
        || cardSport.includes(sportFilter.toLowerCase());

      const queryLower = (locationQuery || '').trim().toLowerCase();
      const locationMatch = !queryLower
        || cardLocation.includes(queryLower)
        || cardName.includes(queryLower);

      if (sportMatch && locationMatch) {
        card.classList.remove('hidden');
        visibleCount++;
      } else {
        card.classList.add('hidden');
      }
    });

    if (noResults) {
      noResults.classList.toggle('hidden', visibleCount !== 0);
      noResults.classList.toggle('flex',   visibleCount === 0);
    }
  }

  function applyCurrentFilter() {
    const activeBtn = document.querySelector('#filterButtons .filter-btn.active');
    const sport = activeBtn ? activeBtn.getAttribute('data-sport') : 'all';
    const locInput = document.getElementById('search-location');
    filterVenues(sport, locInput ? locInput.value : '');
  }

  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      filterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const selectedSport = btn.getAttribute('data-sport') || 'all';
      const searchLocationInput = document.getElementById('search-location');
      filterVenues(selectedSport, searchLocationInput ? searchLocationInput.value : '');
    });
  });

  const categoryCards = document.querySelectorAll('.category-card, [data-filter-link]');
  categoryCards.forEach(card => {
    card.addEventListener('click', () => {
      const sport = card.getAttribute('data-sport-click') || card.getAttribute('data-filter-link');
      if (!sport) return;
      const targetBtn = Array.from(filterBtns).find(b => b.getAttribute('data-sport') === sport);
      if (targetBtn) targetBtn.click();
    });
  });

  if (resetFilterBtn) {
    resetFilterBtn.addEventListener('click', () => {
      const allFilterBtn = Array.from(filterBtns).find(b => b.getAttribute('data-sport') === 'all');
      if (allFilterBtn) allFilterBtn.click();
      const searchLocationInput = document.getElementById('search-location');
      if (searchLocationInput) searchLocationInput.value = '';
      applyCurrentFilter();
    });
  }

  const heroSearchForm      = document.getElementById('heroSearchForm');
  const searchSportSelect   = document.getElementById('search-sport');
  const searchLocationInput = document.getElementById('search-location');

  if (heroSearchForm) {
    heroSearchForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const selectedSport = searchSportSelect ? searchSportSelect.value : 'all';
      const locationQuery = searchLocationInput ? searchLocationInput.value : '';

      filterBtns.forEach(b => {
        b.classList.toggle('active', b.getAttribute('data-sport') === selectedSport);
      });

      filterVenues(selectedSport, locationQuery);

      const direktoriSection = document.getElementById('direktori');
      if (direktoriSection) direktoriSection.scrollIntoView({ behavior: 'smooth' });
    });

    if (searchLocationInput) {
      searchLocationInput.addEventListener('input', () => {
        const selectedSport = searchSportSelect ? searchSportSelect.value : 'all';
        filterVenues(selectedSport, searchLocationInput.value);
      });
    }
  }

  // ============================================
  // 7. BOOKING MODAL (Sprint 02)
  // ============================================
  const bookingModal       = document.getElementById('bookingModal');
  const modalCloseBtn      = document.getElementById('modalCloseBtn');
  const modalCancelBtn     = document.getElementById('modalCancelBtn');
  const bookingForm        = document.getElementById('bookingForm');
  const modalSportBadge    = document.getElementById('modalSportBadge');
  const modalVenueTitle    = document.getElementById('modalVenueTitle');
  const modalVenueLocation = document.getElementById('modalVenueLocation');
  const modalPricePerJam   = document.getElementById('modalPricePerJam');
  const modalSelectedCount = document.getElementById('modalSelectedCount');
  const modalTotalPrice    = document.getElementById('modalTotalPrice');
  const modalSubmitBtn     = document.getElementById('modalSubmitBtn');
  const bookingDateInput   = document.getElementById('bookingDate');
  const slotGrid           = document.getElementById('slotGrid');

  let currentVenuePrice = 0;
  let selectedSlots = [];

  const availableTimes = [
    { time: '08:00 - 09:00', status: 'available' },
    { time: '09:00 - 10:00', status: 'available' },
    { time: '10:00 - 11:00', status: 'booked' },
    { time: '14:00 - 15:00', status: 'available' },
    { time: '15:00 - 16:00', status: 'available' },
    { time: '16:00 - 17:00', status: 'booked' },
    { time: '19:00 - 20:00', status: 'available' },
    { time: '20:00 - 21:00', status: 'available' }
  ];

  const SLOT_BASE = 'slot-btn rounded-lg border px-1.5 py-2.5 text-center text-sm font-semibold transition';
  const SLOT_AVAILABLE = SLOT_BASE
    + ' cursor-pointer border-slate-200 bg-slate-50 text-slate-900'
    + ' [&:not(.selected):hover]:border-emerald-600 [&:not(.selected):hover]:bg-emerald-50 [&:not(.selected):hover]:text-emerald-600'
    + ' [&.selected]:border-emerald-600 [&.selected]:bg-emerald-600 [&.selected]:text-white [&.selected]:shadow-md [&.selected]:shadow-emerald-600/30';
  const SLOT_BOOKED = SLOT_BASE + ' cursor-not-allowed border-amber-300 bg-amber-100 font-bold text-amber-800';

  function openModal(venueData) {
    if (!bookingModal) return;

    currentVenuePrice = parseInt(venueData.price, 10) || 0;
    selectedSlots = [];

    if (modalVenueTitle)    modalVenueTitle.textContent = venueData.name;
    if (modalVenueLocation) modalVenueLocation.textContent = '📍 ' + venueData.location;
    if (modalSportBadge)    modalSportBadge.textContent = venueData.sport;
    if (modalPricePerJam)   modalPricePerJam.textContent = formatRupiah(currentVenuePrice);

    if (bookingDateInput) {
      const today = new Date().toISOString().split('T')[0];
      bookingDateInput.value = today;
      bookingDateInput.min = today;
    }

    renderSlots();
    updateSummary();

    bookingModal.classList.remove('hidden');
    bookingModal.setAttribute('aria-hidden', 'false');
  }

  function closeModal() {
    if (!bookingModal) return;
    bookingModal.classList.add('hidden');
    bookingModal.setAttribute('aria-hidden', 'true');
    if (bookingForm) bookingForm.reset();
    selectedSlots = [];
  }

  function renderSlots() {
    if (!slotGrid) return;
    slotGrid.innerHTML = '';

    availableTimes.forEach(slot => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = slot.status === 'booked' ? SLOT_BOOKED : SLOT_AVAILABLE;
      btn.textContent = slot.time;

      if (slot.status === 'booked') {
        btn.disabled = true;
        btn.title = 'Slot sudah terisi';
      } else {
        btn.addEventListener('click', () => {
          if (btn.classList.contains('selected')) {
            btn.classList.remove('selected');
            selectedSlots = selectedSlots.filter(s => s !== slot.time);
          } else {
            btn.classList.add('selected');
            selectedSlots.push(slot.time);
          }
          updateSummary();
        });
      }
      slotGrid.appendChild(btn);
    });
  }

  function updateSummary() {
    const slotCount  = selectedSlots.length;
    const totalPrice = slotCount * currentVenuePrice;

    if (modalSelectedCount) modalSelectedCount.textContent = `${slotCount} slot (${slotCount} jam)`;
    if (modalTotalPrice)    modalTotalPrice.textContent = formatRupiah(totalPrice);
    if (modalSubmitBtn)     modalSubmitBtn.disabled = slotCount === 0;
  }

  if (venueGrid) {
    venueGrid.addEventListener('click', (e) => {
      const btn = e.target.closest('.btn-open-modal');
      if (!btn) return;
      openModal({
        name:     btn.getAttribute('data-name')     || 'Venue Olahraga',
        price:    btn.getAttribute('data-price')    || '100000',
        location: btn.getAttribute('data-location') || 'Indonesia',
        sport:    btn.getAttribute('data-sport')    || 'Futsal'
      });
    });
  }

  if (modalCloseBtn)  modalCloseBtn.addEventListener('click', closeModal);
  if (modalCancelBtn) modalCancelBtn.addEventListener('click', closeModal);

  if (bookingModal) {
    bookingModal.addEventListener('click', (e) => {
      if (e.target === bookingModal) closeModal();
    });
  }

  if (bookingForm) {
    bookingForm.addEventListener('submit', async (e) => {
      e.preventDefault();

      const playerName = document.getElementById('playerName').value;
      const playerPhone = document.getElementById('playerPhone').value;
      const bookingDate = bookingDateInput.value;
      const venueName = modalVenueTitle.textContent;
      const venueLocation = modalVenueLocation.textContent.replace('📍 ', '').trim();
      const sport = modalSportBadge.textContent.trim();
      const totalPrice = selectedSlots.length * currentVenuePrice;

      try {
        await fetch('api-book.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            player_name: playerName,
            player_phone: playerPhone,
            venue_name: venueName,
            venue_location: venueLocation,
            sport: sport,
            booking_date: bookingDate,
            slots: selectedSlots,
            total_price: totalPrice,
            price_per_hour: currentVenuePrice
          })
        });
      } catch (err) {
        console.warn('Simpan via API error, lanjut client-side:', err);
      }

      closeModal();
      showToast(
        'Booking Berhasil! 🎉',
        `Terima kasih ${playerName}! Slot ${selectedSlots.length} jam di ${venueName} telah tersimpan. Buka menu <a href="riwayat-booking.php" class="underline font-bold text-emerald-400">Riwayat Booking</a> untuk melihat tiket.`
      );
    });
  }

  function showToast(title, message) {
    const toast        = document.getElementById('toastNotification');
    const toastTitle   = document.getElementById('toastTitle');
    const toastMessage = document.getElementById('toastMessage');

    if (!toast) return;
    if (toastTitle)   toastTitle.textContent = title;
    if (toastMessage) toastMessage.textContent = message;

    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 5500);
  }

  // ============================================
  // 8. RETRY BUTTON
  // ============================================
  if (btnRetryApi) {
    btnRetryApi.addEventListener('click', loadVenues);
  }

  // ============================================
  // 9. INITIAL LOAD
  // ============================================
  // Jika kartu venue sudah dirender oleh PHP Server-Side (index.php),
  // jangan timpa dengan loadVenues() tapi cukup pasang event listener tombol modal.
  const existingCards = venueGrid ? venueGrid.querySelectorAll('.venue-card') : [];
  if (existingCards.length > 0) {
    bindOpenModalButtons();
    applyCurrentFilter();
  } else if (apiLoadingEl) {
    // Pada file index.html murni (Client-side), jalankan loadVenues() via fetch()
    loadVenues();
  }

});