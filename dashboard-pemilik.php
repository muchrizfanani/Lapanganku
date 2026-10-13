<?php
require_once __DIR__ . '/includes/auth.php';

// Pastikan user adalah pemilik venue, jika belum login kita arahkan ke login atau otomatis login demo
if (!isLoggedIn()) {
    header("Location: login.php?demo=pemilik");
    exit;
}

$currentUser = getCurrentUser();
$allBookings = getBookings();

// Handle Aksi Terima / Tolak Booking
$actionMessage = '';
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $bookingId = $_GET['id'];

    foreach ($allBookings as &$item) {
        if ($item['id'] === $bookingId) {
            if ($action === 'confirm') {
                $item['status'] = 'confirmed';
                $actionMessage = "Booking {$bookingId} berhasil DITERIMA! ✓";
            } elseif ($action === 'reject') {
                $item['status'] = 'rejected';
                $actionMessage = "Booking {$bookingId} telah DITOLAK. ✕";
            }
            break;
        }
    }
    saveBookings($allBookings);
}

// Hitung Metrik Statistik
$totalBookings = count($allBookings);
$pendingCount = count(array_filter($allBookings, fn($b) => $b['status'] === 'pending'));
$confirmedCount = count(array_filter($allBookings, fn($b) => $b['status'] === 'confirmed'));
$estimatedRevenue = array_reduce($allBookings, function($carry, $item) {
    return ($item['status'] === 'confirmed' || $item['status'] === 'completed') ? $carry + $item['total_price'] : $carry;
}, 0);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Pemilik Venue — LapanganKu</title>
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

  <!-- Header -->
  <header class="sticky top-0 z-50 border-b border-slate-200 bg-white/95 backdrop-blur-md">
    <div class="mx-auto flex h-[76px] w-full max-w-[1200px] items-center justify-between px-5 md:px-6">
      <div class="flex items-center gap-3">
        <a href="index.php" class="flex items-center gap-2 text-2xl font-extrabold text-slate-900">
          <span class="text-3xl">⚽</span>
          <span>Lapangan<span class="text-emerald-600">Ku</span></span>
        </a>
        <span class="hidden rounded-full bg-slate-900 px-3 py-1 text-xs font-bold text-white md:inline-block">
          🏢 Panel Mitra Venue
        </span>
      </div>

      <nav class="flex items-center gap-5">
        <a href="index.php" class="text-sm font-semibold text-slate-600 hover:text-emerald-600">Lihat Web Utama</a>
        <a href="dashboard-pemilik.php" class="text-sm font-bold text-emerald-600">Dashboard</a>
        <a href="riwayat-booking.php" class="text-sm font-semibold text-slate-600 hover:text-emerald-600">Lihat Sisi Pemain</a>
      </nav>

      <div class="flex items-center gap-3">
        <div class="flex items-center gap-2 rounded-xl bg-slate-100 px-3 py-1.5 text-xs font-semibold">
          <span>👤 <?php echo htmlspecialchars($currentUser['name']); ?></span>
        </div>
        <a href="logout.php" class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Keluar</a>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="mx-auto max-w-[1200px] px-5 py-8 md:px-6">
    
    <!-- Welcome Banner -->
    <div class="mb-8 flex flex-col justify-between gap-4 rounded-3xl bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 p-6 text-white md:flex-row md:items-center md:p-8">
      <div>
        <span class="rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-bold text-emerald-300">
          📍 <?php echo htmlspecialchars($currentUser['venue_name'] ?: 'Champion Futsal Center Arena'); ?>
        </span>
        <h1 class="mt-2 text-2xl font-extrabold md:text-3xl">Dashboard Pengelola Venue</h1>
        <p class="mt-1 text-xs text-slate-300 md:text-sm">Kelola jadwal booking masuk, konfirmasi slot, dan monitoring pembayaran langsung di tempat.</p>
      </div>
      <div class="flex gap-2">
        <span class="inline-flex items-center gap-1.5 rounded-xl bg-white/10 px-3.5 py-2 text-xs font-bold backdrop-blur-sm">
          🟢 Status Venue: Buka & Aktif
        </span>
      </div>
    </div>

    <!-- Alert Notifikasi Aksi -->
    <?php if (!empty($actionMessage)): ?>
      <div class="mb-6 flex items-center justify-between rounded-2xl border border-emerald-300 bg-emerald-50 p-4 text-xs font-bold text-emerald-900 shadow-sm">
        <span><?php echo htmlspecialchars($actionMessage); ?></span>
        <a href="dashboard-pemilik.php" class="text-slate-500 hover:underline">Tutup</a>
      </div>
    <?php endif; ?>

    <!-- 4 Metric Cards -->
    <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Booking</span>
        <div class="mt-2 text-2xl font-extrabold text-slate-900"><?php echo $totalBookings; ?> Pesanan</div>
        <span class="text-[11px] text-slate-500">Semua riwayat jadwal</span>
      </div>

      <div class="rounded-2xl border border-amber-200 bg-amber-50/60 p-5 shadow-sm">
        <span class="text-xs font-bold uppercase tracking-wider text-amber-700">Perlu Konfirmasi</span>
        <div class="mt-2 text-2xl font-extrabold text-amber-900"><?php echo $pendingCount; ?> Booking</div>
        <span class="text-[11px] font-semibold text-amber-700">⚠️ Menunggu persetujuan Anda</span>
      </div>

      <div class="rounded-2xl border border-emerald-200 bg-emerald-50/60 p-5 shadow-sm">
        <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Booking Diterima</span>
        <div class="mt-2 text-2xl font-extrabold text-emerald-900"><?php echo $confirmedCount; ?> Jadwal</div>
        <span class="text-[11px] text-emerald-700">Siap main di lapangan</span>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Estimasi Tunai (COD)</span>
        <div class="mt-2 text-2xl font-extrabold text-emerald-600">Rp <?php echo number_format($estimatedRevenue, 0, ',', '.'); ?></div>
        <span class="text-[11px] text-slate-500">Bayar langsung di venue</span>
      </div>
    </div>

    <!-- Booking Approval Table -->
    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
      <div class="flex flex-wrap items-center justify-between border-b border-slate-100 p-6">
        <div>
          <h2 class="text-lg font-extrabold text-slate-900">Daftar Booking Masuk Pemain</h2>
          <p class="text-xs text-slate-500">Terima atau tolak pesanan pemain secara real-time</p>
        </div>
        <span class="text-xs text-slate-400"><?php echo count($allBookings); ?> Data Booking Tersimpan</span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead class="border-b border-slate-100 bg-slate-50 text-[11px] font-extrabold uppercase tracking-wider text-slate-500">
            <tr>
              <th class="px-6 py-4">ID & Pemesan</th>
              <th class="px-6 py-4">Venue & Olahraga</th>
              <th class="px-6 py-4">Jadwal & Slot Jam</th>
              <th class="px-6 py-4">Total Biaya</th>
              <th class="px-6 py-4">Status</th>
              <th class="px-6 py-4 text-center">Aksi Konfirmasi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-slate-700">
            <?php foreach ($allBookings as $bk): ?>
              <tr class="transition hover:bg-slate-50/80">
                <td class="px-6 py-4">
                  <span class="font-mono font-bold text-slate-400"><?php echo $bk['id']; ?></span>
                  <div class="font-extrabold text-slate-900"><?php echo htmlspecialchars($bk['player_name']); ?></div>
                  <div class="text-[11px] text-slate-400">📱 <?php echo htmlspecialchars($bk['player_phone']); ?></div>
                </td>
                <td class="px-6 py-4">
                  <div class="font-bold text-slate-900"><?php echo htmlspecialchars($bk['venue_name']); ?></div>
                  <span class="rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-semibold text-slate-600"><?php echo $bk['sport']; ?></span>
                </td>
                <td class="px-6 py-4">
                  <div class="font-bold text-slate-900">📅 <?php echo date('d M Y', strtotime($bk['booking_date'])); ?></div>
                  <div class="text-[11px] font-semibold text-emerald-700">⏰ <?php echo implode(', ', $bk['slots']); ?></div>
                </td>
                <td class="px-6 py-4">
                  <div class="font-extrabold text-emerald-600">Rp <?php echo number_format($bk['total_price'], 0, ',', '.'); ?></div>
                  <span class="text-[10px] text-slate-400"><?php echo $bk['payment_method']; ?></span>
                </td>
                <td class="px-6 py-4">
                  <?php if ($bk['status'] === 'confirmed'): ?>
                    <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[11px] font-bold text-emerald-800">✓ Diterima</span>
                  <?php elseif ($bk['status'] === 'rejected'): ?>
                    <span class="rounded-full bg-rose-100 px-2.5 py-0.5 text-[11px] font-bold text-rose-800">✕ Ditolak</span>
                  <?php elseif ($bk['status'] === 'completed'): ?>
                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-bold text-slate-600">✓ Selesai</span>
                  <?php else: ?>
                    <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-[11px] font-bold text-amber-800">⏳ Pending</span>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4 text-center">
                  <?php if ($bk['status'] === 'pending'): ?>
                    <div class="flex items-center justify-center gap-1.5">
                      <a href="dashboard-pemilik.php?action=confirm&id=<?php echo $bk['id']; ?>" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-700" title="Terima booking ini">
                        ✓ Terima
                      </a>
                      <a href="dashboard-pemilik.php?action=reject&id=<?php echo $bk['id']; ?>" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-700 transition hover:bg-rose-100" title="Tolak booking ini">
                        ✕ Tolak
                      </a>
                    </div>
                  <?php else: ?>
                    <span class="text-xs text-slate-400">Telah diproses</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

  </main>

</body>
</html>
