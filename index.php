<?php
/**
 * LapanganKu — Sprint 04: From Client-Side to Server-Side (PHP Procedural)
 * Pertemuan 5: Web Programming
 */

// ==============================================================================
// 1. VARIABLE PHP (Task 01: Menyimpan data identitas & konfigurasi web)
// ==============================================================================
$appName = "LapanganKu";
$heroBadge = "⚡ Instant Booking & Bayar di Tempat";
$heroTitle = "Cari & Booking Lapangan Olahraga";
$heroHighlight = "Tanpa Ribet";
$heroSubtitle = "Temukan venue Futsal, Badminton, dan Basket terdekat. Cek ketersediaan jam secara real-time dan konfirmasi slotmu detik ini juga!";

// Statistik Aplikasi
$statTotalVenues = "150+";
$statSportsCount = 3;
$statRating = "4.9★";

// ==============================================================================
// 2. ARRAY PHP (Task 02: Menyimpan data terstruktur Kategori & Venue)
// ==============================================================================
$categories = [
    [
        "id" => "futsal",
        "name" => "Futsal",
        "icon" => "⚽",
        "bg_color" => "bg-emerald-100",
        "desc" => "Lapangan Rumput Sintetis, Vinyl, dan Matras Interlock standar nasional."
    ],
    [
        "id" => "badminton",
        "name" => "Badminton",
        "icon" => "🏸",
        "bg_color" => "bg-sky-100",
        "desc" => "Karpet Vinyl BWF Standard, pencahayaan LED terang anti-silau."
    ],
    [
        "id" => "basket",
        "name" => "Basket",
        "icon" => "🏀",
        "bg_color" => "bg-amber-100",
        "desc" => "Lapangan Indoor Full-court & Outdoor FIBA approved ring & lantai kayu/interlock."
    ]
];

$venueList = [
    [
        "id" => 1,
        "name" => "Champion Futsal Center Arena",
        "sport" => "futsal",
        "sport_badge" => "⚽ Futsal",
        "gradient" => "from-emerald-800 to-emerald-600",
        "location" => "Jakarta Selatan",
        "address" => "📍 Kebayoran Baru, Jakarta Selatan",
        "price" => 150000,
        "rating" => 4.9,
        "reviews" => 128,
        "facilities" => "Rumput Sintetis • Kantin • Ruang Ganti • WiFi Free",
        "status" => "available" // available | limited | booked
    ],
    [
        "id" => 2,
        "name" => "Smash Hub Badminton Center",
        "sport" => "badminton",
        "sport_badge" => "🏸 Badminton",
        "gradient" => "from-sky-600 to-sky-400",
        "location" => "Bandung",
        "address" => "📍 Bandung Wetan, Bandung",
        "price" => 65000,
        "rating" => 4.8,
        "reviews" => 94,
        "facilities" => "4 Karpet Vinyl BWF • AC • Locker Room • Parkir Luas",
        "status" => "available"
    ],
    [
        "id" => 3,
        "name" => "SlamDunk Indoor Basketball Arena",
        "sport" => "basket",
        "sport_badge" => "🏀 Basket",
        "gradient" => "from-amber-600 to-amber-500",
        "location" => "Surabaya",
        "address" => "📍 Gubeng, Surabaya",
        "price" => 120000,
        "rating" => 4.7,
        "reviews" => 67,
        "facilities" => "Wood Floor • Ring FIBA Standard • Tribune Penonton",
        "status" => "limited"
    ],
    [
        "id" => 4,
        "name" => "Garuda Sport Hall Multi-Court",
        "sport" => "futsal badminton",
        "sport_badge" => "⚽ Futsal & 🏸 Badminton",
        "gradient" => "from-slate-700 to-slate-900",
        "location" => "Jakarta Barat",
        "address" => "📍 Grogol, Jakarta Barat",
        "price" => 100000,
        "rating" => 4.9,
        "reviews" => 156,
        "facilities" => "Futsal Vinyl • 6 Lapangan Badminton • Musholla",
        "status" => "available"
    ],
    [
        "id" => 5,
        "name" => "Galaxy Arena Futsal & Basket",
        "sport" => "futsal basket",
        "sport_badge" => "⚽ Futsal & 🏀 Basket",
        "gradient" => "from-indigo-700 to-purple-600",
        "location" => "Tangerang",
        "address" => "📍 Serpong, Tangerang Selatan",
        "price" => 135000,
        "rating" => 4.8,
        "reviews" => 112,
        "facilities" => "Matras Interlock • Scoreboard Digital • Shower Panas",
        "status" => "available"
    ],
    [
        "id" => 6,
        "name" => "Djarum Smash Badminton Hall",
        "sport" => "badminton",
        "sport_badge" => "🏸 Badminton",
        "gradient" => "from-teal-600 to-emerald-500",
        "location" => "Yogyakarta",
        "address" => "📍 Depok, Sleman, Yogyakarta",
        "price" => 55000,
        "rating" => 4.9,
        "reviews" => 180,
        "facilities" => "6 Lapangan Karpet • Toko Senar • Musholla Luas",
        "status" => "available"
    ]
];

// ==============================================================================
// 3. PHP FUNCTION (Task 02: Membuat fungsi prosedural pembantu)
// ==============================================================================

