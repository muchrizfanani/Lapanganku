<?php
/**
 * HomeController – shows homepage with venue listings.
 */
class HomeController {
    public function index() {
        // Load venues via OOP model
        $venues = Venue::getAll();
        // Pass data to view
        $viewPath = __DIR__ . '/../Views/home.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "<h1>Home view not found</h1>";
        }
    }
}
?>