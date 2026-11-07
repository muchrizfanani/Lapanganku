<?php
/**
 * DashboardController – shows owner dashboard and booking history.
 */
class DashboardController {
    // Owner dashboard – shows all bookings (could be filtered by owner later)
    public function ownerDashboard() {
        $bookings = Booking::getAll();
        $viewPath = __DIR__ . '/../Views/dashboard-pemilik.php';
        include $viewPath;
    }
    // Booking history for logged-in user
    public function bookingHistory() {
        $current = getCurrentUser();
        $userId = $current['id'] ?? null;
        $bookings = $userId ? Booking::findByUser($userId) : [];
        $viewPath = __DIR__ . '/../Views/riwayat-booking.php';
        include $viewPath;
    }
}
?>