/**
 * Format angka integer menjadi format mata uang Rupiah
 */
function formatRupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

/**
 * Mendapatkan konfigurasi badge status ketersediaan venue berdasarkan kondisi (Task 02: Conditional)
 */
function getStatusBadgeInfo($status) {
    if ($status === "available") {
        return [
            "label" => "Tersedia Hari Ini",
            "class" => "bg-emerald-500 text-white"
        ];
    } elseif ($status === "limited") {
        return [
            "label" => "⚠️ Terisi Sebagian",
            "class" => "bg-amber-500 text-amber-900 font-extrabold"
        ];
    } else {
        return [
            "label" => "Penuh Hari Ini",
            "class" => "bg-rose-500 text-white"
        ];
    }
}

// Menangkap query filter server-side jika ada
$selectedFilter = isset($_GET['sport']) ? strtolower(trim($_GET['sport'])) : 'all';
$searchQuery = isset($_GET['location']) ? strtolower(trim($_GET['location'])) : '';

// Proses filter data di server-side (PHP Process)
$displayedVenues = [];
foreach ($venueList as $item) {
    $sportMatch = ($selectedFilter === 'all' || strpos($item['sport'], $selectedFilter) !== false);
    $locationMatch = ($searchQuery === '' || strpos(strtolower($item['location']), $searchQuery) !== false || strpos(strtolower($item['name']), $searchQuery) !== false);

    if ($sportMatch && $locationMatch) {
        $displayedVenues[] = $item;
    }
}

require_once __DIR__ . '/includes/auth.php';
$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $appName; ?> — Platform Booking Lapangan Futsal, Badminton & Basket (Server-Side PHP)</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Tailwind CSS (CDN) -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'] },
          keyframes: {
            fadeIn: { from: { opacity: 0 }, to: { opacity: 1 } },
            slideUp: { from: { transform: 'translateY(20px)', opacity: 0 }, to: { transform: 'translateY(0)', opacity: 1 } },
            slideInRight: { from: { transform: 'translateX(100px)', opacity: 0 }, to: { transform: 'translateX(0)', opacity: 1 } }
          },
          animation: {
            'fade-in': 'fadeIn 0.25s ease-out',
            'slide-up': 'slideUp 0.3s cubic-bezier(0.16,1,0.3,1)',
            'slide-in-right': 'slideInRight 0.3s ease-out'
          }
        }
      }
    };
  </script>
