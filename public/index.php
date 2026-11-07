<?php
// Front controller for MVC routing
require __DIR__ . '/../app/Models/DataStore.php';
require __DIR__ . '/../app/Models/Venue.php';
require __DIR__ . '/../app/Models/Booking.php';
require __DIR__ . '/../app/Models/User.php';
require __DIR__ . '/../app/Controllers/HomeController.php';
require __DIR__ . '/../app/Controllers/AuthController.php';
require __DIR__ . '/../app/Controllers/DashboardController.php';
require __DIR__ . '/../app/Controllers/BookingController.php';

// Start session for auth
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page = $_GET['page'] ?? 'home';
$controller = null;
switch ($page) {
    case 'login':
        $controller = new AuthController();
        $controller->showLogin();
        break;
    case 'login_process':
        $controller = new AuthController();
        $controller->processLogin();
        break;
    case 'register':
        $controller = new AuthController();
        $controller->showRegister();
        break;
    case 'register_process':
        $controller = new AuthController();
        $controller->processRegister();
        break;
    case 'logout':
        $controller = new AuthController();
        $controller->logout();
        break;
    case 'dashboard':
        $controller = new DashboardController();
        $controller->ownerDashboard();
        break;
    case 'history':
        $controller = new DashboardController();
        $controller->bookingHistory();
        break;
    case 'book':
        $controller = new BookingController();
        $controller->handleBooking();
        break;
    default:
        $controller = new HomeController();
        $controller->index();
}
?>