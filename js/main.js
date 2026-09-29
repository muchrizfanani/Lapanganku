/**
 * LapanganKu — Main Interactive Script (Sprint 02 - Pertemuan 3)
 * Demonstrating Vanilla JS: Event Handling, DOM Selector, and DOM Manipulation
 */

document.addEventListener('DOMContentLoaded', () => {


  const mobileToggle = document.getElementById('mobileToggle');
  const navMenu = document.getElementById('navMenu');

  if (mobileToggle && navMenu) {
    mobileToggle.addEventListener('click', () => {
      navMenu.classList.toggle('active');
    });

    // Close menu when clicking navigation link on mobile
    const navLinks = navMenu.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
      link.addEventListener('click', () => {
        navMenu.classList.remove('active');
      });
    });
  }


  const filterButtonsContainer = document.getElementById('filterButtons');
  const filterBtns = filterButtonsContainer ? filterButtonsContainer.querySelectorAll('.filter-btn') : [];
  const venueCards = document.querySelectorAll('.venue-card');
  const noResults = document.getElementById('noResults');
  const resetFilterBtn = document.getElementById('resetFilterBtn');

  /**
   * Filter venue cards based on selected sport category & location query
   */
  function filterVenues(sportFilter = 'all', locationQuery = '') {
    let visibleCount = 0;

    venueCards.forEach(card => {
      const cardSport = card.getAttribute('data-sport') || '';
      const cardLocation = (card.getAttribute('data-location') || '').toLowerCase();
      const cardName = (card.getAttribute('data-name') || '').toLowerCase();

      // Check sport match
      const sportMatch = (sportFilter === 'all') || cardSport.includes(sportFilter);

      // Check location / query match
      const queryLower = locationQuery.trim().toLowerCase();
      const locationMatch = !queryLower || cardLocation.includes(queryLower) || cardName.includes(queryLower);

      if (sportMatch && locationMatch) {
        card.classList.remove('hidden');
        visibleCount++;
      } else {
        card.classList.add('hidden');
      }
    });

    // Show/hide no results message
    if (noResults) {
      if (visibleCount === 0) {
        noResults.classList.remove('hidden');
      } else {
        noResults.classList.add('hidden');
      }
    }
  }

  // Filter button click handler
  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      // Toggle active button class
      filterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      const selectedSport = btn.getAttribute('data-sport') || 'all';
      const searchLocationInput = document.getElementById('search-location');
      const locationVal = searchLocationInput ? searchLocationInput.value : '';

      filterVenues(selectedSport, locationVal);
    });
  });

  // Category card links click handler
  const categoryCards = document.querySelectorAll('.category-card, [data-filter-link]');
  categoryCards.forEach(card => {
    card.addEventListener('click', (e) => {
      const sport = card.getAttribute('data-sport-click') || card.getAttribute('data-filter-link');
      if (sport) {
        // Find corresponding filter button
        const targetBtn = Array.from(filterBtns).find(b => b.getAttribute('data-sport') === sport);
        if (targetBtn) {
          targetBtn.click();
        }
      }
    });
  });

  // Reset filter button
  if (resetFilterBtn) {
    resetFilterBtn.addEventListener('click', () => {
      const allFilterBtn = Array.from(filterBtns).find(b => b.getAttribute('data-sport') === 'all');
      if (allFilterBtn) allFilterBtn.click();
      const searchLocationInput = document.getElementById('search-location');
      if (searchLocationInput) searchLocationInput.value = '';
    });
  }


  // ============================================
  // 3. LIVE SEARCH FORM (HERO SECTION)
  // ============================================
  const heroSearchForm = document.getElementById('heroSearchForm');
  const searchSportSelect = document.getElementById('search-sport');
  const searchLocationInput = document.getElementById('search-location');

  if (heroSearchForm) {
    heroSearchForm.addEventListener('submit', (e) => {
      e.preventDefault();

      const selectedSport = searchSportSelect ? searchSportSelect.value : 'all';
      const locationQuery = searchLocationInput ? searchLocationInput.value : '';

      // Update active filter button
      filterBtns.forEach(b => {
        if (b.getAttribute('data-sport') === selectedSport) {
          b.classList.add('active');
        } else {
          b.classList.remove('active');
        }
      });

      filterVenues(selectedSport, locationQuery);

      // Smooth scroll to direktori section
      const direktoriSection = document.getElementById('direktori');
      if (direktoriSection) {
        direktoriSection.scrollIntoView({ behavior: 'smooth' });
      }
    });

    // Real-time input filter listener
    if (searchLocationInput) {
      searchLocationInput.addEventListener('input', () => {
        const selectedSport = searchSportSelect ? searchSportSelect.value : 'all';
        filterVenues(selectedSport, searchLocationInput.value);
      });
    }
  }


  const bookingModal = document.getElementById('bookingModal');
  const modalCloseBtn = document.getElementById('modalCloseBtn');
  const modalCancelBtn = document.getElementById('modalCancelBtn');
  const openModalBtns = document.querySelectorAll('.btn-open-modal');
  const bookingForm = document.getElementById('bookingForm');

  // Modal elements
  const modalSportBadge = document.getElementById('modalSportBadge');
  const modalVenueTitle = document.getElementById('modalVenueTitle');
  const modalVenueLocation = document.getElementById('modalVenueLocation');
  const modalPricePerJam = document.getElementById('modalPricePerJam');
  const modalSelectedCount = document.getElementById('modalSelectedCount');
  const modalTotalPrice = document.getElementById('modalTotalPrice');
  const modalSubmitBtn = document.getElementById('modalSubmitBtn');
  const bookingDateInput = document.getElementById('bookingDate');
  const slotGrid = document.getElementById('slotGrid');

  // State variables for modal
  let currentVenuePrice = 0;
  let selectedSlots = [];

  // Standard hours list
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

  /**
   * Format currency number to Indonesian Rupiah (Rp)
   */
  function formatRupiah(number) {
    return 'Rp ' + Number(number).toLocaleString('id-ID');
  }

  /**
   * Open modal and populate data
   */
  function openModal(venueData) {
    if (!bookingModal) return;

    currentVenuePrice = parseInt(venueData.price, 10) || 0;
    selectedSlots = [];

    // Set venue info in DOM
    if (modalVenueTitle) modalVenueTitle.textContent = venueData.name;
    if (modalVenueLocation) modalVenueLocation.textContent = '📍 ' + venueData.location;
    if (modalSportBadge) modalSportBadge.textContent = venueData.sport;
    if (modalPricePerJam) modalPricePerJam.textContent = formatRupiah(currentVenuePrice);

    // Set default date to today
    if (bookingDateInput) {
      const today = new Date().toISOString().split('T')[0];
      bookingDateInput.value = today;
      bookingDateInput.min = today;
    }

    // Render slot grid
    renderSlots();
    updateSummary();

    // Show modal
    bookingModal.classList.remove('hidden');
    bookingModal.setAttribute('aria-hidden', 'false');
  }

  /**
   * Close modal
   */
  function closeModal() {
    if (!bookingModal) return;
    bookingModal.classList.add('hidden');
    bookingModal.setAttribute('aria-hidden', 'true');
    if (bookingForm) bookingForm.reset();
  }

  // Class Tailwind untuk tombol slot (class "selected" tetap dipasang/dihapus oleh JavaScript)
  const SLOT_BASE = 'slot-btn rounded-lg border px-1.5 py-2.5 text-center text-sm font-semibold transition';
  const SLOT_AVAILABLE = SLOT_BASE + ' cursor-pointer border-slate-200 bg-slate-50 text-slate-900'
    + ' [&:not(.selected):hover]:border-emerald-600 [&:not(.selected):hover]:bg-emerald-50 [&:not(.selected):hover]:text-emerald-600'
    + ' [&.selected]:border-emerald-600 [&.selected]:bg-emerald-600 [&.selected]:text-white [&.selected]:shadow-md [&.selected]:shadow-emerald-600/30';
  const SLOT_BOOKED = SLOT_BASE + ' cursor-not-allowed border-amber-300 bg-amber-100 font-bold text-amber-800';

  /**
   * Render hourly slots dynamically into the slot grid
   */
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
        btn.title = 'Slot sudah terisi oleh pemesan lain';
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

  /**
   * Update price calculation summary
   */
  function updateSummary() {
    const slotCount = selectedSlots.length;
    const totalPrice = slotCount * currentVenuePrice;

    if (modalSelectedCount) modalSelectedCount.textContent = `${slotCount} slot (${slotCount} jam)`;
    if (modalTotalPrice) modalTotalPrice.textContent = formatRupiah(totalPrice);

    // Enable/disable submit button
    if (modalSubmitBtn) {
      modalSubmitBtn.disabled = slotCount === 0;
    }
  }

  // Open modal click listeners
  openModalBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const venueData = {
        name: btn.getAttribute('data-name') || 'Venue Olahraga',
        price: btn.getAttribute('data-price') || '100000',
        location: btn.getAttribute('data-location') || 'Indonesia',
        sport: btn.getAttribute('data-sport') || 'Futsal'
      };
      openModal(venueData);
    });
  });

  // Close modal click listeners
  if (modalCloseBtn) modalCloseBtn.addEventListener('click', closeModal);
  if (modalCancelBtn) modalCancelBtn.addEventListener('click', closeModal);

  // Close modal when clicking backdrop
  if (bookingModal) {
    bookingModal.addEventListener('click', (e) => {
      if (e.target === bookingModal) {
        closeModal();
      }
    });
  }

  // Handle Booking Form Submit
  if (bookingForm) {
    bookingForm.addEventListener('submit', (e) => {
      e.preventDefault();

      const playerName = document.getElementById('playerName').value;
      const playerPhone = document.getElementById('playerPhone').value;
      const bookingDate = bookingDateInput.value;
      const venueName = modalVenueTitle.textContent;
      const totalPriceText = modalTotalPrice.textContent;

      // Close modal
      closeModal();

      // Show Success Toast Notification
      showToast(
        'Booking Berhasil! 🎉',
        `Terima kasih ${playerName}! Slot ${selectedSlots.length} jam di ${venueName} pada tanggal ${bookingDate} telah terkonfirmasi. Pembayaran ${totalPriceText} dilakukan langsung di lokasi.`
      );
    });
  }

  /**
   * Display toast notification (DOM Manipulation)
   */
  function showToast(title, message) {
    const toast = document.getElementById('toastNotification');
    const toastTitle = document.getElementById('toastTitle');
    const toastMessage = document.getElementById('toastMessage');

    if (!toast) return;

    if (toastTitle) toastTitle.textContent = title;
    if (toastMessage) toastMessage.textContent = message;

    toast.classList.remove('hidden');

    // Hide toast automatically after 5 seconds
    setTimeout(() => {
      toast.classList.add('hidden');
    }, 5500);
  }

});