</head>
<body class="w-full overflow-x-hidden bg-slate-50 font-sans leading-[1.6] text-slate-900">

  <!-- ============ HEADER & NAVIGATION ============ -->
  <header class="sticky top-0 z-[100] flex h-[76px] items-center border-b border-slate-200 bg-white/90 backdrop-blur-md">
    <div class="mx-auto flex w-full max-w-[1200px] items-center justify-between px-5 md:px-6">
      <a href="index.php" class="flex items-center gap-2 text-[1.4rem] font-extrabold text-slate-900">
        <span class="text-2xl">⚽</span>
        <span>Lapangan<span class="text-emerald-600">Ku</span></span>
        <span class="ml-2 rounded-md bg-emerald-100 px-2 py-0.5 text-xs font-bold text-emerald-800">PHP</span>
      </a>

      <!-- Menu Navigasi -->
      <nav id="navMenu" class="hidden absolute left-0 top-[76px] z-[99] w-full border-b border-slate-200 bg-white px-5 py-6 shadow-[0_15px_30px_rgba(0,0,0,0.12)] md:static md:block md:w-auto md:border-0 md:bg-transparent md:p-0 md:shadow-none [&.active]:block [&.active]:animate-fade-in">
        <ul class="flex flex-col items-start gap-5 md:flex-row md:items-center md:gap-8">
          <li><a href="index.php" class="nav-link active text-[0.95rem] font-semibold text-emerald-600 transition hover:text-emerald-600">Beranda</a></li>
          <li><a href="#direktori" class="nav-link text-[0.95rem] font-medium text-slate-500 transition hover:font-semibold hover:text-emerald-600">Cari Venue</a></li>
          <li><a href="riwayat-booking.php" class="nav-link text-[0.95rem] font-medium text-slate-500 transition hover:font-semibold hover:text-emerald-600">Riwayat Booking</a></li>
          <li><a href="dashboard-pemilik.php" class="nav-link text-[0.95rem] font-medium text-slate-500 transition hover:font-semibold hover:text-emerald-600">Dashboard Pemilik</a></li>
          <li class="mt-4 flex w-full flex-col gap-3 border-t border-slate-200 pt-5 md:hidden">
            <?php if ($currentUser): ?>
              <span class="text-xs text-slate-500">Masuk sebagai: <strong><?php echo htmlspecialchars($currentUser['name']); ?></strong></span>
              <a href="logout.php" class="inline-flex w-full items-center justify-center rounded-xl border border-rose-200 bg-rose-50 px-4 py-2.5 text-xs font-bold text-rose-700">Keluar</a>
            <?php else: ?>
              <a href="login.php" class="inline-flex w-full items-center justify-center rounded-xl border-2 border-slate-200 px-[1.4rem] py-3 text-[0.95rem] font-semibold transition hover:border-emerald-600 hover:bg-emerald-50 hover:text-emerald-600">Masuk</a>
              <a href="register.php" class="inline-flex w-full items-center justify-center rounded-xl border-2 border-transparent bg-emerald-600 px-[1.4rem] py-3 text-[0.95rem] font-semibold text-white transition hover:bg-emerald-700">Daftar</a>
            <?php endif; ?>
          </li>
        </ul>
      </nav>

      <div class="flex items-center gap-3">
        <?php if ($currentUser): ?>
          <div class="hidden items-center gap-2 rounded-xl bg-slate-100 px-3.5 py-2 text-xs font-semibold md:flex">
            <span>👤 <?php echo htmlspecialchars($currentUser['name']); ?></span>
            <span class="rounded bg-emerald-600 px-1.5 py-0.5 text-[10px] font-bold text-white"><?php echo strtoupper($currentUser['role']); ?></span>
          </div>
          <a href="logout.php" class="hidden rounded-xl border border-rose-200 bg-rose-50 px-3.5 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100 md:inline-flex">Keluar</a>
        <?php else: ?>
          <a href="login.php" class="hidden items-center justify-center rounded-xl border-2 border-slate-200 px-[1.4rem] py-3 text-[0.95rem] font-semibold transition hover:border-emerald-600 hover:bg-emerald-50 hover:text-emerald-600 md:inline-flex">Masuk</a>
          <a href="register.php" class="hidden items-center justify-center rounded-xl border-2 border-transparent bg-emerald-600 px-[1.4rem] py-3 text-[0.95rem] font-semibold text-white transition hover:-translate-y-0.5 hover:bg-emerald-700 hover:shadow-lg hover:shadow-emerald-600/30 md:inline-flex">Daftar</a>
        <?php endif; ?>
        <button id="mobileToggle" aria-label="Toggle menu" class="p-1.5 md:hidden">
          <span class="my-[5px] block h-0.5 w-6 bg-slate-900"></span>
          <span class="my-[5px] block h-0.5 w-6 bg-slate-900"></span>
          <span class="my-[5px] block h-0.5 w-6 bg-slate-900"></span>
        </button>
      </div>
    </div>
  </header>

  <main>

    <!-- ============ HERO SECTION ============ -->
    <section class="bg-[radial-gradient(circle_at_80%_20%,rgba(5,150,105,0.08),transparent_50%),radial-gradient(circle_at_20%_80%,rgba(245,158,11,0.05),transparent_50%)] pb-20 pt-16">
      <div class="mx-auto flex w-full max-w-[1200px] flex-col items-center px-5 text-center md:px-6">
        <div class="w-full max-w-[860px]">
          <!-- Output Variable PHP: Badge & Judul -->
          <span class="inline-block rounded-full bg-emerald-50 px-[0.85rem] py-[0.35rem] text-sm font-semibold text-emerald-600"><?php echo $heroBadge; ?></span>
          <h1 class="mb-4 mt-[1.2rem] text-[1.75rem] font-extrabold leading-[1.25] tracking-tight md:text-[2.4rem] md:leading-[1.2] lg:text-[3rem]">
            <?php echo $heroTitle; ?> <span class="bg-gradient-to-br from-emerald-600 to-emerald-500 bg-clip-text text-transparent"><?php echo $heroHighlight; ?></span>
          </h1>
          <p class="mb-7 text-[0.95rem] text-slate-500 md:mb-10 md:text-[1.15rem]">
            <?php echo $heroSubtitle; ?>
          </p>

          <!-- Quick Search Form (Mendukung GET Request Server-Side) -->
          <form id="heroSearchForm" action="index.php#direktori" method="GET" class="grid grid-cols-1 items-end gap-4 rounded-[20px] bg-white/90 p-4 text-left shadow-[0_20px_40px_-15px_rgba(5,150,105,0.15),0_0_0_1px_rgba(5,150,105,0.12)] backdrop-blur-xl transition hover:shadow-[0_25px_50px_-12px_rgba(5,150,105,0.22),0_0_0_1px_rgba(5,150,105,0.25)] md:grid-cols-2 md:gap-5 md:rounded-3xl md:p-6 lg:grid-cols-[1.1fr_1.2fr_1fr_auto]">
            <div class="flex flex-col gap-2">
              <label for="search-sport" class="text-xs font-extrabold uppercase tracking-[0.08em] text-slate-600">Jenis Olahraga</label>
              <select id="search-sport" name="sport" class="w-full appearance-none rounded-[14px] border-[1.5px] border-slate-200 bg-slate-50/80 px-[1.1rem] py-[0.85rem] text-base font-semibold text-slate-900 outline-none transition hover:border-slate-300 hover:bg-white focus:-translate-y-px focus:border-emerald-600 focus:bg-white focus:ring-4 focus:ring-emerald-600/20">
                <option value="all" <?php echo ($selectedFilter === 'all') ? 'selected' : ''; ?>>Semua Olahraga</option>
                <option value="futsal" <?php echo ($selectedFilter === 'futsal') ? 'selected' : ''; ?>>⚽ Futsal</option>
                <option value="badminton" <?php echo ($selectedFilter === 'badminton') ? 'selected' : ''; ?>>🏸 Badminton</option>
                <option value="basket" <?php echo ($selectedFilter === 'basket') ? 'selected' : ''; ?>>🏀 Basket</option>
              </select>
            </div>

            <div class="flex flex-col gap-2">
              <label for="search-location" class="text-xs font-extrabold uppercase tracking-[0.08em] text-slate-600">Lokasi / Kota</label>
              <input type="text" id="search-location" name="location" value="<?php echo htmlspecialchars($searchQuery); ?>" placeholder="Misal: Jakarta, Bandung, Surabaya" class="w-full appearance-none rounded-[14px] border-[1.5px] border-slate-200 bg-slate-50/80 px-[1.1rem] py-[0.85rem] text-base font-semibold text-slate-900 outline-none transition hover:border-slate-300 hover:bg-white focus:-translate-y-px focus:border-emerald-600 focus:bg-white focus:ring-4 focus:ring-emerald-600/20">
            </div>

            <div class="flex flex-col gap-2">
              <label for="search-date" class="text-xs font-extrabold uppercase tracking-[0.08em] text-slate-600">Tanggal Main</label>
              <input type="date" id="search-date" name="date" class="w-full appearance-none rounded-[14px] border-[1.5px] border-slate-200 bg-slate-50/80 px-[1.1rem] py-[0.85rem] text-base font-semibold text-slate-900 outline-none transition hover:border-slate-300 hover:bg-white focus:-translate-y-px focus:border-emerald-600 focus:bg-white focus:ring-4 focus:ring-emerald-600/20">
            </div>

            <button type="submit" class="inline-flex h-[52px] items-center justify-center gap-2 rounded-[14px] border-none bg-gradient-to-br from-emerald-600 to-emerald-500 px-7 text-[0.95rem] font-bold text-white shadow-lg shadow-emerald-600/40 transition hover:-translate-y-0.5 hover:scale-[1.02] hover:bg-gradient-to-br hover:from-emerald-700 hover:to-emerald-600 hover:shadow-emerald-600/50">
              <span>🔍</span> Cari Venue
            </button>
          </form>

          <!-- Output Variable PHP: Statistik Hero -->
          <div class="mt-12 flex flex-col items-center justify-center gap-4 md:flex-row md:gap-10">
            <div class="flex flex-col items-center">
              <span class="text-[1.75rem] font-extrabold text-slate-900"><?php echo $statTotalVenues; ?></span>
              <span class="text-sm text-slate-500">Venue Terdaftar</span>
            </div>
            <div class="h-px w-14 bg-slate-200 md:h-9 md:w-px"></div>
            <div class="flex flex-col items-center">
              <span class="text-[1.75rem] font-extrabold text-slate-900"><?php echo $statSportsCount; ?></span>
              <span class="text-sm text-slate-500">Cabang Olahraga</span>
            </div>
            <div class="h-px w-14 bg-slate-200 md:h-9 md:w-px"></div>
            <div class="flex flex-col items-center">
              <span class="text-[1.75rem] font-extrabold text-slate-900"><?php echo $statRating; ?></span>
              <span class="text-sm text-slate-500">Rating Pemain</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ============ CATEGORY SECTION (Task 02: PHP Loop foreach) ============ -->
    <section class="border-y border-slate-200 bg-white py-[4.5rem]">
      <div class="mx-auto w-full max-w-[1200px] px-5 md:px-6">
        <div class="mx-auto mb-12 max-w-[600px] text-center">
          <h2 class="text-[2rem] font-extrabold leading-[1.3] text-slate-900">Pilih Jenis Olahraga</h2>
          <p class="mt-[0.4rem] text-base text-slate-500">Temukan lapangan sesuai dengan hobi dan tim kamu</p>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-3 md:gap-7">
          <?php foreach ($categories as $cat): ?>
            <article class="category-card flex cursor-pointer flex-col items-start rounded-[18px] border border-slate-200 bg-slate-50 p-8 transition hover:-translate-y-1.5 hover:border-emerald-600 hover:bg-white hover:shadow-md" data-sport-click="<?php echo $cat['id']; ?>">
              <div class="mb-5 flex h-[60px] w-[60px] items-center justify-center rounded-xl <?php echo $cat['bg_color']; ?> text-[1.8rem]">
                <?php echo $cat['icon']; ?>
              </div>
              <h3 class="mb-2 text-[1.35rem] font-bold"><?php echo $cat['name']; ?></h3>
              <p class="mb-6 text-[0.9rem] text-slate-500"><?php echo $cat['desc']; ?></p>
              <a href="index.php?sport=<?php echo $cat['id']; ?>#direktori" data-filter-link="<?php echo $cat['id']; ?>" class="text-[0.9rem] font-bold text-emerald-600">
                Lihat Venue <?php echo $cat['name']; ?> &rarr;
              </a>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- ============ DIREKTORI VENUE (Task 02 & 03: Dynamic PHP Rendering) ============ -->
    <section id="direktori" class="py-[4.5rem]">
      <div class="mx-auto w-full max-w-[1200px] px-5 md:px-6">
        <div class="mb-10 flex flex-wrap items-end justify-between gap-4">
          <div>
            <h2 class="text-[2rem] font-extrabold leading-[1.3] text-slate-900">Rekomendasi Venue Terpopuler</h2>
            <p class="mt-[0.4rem] text-base text-slate-500">Lapangan favorit pemain yang dirender secara dinamis melalui Server-Side PHP</p>
          </div>
          <!-- Filter Buttons -->
          <div id="filterButtons" class="flex max-w-full gap-2 overflow-x-auto whitespace-nowrap pb-2 md:overflow-visible md:pb-0">
            <button class="filter-btn <?php echo ($selectedFilter === 'all') ? 'active' : ''; ?> shrink-0 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-500 transition hover:border-emerald-600 hover:bg-emerald-600 hover:text-white [&.active]:border-emerald-600 [&.active]:bg-emerald-600 [&.active]:text-white" data-sport="all">Semua</button>
            <button class="filter-btn <?php echo ($selectedFilter === 'futsal') ? 'active' : ''; ?> shrink-0 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-500 transition hover:border-emerald-600 hover:bg-emerald-600 hover:text-white [&.active]:border-emerald-600 [&.active]:bg-emerald-600 [&.active]:text-white" data-sport="futsal">Futsal</button>
            <button class="filter-btn <?php echo ($selectedFilter === 'badminton') ? 'active' : ''; ?> shrink-0 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-500 transition hover:border-emerald-600 hover:bg-emerald-600 hover:text-white [&.active]:border-emerald-600 [&.active]:bg-emerald-600 [&.active]:text-white" data-sport="badminton">Badminton</button>
            <button class="filter-btn <?php echo ($selectedFilter === 'basket') ? 'active' : ''; ?> shrink-0 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-500 transition hover:border-emerald-600 hover:bg-emerald-600 hover:text-white [&.active]:border-emerald-600 [&.active]:bg-emerald-600 [&.active]:text-white" data-sport="basket">Basket</button>
          </div>
        </div>

        <!-- Server-Side Info Notice (Sprint 04: Server-Side Processing) -->
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 text-xs text-slate-600 shadow-sm">
          <div class="flex items-center gap-2">
            <span class="inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span><strong>PHP Server-Side Active:</strong> Menampilkan <span class="font-bold text-emerald-600"><?php echo count($displayedVenues); ?></span> venue dari total <?php echo count($venueList); ?> venue yang diproses oleh PHP.</span>
          </div>
          <?php if ($selectedFilter !== 'all' || !empty($searchQuery)): ?>
            <a href="index.php#direktori" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-1 font-semibold text-slate-700 transition hover:bg-slate-100">
              ✕ Reset Filter Server
            </a>
          <?php endif; ?>
        </div>

        <div id="venueGrid" class="grid grid-cols-1 gap-6 md:grid-cols-[repeat(auto-fit,minmax(270px,1fr))] md:gap-7">
          <?php if (empty($displayedVenues)): ?>
            <div class="col-span-full rounded-2xl border border-dashed border-slate-200 bg-white p-12 text-center text-slate-500">
              <p class="text-base font-semibold">🔍 Tidak ada venue yang cocok dengan pencarian Anda.</p>
              <a href="index.php#direktori" class="mt-4 inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">Tampilkan Semua Venue</a>
            </div>
          <?php else: ?>
            <?php foreach ($displayedVenues as $venue): ?>
              <?php 
                // Memanggil fungsi PHP conditional untuk status badge
                $badgeInfo = getStatusBadgeInfo($venue['status']); 
              ?>
              <article class="venue-card flex flex-col overflow-hidden rounded-[18px] border border-slate-200 bg-white transition hover:-translate-y-1.5 hover:shadow-[0_12px_24px_-4px_rgba(0,0,0,0.12)]" 
                       data-sport="<?php echo htmlspecialchars($venue['sport']); ?>" 
                       data-name="<?php echo htmlspecialchars($venue['name']); ?>" 
                       data-price="<?php echo $venue['price']; ?>" 
                       data-location="<?php echo htmlspecialchars($venue['location']); ?>">
                <div class="relative flex h-[180px] w-full flex-col items-start justify-between bg-gradient-to-br <?php echo $venue['gradient']; ?> p-4">
                  <span class="rounded-full bg-white/90 px-[0.65rem] py-[0.3rem] text-xs font-bold text-slate-900 backdrop-blur-sm">
                    <?php echo $venue['sport_badge']; ?>
                  </span>
                  <span class="self-end rounded-full px-[0.65rem] py-[0.3rem] text-xs font-bold <?php echo $badgeInfo['class']; ?>">
                    <?php echo $badgeInfo['label']; ?>
                  </span>
                </div>
                <div class="flex grow flex-col p-5">
                  <div class="mb-[0.6rem] flex items-center justify-between text-[0.8rem] text-slate-500">
                    <span><?php echo $venue['address']; ?></span>
                    <span class="font-bold text-slate-900">⭐ <?php echo $venue['rating']; ?> (<?php echo $venue['reviews']; ?> Ulasan)</span>
                  </div>
                  <h3 class="mb-[0.4rem] text-[1.15rem] font-bold text-slate-900"><?php echo $venue['name']; ?></h3>
                  <p class="mb-5 text-[0.85rem] text-slate-500"><?php echo $venue['facilities']; ?></p>
                  <div class="mt-auto flex items-center justify-between border-t border-dashed border-slate-200 pt-4">
                    <div>
                      <!-- Output PHP Function formatRupiah() -->
                      <span class="text-[1.15rem] font-extrabold text-emerald-600"><?php echo formatRupiah($venue['price']); ?></span>
                      <span class="text-[0.8rem] text-slate-500">/ jam</span>
                    </div>
                    <button type="button" class="btn-open-modal inline-flex items-center justify-center gap-2 rounded-lg border-2 border-transparent bg-emerald-600 px-4 py-2 text-[0.85rem] font-semibold text-white transition hover:-translate-y-0.5 hover:bg-emerald-700 hover:shadow-lg hover:shadow-emerald-600/30"
                      data-name="<?php echo htmlspecialchars($venue['name']); ?>" 
                      data-price="<?php echo $venue['price']; ?>" 
                      data-location="<?php echo htmlspecialchars($venue['location']); ?>" 
                      data-sport="<?php echo htmlspecialchars($venue['sport']); ?>">
                      Cek Slot & Booking
                    </button>
                  </div>
                </div>
              </article>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <!-- No Results (Client-side fallback) -->
        <div id="noResults" class="hidden mt-6 flex flex-col items-center gap-4 rounded-[18px] border border-dashed border-slate-200 bg-white px-4 py-12 text-center text-slate-500">
          <p>🔍 Tidak ada venue yang cocok dengan pencarian Anda.</p>
          <button type="button" id="resetFilterBtn" class="inline-flex items-center justify-center gap-2 rounded-lg border-2 border-slate-200 px-4 py-2 text-[0.85rem] font-semibold text-slate-900 transition hover:border-emerald-600 hover:bg-emerald-50 hover:text-emerald-600">Tampilkan Semua Venue</button>
        </div>
      </div>
    </section>

    <!-- ============ CARA BOOKING ============ -->
    <section class="border-t border-slate-200 bg-white py-[4.5rem]">
      <div class="mx-auto w-full max-w-[1200px] px-5 md:px-6">
        <div class="mx-auto mb-12 max-w-[600px] text-center">
          <h2 class="text-[2rem] font-extrabold leading-[1.3] text-slate-900">Cara Mudah Booking di LapanganKu</h2>
          <p class="mt-[0.4rem] text-base text-slate-500">Hanya butuh 3 langkah singkat tanpa perlu pembayaran awal online</p>
        </div>

        <div class="grid gap-5 md:grid-cols-3 md:gap-8">
          <div class="rounded-[18px] bg-slate-50 p-8 text-center">
            <div class="mx-auto mb-5 flex h-[50px] w-[50px] items-center justify-center rounded-full bg-emerald-600 text-[1.4rem] font-extrabold text-white shadow-[0_4px_12px_rgba(5,150,105,0.3)]">1</div>
            <h3 class="mb-2 text-xl font-bold">Pilih Venue & Olahraga</h3>
            <p class="text-[0.9rem] text-slate-500">Cari lapangan berdasarkan lokasi terdekat, cabang olahraga (Futsal, Badminton, Basket), dan fasilitas yang kamu inginkan.</p>
          </div>
          <div class="rounded-[18px] bg-slate-50 p-8 text-center">
            <div class="mx-auto mb-5 flex h-[50px] w-[50px] items-center justify-center rounded-full bg-emerald-600 text-[1.4rem] font-extrabold text-white shadow-[0_4px_12px_rgba(5,150,105,0.3)]">2</div>
            <h3 class="mb-2 text-xl font-bold">Pilih Jam & Slot Real-time</h3>
            <p class="text-[0.9rem] text-slate-500">Lihat kalender ketersediaan slot yang belum terisi per jam secara transparan tanpa perlu menelepon admin.</p>
          </div>
          <div class="rounded-[18px] bg-slate-50 p-8 text-center">
            <div class="mx-auto mb-5 flex h-[50px] w-[50px] items-center justify-center rounded-full bg-emerald-600 text-[1.4rem] font-extrabold text-white shadow-[0_4px_12px_rgba(5,150,105,0.3)]">3</div>
            <h3 class="mb-2 text-xl font-bold">Konfirmasi & Bayar di Tempat</h3>
            <p class="text-[0.9rem] text-slate-500">Terima bukti booking otomatis dan langsung datang ke lokasi. Pembayaran dilakukan secara tunai/cash di tempat!</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ============ CTA MITRA ============ -->
    <section id="dashboard-mitra" class="py-[4.5rem]">
      <div class="mx-auto w-full max-w-[1200px] px-5 md:px-6">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 to-slate-800 px-5 py-8 text-white md:px-12 md:py-14">
          <div class="max-w-[650px]">
            <span class="inline-block rounded-full bg-amber-100 px-[0.85rem] py-[0.35rem] text-sm font-semibold text-amber-700">🤝 Khusus Pemilik Venue</span>
            <h2 class="my-4 text-2xl font-extrabold leading-[1.3] md:text-[2.2rem]">Punya Lapangan Olahraga? Daftarkan Venue Anda Sekarang!</h2>
            <p class="mb-6 text-[1.05rem] text-slate-400">
              Kelola jadwal ketersediaan lapangan secara otomatis, atur slot per jam, serta terima atau tolak booking dari pemain dengan mudah melalui Dashboard Mitra.
            </p>
            <ul class="mb-8">
              <li class="mb-2 text-[0.95rem] text-slate-300">✓ Tingkatkan keterisian slot jam kosong venue Anda</li>
              <li class="mb-2 text-[0.95rem] text-slate-300">✓ Kelola status jadwal secara real-time & transparan</li>
              <li class="mb-2 text-[0.95rem] text-slate-300">✓ Bebas biaya pendaftaran awal bagi mitra baru</li>
            </ul>
            <div class="flex flex-wrap gap-4">
              <a href="register.php" class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-transparent bg-emerald-600 px-[1.4rem] py-3 text-[0.95rem] font-semibold text-white transition hover:bg-emerald-700">Daftar Sebagai Mitra Venue</a>
              <a href="dashboard-pemilik.php" class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-white/40 px-[1.4rem] py-3 text-[0.95rem] font-semibold text-white transition hover:border-white hover:bg-white/10">Lihat Demo Dashboard</a>
            </div>
          </div>
        </div>
      </div>
    </section>

  </main>

  <!-- ============ MODAL BOOKING ============ -->
  <div id="bookingModal" aria-hidden="true" class="hidden fixed inset-0 z-[1000] flex animate-fade-in items-center justify-center bg-slate-900/65 p-4 backdrop-blur-[6px]">
    <div class="max-h-[90vh] w-full max-w-[94vw] animate-slide-up overflow-y-auto rounded-[18px] border border-slate-200 bg-white shadow-[0_20px_40px_rgba(0,0,0,0.2)] md:max-w-[580px] md:rounded-[20px]">
      <div class="flex items-start justify-between border-b border-slate-200 p-6">
        <div>
          <span id="modalSportBadge" class="inline-block rounded-full bg-emerald-50 px-[0.85rem] py-[0.35rem] text-sm font-semibold text-emerald-600">Futsal</span>
          <h3 id="modalVenueTitle" class="mt-[0.4rem] text-[1.3rem] font-extrabold text-slate-900">Nama Venue</h3>
          <p id="modalVenueLocation" class="text-[0.85rem] text-slate-500">📍 Lokasi Venue</p>
        </div>
        <button id="modalCloseBtn" aria-label="Tutup modal" class="p-[0.2rem] text-[1.8rem] leading-none text-slate-500 transition hover:text-slate-900">&times;</button>
      </div>

      <form id="bookingForm" class="flex flex-col gap-5 p-6">
        <div class="flex flex-col gap-2">
          <label for="bookingDate" class="text-sm font-bold text-slate-900">Pilih Tanggal Main:</label>
          <input type="date" id="bookingDate" required class="appearance-none rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-base text-slate-900 outline-none transition focus:border-emerald-600 focus:bg-white focus:ring-[3px] focus:ring-emerald-600/15">
        </div>

        <div class="flex flex-col gap-2">
          <label class="text-sm font-bold text-slate-900">Pilih Slot Jam (Real-Time):</label>
          <div class="mb-[0.4rem] flex gap-5 text-[0.8rem] text-slate-500">
            <span class="flex items-center gap-[0.4rem]"><span class="h-3 w-3 rounded-[3px] border border-emerald-500 bg-emerald-50"></span> Tersedia</span>
            <span class="flex items-center gap-[0.4rem]"><span class="h-3 w-3 rounded-[3px] bg-emerald-600"></span> Terpilih</span>
            <span class="flex items-center gap-[0.4rem]"><span class="h-3 w-3 rounded-[3px] border border-amber-600 bg-amber-500"></span> Terisi</span>
          </div>
          <!-- Tombol slot dibuat oleh JavaScript (renderSlots) -->
          <div id="slotGrid" class="grid grid-cols-2 gap-2 md:grid-cols-4 md:gap-[0.6rem]"></div>
        </div>

        <div class="flex flex-col gap-2">
          <label for="playerName" class="text-sm font-bold text-slate-900">Nama Pemain (Pemesan):</label>
          <input type="text" id="playerName" required placeholder="Masukkan nama lengkap Anda" class="appearance-none rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-base text-slate-900 outline-none transition focus:border-emerald-600 focus:bg-white focus:ring-[3px] focus:ring-emerald-600/15">
        </div>

        <div class="flex flex-col gap-2">
          <label for="playerPhone" class="text-sm font-bold text-slate-900">Nomor WhatsApp / HP:</label>
          <input type="tel" id="playerPhone" required placeholder="Misal: 08123456789" class="appearance-none rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-base text-slate-900 outline-none transition focus:border-emerald-600 focus:bg-white focus:ring-[3px] focus:ring-emerald-600/15">
        </div>

        <div class="flex flex-col gap-2 rounded-xl border border-slate-200 bg-slate-50 px-5 py-4 text-[0.9rem]">
          <div class="flex justify-between text-slate-500">
            <span>Harga per jam:</span>
            <span id="modalPricePerJam">Rp 0</span>
          </div>
          <div class="flex justify-between text-slate-500">
            <span>Jumlah slot terpilih:</span>
            <span id="modalSelectedCount">0 jam</span>
          </div>
          <div class="mt-[0.4rem] flex justify-between border-t border-dashed border-slate-200 pt-[0.6rem] font-bold text-slate-900">
            <span>Total Pembayaran (Bayar di Tempat):</span>
            <span id="modalTotalPrice" class="text-[1.2rem] font-extrabold text-emerald-600">Rp 0</span>
          </div>
        </div>

        <div class="mt-2 flex justify-end gap-3">
          <button type="button" id="modalCancelBtn" class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-slate-200 px-[1.4rem] py-3 text-[0.95rem] font-semibold text-slate-900 transition hover:border-emerald-600 hover:bg-emerald-50 hover:text-emerald-600">Batal</button>
          <button type="submit" id="modalSubmitBtn" disabled class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-transparent bg-emerald-600 px-[1.4rem] py-3 text-[0.95rem] font-semibold text-white transition hover:-translate-y-0.5 hover:bg-emerald-700 hover:shadow-lg hover:shadow-emerald-600/30 disabled:cursor-not-allowed disabled:bg-slate-400 disabled:opacity-70 disabled:hover:translate-y-0 disabled:hover:shadow-none">
            ⚡ Konfirmasi Booking (Bayar di Tempat)
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ============ TOAST ============ -->
  <div id="toastNotification" class="hidden fixed inset-x-4 bottom-4 z-[1100] flex animate-slide-in-right items-center gap-4 rounded-xl border-l-4 border-emerald-600 bg-slate-900 px-6 py-4 text-white shadow-[0_10px_30px_rgba(0,0,0,0.2)] md:inset-x-auto md:bottom-8 md:right-8">
    <div class="text-2xl">✅</div>
    <div>
      <h4 id="toastTitle" class="text-[0.95rem] font-bold">Booking Berhasil!</h4>
      <p id="toastMessage" class="text-[0.85rem] text-slate-400">Pesanan Anda telah dicatat. Pembayaran dilakukan di tempat.</p>
    </div>
  </div>

  <!-- ============ FOOTER ============ -->
  <footer class="bg-slate-900 pt-16 text-slate-400">
    <div class="mx-auto grid w-full max-w-[1200px] gap-10 px-5 pb-12 md:px-6 lg:grid-cols-[2fr_3fr] lg:gap-16">
      <div>
        <a href="index.php" class="flex items-center gap-2 text-[1.4rem] font-extrabold text-white">
          <span class="text-2xl">⚽</span>
          <span><?php echo $appName; ?></span>
        </a>
        <p class="mt-4 max-w-[320px] text-[0.9rem] leading-relaxed">
          Platform direktori dan booking slot lapangan olahraga Futsal, Badminton, dan Basket terdepan. Memudahkan pemain dan pemilik venue bertransaksi secara langsung.
        </p>
      </div>

      <div class="grid gap-6 md:grid-cols-3 md:gap-8">
        <div>
          <h4 class="mb-5 text-base font-bold text-white">Pemain</h4>
          <ul class="text-sm">
            <li class="mb-3"><a href="index.php?sport=futsal#direktori" class="transition hover:text-emerald-50">Cari Venue Futsal</a></li>
            <li class="mb-3"><a href="index.php?sport=badminton#direktori" class="transition hover:text-emerald-50">Cari Venue Badminton</a></li>
            <li class="mb-3"><a href="index.php?sport=basket#direktori" class="transition hover:text-emerald-50">Cari Venue Basket</a></li>
            <li class="mb-3"><a href="#riwayat" class="transition hover:text-emerald-50">Cek Status Booking</a></li>
          </ul>
        </div>
        <div>
          <h4 class="mb-5 text-base font-bold text-white">Pemilik Venue</h4>
          <ul class="text-sm">
            <li class="mb-3"><a href="#dashboard-mitra" class="transition hover:text-emerald-50">Cara Jadi Mitra</a></li>
            <li class="mb-3"><a href="#dashboard-mitra" class="transition hover:text-emerald-50">Dashboard Pengelola</a></li>
            <li class="mb-3"><a href="#dashboard-mitra" class="transition hover:text-emerald-50">Manajemen Jadwal Slot</a></li>
          </ul>
        </div>
        <div>
          <h4 class="mb-5 text-base font-bold text-white"><?php echo $appName; ?></h4>
          <ul class="text-sm">
            <li class="mb-3"><a href="#tentang" class="transition hover:text-emerald-50">Tentang Kami</a></li>
            <li class="mb-3"><a href="#syarat" class="transition hover:text-emerald-50">Syarat & Ketentuan</a></li>
            <li class="mb-3"><a href="#kontak" class="transition hover:text-emerald-50">Bantuan & Kontak</a></li>
          </ul>
        </div>
      </div>
    </div>

    <div class="border-t border-slate-700 py-6 text-[0.85rem]">
      <div class="mx-auto flex w-full max-w-[1200px] flex-wrap items-center justify-between gap-4 px-5 md:px-6">
        <p>&copy; 2026 <?php echo $appName; ?> — Tugas Web Programming Pertemuan 5 (Sprint 04). All rights reserved.</p>
        <span>MVP Semester 5 | Futsal • Badminton • Basket</span>
      </div>
    </div>
  </footer>

  <!-- JavaScript Client-Side (Tetap aktif untuk interaktivitas modal slot & navigasi mobile) -->
  <script src="js/main.js"></script>
</body>
</html>
