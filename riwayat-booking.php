<?php
require_once __DIR__ . '/includes/auth.php';

$currentUser = getCurrentUser();
$allBookings = getBookings();

// Jika pemain sedang login, filter berdasarkan nomor HP atau nama (atau tampilkan semua untuk kemudahan demo)
$myBookings = $allBookings;
if ($currentUser && $currentUser['role'] === 'pemain') {
    $filtered = array_filter($allBookings, function($b) use ($currentUser) {
        return (isset($b['player_name']) && stripos($b['player_name'], $currentUser['name']) !== false)
            || (isset($currentUser['phone']) && isset($b['player_phone']) && $b['player_phone'] === $currentUser['phone']);
    });
    // Jika ada yang cocok tampilkan, jika tidak ada fallback ke data all bookings demo
    if (!empty($filtered)) {
        $myBookings = array_values($filtered);
    }
}

function getBookingBadge($status) {
    switch ($status) {
        case 'confirmed':
            return ['label' => '✓ Dikonfirmasi (Siap Main)', 'class' => 'bg-emerald-100 text-emerald-800 border-emerald-300'];
        case 'rejected':
            return ['label' => '✕ Ditolak', 'class' => 'bg-rose-100 text-rose-800 border-rose-300'];
        case 'completed':
            return ['label' => '✓ Selesai', 'class' => 'bg-slate-100 text-slate-700 border-slate-300'];
        default:
            return ['label' => '⏳ Menunggu Konfirmasi', 'class' => 'bg-amber-100 text-amber-800 border-amber-300'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Riwayat Booking — LapanganKu</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: { extend: { fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] } } }
    };
  </script>
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900">

  <!-- Header / Navbar -->
  <header class="sticky top-0 z-50 border-b border-slate-200 bg-white/95 backdrop-blur-md">
    <div class="mx-auto flex h-[76px] w-full max-w-[1200px] items-center justify-between px-5 md:px-6">
      <a href="index.php" class="flex items-center gap-2 text-2xl font-extrabold text-slate-900">
        <span class="text-3xl">⚽</span>
        <span>Lapangan<span class="text-emerald-600">Ku</span></span>
      </a>

      <nav class="flex items-center gap-5">
        <a href="index.php" class="text-sm font-semibold text-slate-600 hover:text-emerald-600">Beranda</a>
        <a href="index.php#direktori" class="text-sm font-semibold text-slate-600 hover:text-emerald-600">Cari Venue</a>
        <a href="riwayat-booking.php" class="text-sm font-bold text-emerald-600">Riwayat Booking</a>
        <?php if ($currentUser && $currentUser['role'] === 'pemilik'): ?>
          <a href="dashboard-pemilik.php" class="text-sm font-semibold text-slate-600 hover:text-emerald-600">Dashboard Mitra</a>
        <?php endif; ?>
      </nav>

      <div class="flex items-center gap-3">
        <?php if ($currentUser): ?>
          <div class="flex items-center gap-2 rounded-xl bg-slate-100 px-3 py-1.5 text-xs font-semibold">
            <span>👤 <?php echo htmlspecialchars($currentUser['name']); ?></span>
            <span class="rounded bg-emerald-600 px-1.5 py-0.5 text-[10px] text-white"><?php echo strtoupper($currentUser['role']); ?></span>
          </div>
          <a href="logout.php" class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Keluar</a>
        <?php else: ?>
          <a href="login.php" class="rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white transition hover:bg-emerald-700">Masuk</a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <!-- Content -->
  <main class="mx-auto max-w-[1000px] px-5 py-10 md:px-6">
    <div class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-end">
      <div>
        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">🎟️ Tiket Booking Lapangan</span>
        <h1 class="mt-2 text-2xl font-extrabold text-slate-900 md:text-3xl">Riwayat Booking Pemain</h1>
        <p class="mt-1 text-sm text-slate-500">Lacak status pesanan lapangan Anda dan instruksi pembayaran di tempat (COD).</p>
      </div>
      <a href="index.php#direktori" class="inline-flex items-center gap-2 self-start rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700">
        <span>+</span> Booking Lapangan Lagi
      </a>
    </div>

    <!-- Booking List Cards -->
    <div class="space-y-5">
      <?php if (empty($myBookings)): ?>
        <div class="rounded-3xl border border-dashed border-slate-200 bg-white p-12 text-center text-slate-500">
          <p class="text-base font-semibold">Belum ada riwayat booking.</p>
          <p class="mt-1 text-xs text-slate-400">Pilih lapangan favoritmu dan jadwalkan pertandingan sekarang!</p>
          <a href="index.php#direktori" class="mt-4 inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white transition hover:bg-emerald-700">Cari Venue</a>
        </div>
      <?php else: ?>
        <?php foreach ($myBookings as $bk): ?>
          <?php $badge = getBookingBadge($bk['status']); ?>
          <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:shadow-md">
            
            <!-- Top bar -->
            <div class="flex flex-wrap items-center justify-between border-b border-slate-100 bg-slate-50/60 px-6 py-4">
              <div class="flex items-center gap-3">
                <span class="font-mono text-xs font-bold text-slate-500"><?php echo $bk['id']; ?></span>
                <span class="rounded-full border px-2.5 py-0.5 text-xs font-bold <?php echo $badge['class']; ?>">
                  <?php echo $badge['label']; ?>
                </span>
              </div>
              <span class="text-xs text-slate-400">Dipesan pada: <?php echo date('d M Y, H:i', strtotime($bk['created_at'])); ?></span>
            </div>

            <!-- Body -->
            <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-3">
              <!-- Venue details -->
              <div class="md:col-span-2">
                <div class="flex items-center gap-2 text-xs font-bold text-emerald-600">
                  <span><?php echo ($bk['sport'] === 'Badminton') ? '🏸' : (($bk['sport'] === 'Basket') ? '🏀' : '⚽'); ?></span>
                  <span><?php echo htmlspecialchars($bk['sport']); ?></span>
                  <span>&bull;</span>
                  <span>📍 <?php echo htmlspecialchars($bk['venue_location']); ?></span>
                </div>
                <h3 class="mt-1 text-lg font-extrabold text-slate-900"><?php echo htmlspecialchars($bk['venue_name']); ?></h3>
                
                <div class="mt-4 flex flex-wrap gap-4 text-xs text-slate-600">
                  <div class="rounded-xl bg-slate-100 p-2.5">
                    <span class="block text-[10px] font-bold uppercase text-slate-400">Tanggal Main</span>
                    <span class="font-bold text-slate-800">📅 <?php echo date('d F Y', strtotime($bk['booking_date'])); ?></span>
                  </div>
                  <div class="rounded-xl bg-slate-100 p-2.5">
                    <span class="block text-[10px] font-bold uppercase text-slate-400">Slot Jam Main</span>
                    <span class="font-bold text-slate-800">⏰ <?php echo implode(', ', $bk['slots']); ?></span>
                  </div>
                  <div class="rounded-xl bg-slate-100 p-2.5">
                    <span class="block text-[10px] font-bold uppercase text-slate-400">Nama Pemesan</span>
                    <span class="font-bold text-slate-800">👤 <?php echo htmlspecialchars($bk['player_name']); ?></span>
                  </div>
                </div>
              </div>

              <!-- Price & Actions -->
              <div class="flex flex-col justify-between rounded-2xl border border-slate-100 bg-slate-50/50 p-4">
                <div>
                  <span class="text-xs text-slate-500">Total Pembayaran</span>
                  <div class="text-xl font-extrabold text-emerald-600">Rp <?php echo number_format($bk['total_price'], 0, ',', '.'); ?></div>
                  <span class="mt-1 block text-[11px] font-semibold text-amber-700">💵 <?php echo $bk['payment_method']; ?></span>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-200">
                  <a href="https://wa.me/6281234567890?text=Halo%20Admin%20<?php echo urlencode($bk['venue_name']); ?>,%20saya%20konfirmasi%20booking%20<?php echo $bk['id']; ?>" target="_blank" class="flex w-full items-center justify-center gap-1.5 rounded-xl border border-emerald-600 bg-white py-2 text-xs font-bold text-emerald-700 transition hover:bg-emerald-50">
                    💬 Hubungi Pengelola
                  </a>
                </div>
              </div>

            </div>

          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </main>

</body>
</html>
