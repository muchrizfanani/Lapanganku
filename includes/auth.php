<?php
/**
 * Auth Helper & Session Management
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Cek apakah user sudah login
 */
function isLoggedIn() {
    return isset($_SESSION['user']);
}

/**
 * Dapatkan user aktif
 */
function getCurrentUser() {
    return $_SESSION['user'] ?? null;
}

/**
 * Dapatkan role user aktif ('pemain' | 'pemilik')
 */
function getCurrentRole() {
    return $_SESSION['user']['role'] ?? null;
}

/**
 * Wajibkan login
 */
function requireLogin($requiredRole = null) {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
    if ($requiredRole && getCurrentRole() !== $requiredRole) {
        header("Location: index.php");
        exit;
    }
}

/**
 * Baca seluruh bookings dari JSON
 */
function getBookings() {
    $filePath = __DIR__ . '/../data/bookings.json';
    if (!file_exists($filePath)) {
        return [];
    }
    $json = file_get_contents($filePath);
    return json_decode($json, true) ?: [];
}

/**
 * Simpan bookings ke JSON
 */
function saveBookings($bookings) {
    $filePath = __DIR__ . '/../data/bookings.json';
    file_put_contents($filePath, json_encode($bookings, JSON_PRETTY_PRINT));
}

/**
 * Tambah booking baru
 */
function addBooking($data) {
    $bookings = getBookings();
    $newId = "BK-" . date('Ym') . "-" . str_pad(count($bookings) + 1, 3, '0', STR_PAD_LEFT);
    $newBooking = array_merge([
        "id" => $newId,
        "created_at" => date('Y-m-d H:i:s'),
        "status" => "pending",
        "payment_method" => "Bayar di Tempat (COD)"
    ], $data);

    array_unshift($bookings, $newBooking);
    saveBookings($bookings);
    return $newBooking;
}
?>
