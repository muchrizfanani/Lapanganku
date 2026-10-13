<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    // Coba fallback ke $_POST biasa
    $input = $_POST;
}

$playerName = trim($input['player_name'] ?? '');
$playerPhone = trim($input['player_phone'] ?? '');
$venueName = trim($input['venue_name'] ?? 'Venue LapanganKu');
$venueLocation = trim($input['venue_location'] ?? 'Indonesia');
$sport = trim($input['sport'] ?? 'Futsal');
$bookingDate = trim($input['booking_date'] ?? date('Y-m-d'));
$slots = $input['slots'] ?? ['19:00 - 20:00'];
$totalPrice = intval($input['total_price'] ?? 100000);
$pricePerHour = intval($input['price_per_hour'] ?? 100000);

if (empty($playerName) || empty($playerPhone)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Nama dan nomor telepon wajib diisi.']);
    exit;
}

$newBooking = addBooking([
    'venue_name' => $venueName,
    'venue_location' => $venueLocation,
    'sport' => $sport,
    'player_name' => $playerName,
    'player_phone' => $playerPhone,
    'booking_date' => $bookingDate,
    'slots' => $slots,
    'total_slots' => count($slots),
    'price_per_hour' => $pricePerHour,
    'total_price' => $totalPrice
]);

echo json_encode([
    'success' => true,
    'message' => 'Booking berhasil dibuat.',
    'data' => $newBooking
]);
?>
