<?php
/**
 * AuthController – handles login, registration, logout.
 */
class AuthController {
    public function showLogin() {
        include __DIR__ . '/../Views/login.php';
    }
    public function processLogin() {
        // Expect POST with email & password
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $user = User::authenticate($email, $password);
        if ($user) {
            session_start();
            $_SESSION['user'] = $user->toArray();
            header('Location: index.php?page=dashboard');
            exit;
        } else {
            echo "Login failed";
        }
    }
    public function showRegister() {
        include __DIR__ . '/../Views/register.php';
    }
    public function processRegister() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $name = $_POST['name'] ?? '';
        $role = $_POST['role'] ?? 'pemain';
        // Simple validation omitted
        $store = DataStore::getInstance();
        $users = $store->read('data/users.json');
        // hash password
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $newUser = ['id'=>count($users)+1,'email'=>$email,'password'=>$hashed,'name'=>$name,'role'=>$role];
        $users[] = $newUser;
        $store->write('data/users.json', $users);
        // Auto login
        session_start();
        $_SESSION['user'] = $newUser;
        header('Location: index.php?page=dashboard');
        exit;
    }
    public function logout() {
        session_start();
        session_destroy();
        header('Location: index.php');
        exit;
    }
}
?>