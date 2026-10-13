<?php
/**
 * LapanganKu — Task 01: Create PHP Page (Variabel)
 * Sprint 04 - Pertemuan 5
 *
 * Semua data venue disimpan di variabel PHP,
 * lalu ditampilkan ke HTML dengan echo.
 */

// ===== DATA VENUE (VARIABEL) =====
$namaPlatform   = "LapanganKu";
$namaVenue      = "Champion Futsal Center Arena";
$olahraga       = "Futsal";
$lokasi         = "Kebayoran Baru, Jakarta Selatan";
$hargaPerJam    = 150000;
$rating         = 4.9;
$jumlahUlasan   = 128;
$jamBuka        = "08:00";
$jamTutup       = "22:00";
$jumlahLapangan = 3;
$fasilitas      = "Rumput Sintetis • Kantin • Ruang Ganti • WiFi Free";
$statusVenue    = "Tersedia Hari Ini";

// ===== DATA PEMESAN (VARIABEL) =====
$namaPemain     = "Budi Santoso";
$tanggalMain    = "2026-10-17";
$jumlahJam      = 2;

// ===== HASIL HITUNG (OPERATOR) =====
$totalBayar     = $hargaPerJam * $jumlahJam;

// Format angka ke Rupiah: Rp 150.000
$hargaFormat    = "Rp " . number_format($hargaPerJam, 0, ',', '.');
$totalFormat    = "Rp " . number_format($totalBayar, 0, ',', '.');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $namaVenue; ?> — <?php echo $namaPlatform; ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: { extend: { fontFamily: { sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'] } } }
    };
  </script>
</head>
<body class="bg-slate-50 font-sans text-slate-900">

  <!-- HEADER -->
  <header class="flex h-[76px] items-center border-b border-slate-200 bg-white">
    <div class="mx-auto flex w-full max-w-[1000px] items-center justify-between px-5">
      <a href="index.html" class="flex items-center gap-2 text-[1.4rem] font-extrabold">
        <span class="text-2xl">⚽</span>
        <span>Lapangan<span class="text-emerald-600">Ku</span></span>
      </a>
      <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-600">Pertemuan 5 · Task 01</span>
    </div>
  </header>

  <main class="mx-auto w-full max-w-[1000px] px-5 py-12">

    <h1 class="text-[1.75rem] font-extrabold md:text-[2.2rem]">Informasi Venue</h1>
    <p class="mt-1 text-slate-500">Halaman ini dibuat oleh PHP. Semua data di bawah berasal dari variabel.</p>

    <div class="mt-8 grid gap-6 md:grid-cols-[3fr_2fr]">

      <!-- KARTU VENUE -->
      <article class="overflow-hidden rounded-[18px] border border-slate-200 bg-white">
        <div class="flex h-[160px] flex-col justify-between bg-gradient-to-br from-emerald-800 to-emerald-600 p-4">
          <span class="self-start rounded-full bg-white/90 px-3 py-1 text-xs font-bold">⚽ <?php echo $olahraga; ?></span>
          <span class="self-end rounded-full bg-emerald-500 px-3 py-1 text-xs font-bold text-white"><?php echo $statusVenue; ?></span>
        </div>

        <div class="p-6">
          <p class="text-sm text-slate-500">📍 <?php echo $lokasi; ?></p>
          <h2 class="mt-1 text-xl font-bold"><?php echo $namaVenue; ?></h2>
          <p class="mt-2 text-sm text-slate-500"><?php echo $fasilitas; ?></p>

          <dl class="mt-5 grid grid-cols-2 gap-4 border-t border-dashed border-slate-200 pt-5 text-sm">
            <div>
              <dt class="text-slate-500">Rating</dt>
              <dd class="font-bold">⭐ <?php echo $rating; ?> (<?php echo $jumlahUlasan; ?> ulasan)</dd>
            </div>
            <div>
              <dt class="text-slate-500">Jumlah lapangan</dt>
              <dd class="font-bold"><?php echo $jumlahLapangan; ?> lapangan</dd>
            </div>
            <div>
              <dt class="text-slate-500">Jam operasional</dt>
              <dd class="font-bold"><?php echo $jamBuka; ?> - <?php echo $jamTutup; ?></dd>
            </div>
            <div>
              <dt class="text-slate-500">Harga</dt>
              <dd class="font-bold text-emerald-600"><?php echo $hargaFormat; ?> / jam</dd>
            </div>
          </dl>
        </div>
      </article>

      <!-- RINGKASAN BOOKING -->
      <aside class="h-fit rounded-[18px] border border-slate-200 bg-white p-6">
        <h2 class="text-lg font-bold">Ringkasan Booking</h2>
        <div class="mt-4 flex flex-col gap-2 text-sm">
          <div class="flex justify-between text-slate-500"><span>Pemesan</span><span class="font-semibold text-slate-900"><?php echo $namaPemain; ?></span></div>
          <div class="flex justify-between text-slate-500"><span>Tanggal main</span><span class="font-semibold text-slate-900"><?php echo $tanggalMain; ?></span></div>
          <div class="flex justify-between text-slate-500"><span>Durasi</span><span class="font-semibold text-slate-900"><?php echo $jumlahJam; ?> jam</span></div>
          <div class="mt-2 flex justify-between border-t border-dashed border-slate-200 pt-3 font-bold">
            <span>Total (bayar di tempat)</span>
            <span class="text-lg font-extrabold text-emerald-600"><?php echo $totalFormat; ?></span>
          </div>
        </div>
      </aside>
    </div>

    <!-- CHECKLIST TASK 01 -->
    <section class="mt-8 rounded-[18px] border border-slate-200 bg-white p-6">
      <h2 class="text-lg font-bold">Checklist Task 01</h2>
      <ul class="mt-3 space-y-1 text-sm text-slate-600">
        <li>☑ File PHP dijalankan lewat server (localhost)</li>
        <li>☑ Variabel digunakan untuk data venue dan pemesan</li>
        <li>☑ Output tampil di halaman dengan echo</li>
        <li>☑ Tampilan tetap mengikuti tema LapanganKu</li>
        <li>☑ Tidak ada error di browser</li>
      </ul>
    </section>

  </main>

  <footer class="bg-slate-900 py-6 text-center text-sm text-slate-400">
    &copy; <?php echo date("Y"); ?> <?php echo $namaPlatform; ?> — Tugas Web Programming Pertemuan 5
  </footer>

</body>
</html>