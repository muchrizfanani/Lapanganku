<?php
require_once __DIR__ . '/includes/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = trim($_POST['role'] ?? 'pemain');
    $venueName = trim($_POST['venue_name'] ?? '');

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Nama, Email, dan Password wajib diisi.';
    } else {
        $_SESSION['user'] = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'role' => $role,
            'venue_name' => ($role === 'pemilik') ? ($venueName ?: 'Venue Olahraga Baru') : ''
        ];

        if ($role === 'pemilik') {
            header("Location: dashboard-pemilik.php");
        } else {
            header("Location: index.php");
        }
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Akun — LapanganKu</title>
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
<body class="flex min-h-screen items-center justify-center bg-slate-50 p-4 font-sans text-slate-900">

  <div class="w-full max-w-md py-6">
    <div class="mb-6 text-center">
      <a href="index.php" class="inline-flex items-center gap-2 text-2xl font-extrabold text-slate-900">
        <span class="text-3xl">⚽</span>
        <span>Lapangan<span class="text-emerald-600">Ku</span></span>
      </a>
      <p class="mt-2 text-sm text-slate-500">Buat akun pemain atau daftarkan venue Anda</p>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-7 shadow-xl shadow-slate-200/50">
      
      <?php if (!empty($error)): ?>
        <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs font-semibold text-rose-700">
          ⚠️ <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="register.php" class="space-y-4">
        <!-- Role Selection -->
        <div>
          <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-600">Daftar Sebagai</label>
          <div class="grid grid-cols-2 gap-2 rounded-xl bg-slate-100 p-1 text-sm font-semibold">
            <label class="flex cursor-pointer items-center justify-center gap-1.5 rounded-lg py-2 transition has-[:checked]:bg-white has-[:checked]:text-emerald-700 has-[:checked]:shadow-sm">
              <input type="radio" name="role" value="pemain" id="rolePemain" checked class="hidden" onchange="toggleVenueField()">
              <span>👤 Pemain</span>
            </label>
            <label class="flex cursor-pointer items-center justify-center gap-1.5 rounded-lg py-2 transition has-[:checked]:bg-white has-[:checked]:text-emerald-700 has-[:checked]:shadow-sm">
              <input type="radio" name="role" value="pemilik" id="rolePemilik" class="hidden" onchange="toggleVenueField()">
              <span>🏢 Pemilik Venue</span>
            </label>
          </div>
        </div>

        <div>
          <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-600">Nama Lengkap</label>
          <input type="text" name="name" required placeholder="Nama Anda" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none transition focus:border-emerald-600 focus:bg-white focus:ring-4 focus:ring-emerald-600/10">
        </div>

        <!-- Khusus Pemilik Venue -->
        <div id="venueField" class="hidden">
          <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-600">Nama Venue / Lapangan</label>
          <input type="text" name="venue_name" placeholder="Misal: Futsal Arena Galaxy" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none transition focus:border-emerald-600 focus:bg-white focus:ring-4 focus:ring-emerald-600/10">
        </div>

        <div>
          <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-600">Nomor WhatsApp / HP</label>
          <input type="tel" name="phone" placeholder="082235577906" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none transition focus:border-emerald-600 focus:bg-white focus:ring-4 focus:ring-emerald-600/10">
        </div>

        <div>
          <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-600">Email</label>
          <input type="email" name="email" required placeholder="nama@email.com" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none transition focus:border-emerald-600 focus:bg-white focus:ring-4 focus:ring-emerald-600/10">
        </div>

        <div>
          <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-600">Password</label>
          <input type="password" name="password" required placeholder="Minimal 6 karakter" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none transition focus:border-emerald-600 focus:bg-white focus:ring-4 focus:ring-emerald-600/10">
        </div>

        <button type="submit" class="w-full rounded-xl bg-emerald-600 py-3.5 text-sm font-bold text-white shadow-lg shadow-emerald-600/30 transition hover:bg-emerald-700 hover:shadow-emerald-600/40">
          Daftar Sekarang
        </button>
      </form>

      <div class="mt-6 border-t border-slate-100 pt-5 text-center text-xs text-slate-500">
        Sudah memiliki akun? 
        <a href="login.php" class="font-bold text-emerald-600 hover:underline">Masuk di sini</a>
      </div>
    </div>

    <div class="mt-6 text-center">
      <a href="index.php" class="text-xs font-semibold text-slate-500 hover:text-slate-900">&larr; Kembali ke Beranda</a>
    </div>
  </div>

  <script>
    function toggleVenueField() {
      const isPemilik = document.getElementById('rolePemilik').checked;
      const venueField = document.getElementById('venueField');
      if (isPemilik) {
        venueField.classList.remove('hidden');
      } else {
        venueField.classList.add('hidden');
      }
    }
  </script>
</body>
</html>
