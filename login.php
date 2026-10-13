<?php
require_once __DIR__ . '/includes/auth.php';

$error = '';
$success = '';

// Proses Demo Quick Login
if (isset($_GET['demo'])) {
    $demoRole = $_GET['demo'];
    if ($demoRole === 'pemilik') {
        $_SESSION['user'] = [
            'name' => 'Bapak Hendra (Mitra)',
            'email' => 'hendra.venue@lapanganku.id',
            'role' => 'pemilik',
            'venue_name' => 'Champion Futsal Center Arena'
        ];
        header("Location: dashboard-pemilik.php");
        exit;
    } else {
        $_SESSION['user'] = [
            'name' => 'Muchamad Rizky Fanani',
            'email' => 'fanani@student.id',
            'role' => 'pemain',
            'phone' => '082235577906'
        ];
        header("Location: riwayat-booking.php");
        exit;
    }
}

// Proses Submit Form Login Manual
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = trim($_POST['role'] ?? 'pemain');

    if (empty($email) || empty($password)) {
        $error = 'Email dan Password wajib diisi.';
    } else {
        // Simulasi otentikasi login
        $_SESSION['user'] = [
            'name' => ($role === 'pemilik') ? 'Mitra Pengelola Venue' : 'Pengguna LapanganKu',
            'email' => $email,
            'role' => $role,
            'venue_name' => ($role === 'pemilik') ? 'Champion Futsal Center Arena' : ''
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
  <title>Masuk — LapanganKu</title>
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

  <div class="w-full max-w-md">
    <!-- Header Logo -->
    <div class="mb-8 text-center">
      <a href="index.php" class="inline-flex items-center gap-2 text-2xl font-extrabold text-slate-900">
        <span class="text-3xl">⚽</span>
        <span>Lapangan<span class="text-emerald-600">Ku</span></span>
      </a>
      <p class="mt-2 text-sm text-slate-500">Pilih peran dan masuk ke akun Anda</p>
    </div>

    <!-- Quick Demo Box -->
    <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50/70 p-4 text-xs">
      <p class="font-bold text-emerald-900">⚡ 1-Klik Demo Login (Langsung Coba):</p>
      <div class="mt-2.5 flex gap-2">
        <a href="login.php?demo=pemain" class="flex-1 rounded-xl bg-emerald-600 px-3 py-2 text-center font-bold text-white shadow-sm transition hover:bg-emerald-700">
          👤 Masuk sbg Pemain
        </a>
        <a href="login.php?demo=pemilik" class="flex-1 rounded-xl bg-slate-900 px-3 py-2 text-center font-bold text-white shadow-sm transition hover:bg-slate-800">
          🏢 Masuk sbg Mitra Venue
        </a>
      </div>
    </div>

    <!-- Card Form -->
    <div class="rounded-3xl border border-slate-200 bg-white p-7 shadow-xl shadow-slate-200/50">
      
      <?php if (!empty($error)): ?>
        <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs font-semibold text-rose-700">
          ⚠️ <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="login.php" class="space-y-4">
        <!-- Role Selection -->
        <div>
          <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-600">Masuk Sebagai</label>
          <div class="grid grid-cols-2 gap-2 rounded-xl bg-slate-100 p-1 text-sm font-semibold">
            <label class="flex cursor-pointer items-center justify-center gap-1.5 rounded-lg py-2 transition has-[:checked]:bg-white has-[:checked]:text-emerald-700 has-[:checked]:shadow-sm">
              <input type="radio" name="role" value="pemain" checked class="hidden">
              <span>👤 Pemain</span>
            </label>
            <label class="flex cursor-pointer items-center justify-center gap-1.5 rounded-lg py-2 transition has-[:checked]:bg-white has-[:checked]:text-emerald-700 has-[:checked]:shadow-sm">
              <input type="radio" name="role" value="pemilik" class="hidden">
              <span>🏢 Pemilik Venue</span>
            </label>
          </div>
        </div>

        <div>
          <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-600">Email / Username</label>
          <input type="email" name="email" required placeholder="nama@email.com" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none transition focus:border-emerald-600 focus:bg-white focus:ring-4 focus:ring-emerald-600/10">
        </div>

        <div>
          <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-600">Password</label>
          <input type="password" name="password" required placeholder="••••••••" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none transition focus:border-emerald-600 focus:bg-white focus:ring-4 focus:ring-emerald-600/10">
        </div>

        <button type="submit" class="w-full rounded-xl bg-emerald-600 py-3.5 text-sm font-bold text-white shadow-lg shadow-emerald-600/30 transition hover:bg-emerald-700 hover:shadow-emerald-600/40">
          Masuk ke LapanganKu
        </button>
      </form>

      <div class="mt-6 border-t border-slate-100 pt-5 text-center text-xs text-slate-500">
        Belum punya akun? 
        <a href="register.php" class="font-bold text-emerald-600 hover:underline">Daftar sekarang</a>
      </div>
    </div>

    <div class="mt-6 text-center">
      <a href="index.php" class="text-xs font-semibold text-slate-500 hover:text-slate-900">&larr; Kembali ke Beranda</a>
    </div>
  </div>

</body>
</html>
