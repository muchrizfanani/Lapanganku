<?php
/**
 * User model – handles user data stored in JSON.
 */
class User {
    private array $data;
    public function __construct(array $data) {
        $this->data = $data;
    }
    public static function getAll(): array {
        $store = DataStore::getInstance();
        return $store->read('data/users.json');
    }
    public static function findByEmail(string $email): ?User {
        foreach (self::getAll() as $item) {
            if (isset($item['email']) && $item['email'] === $email) {
                return new User($item);
            }
        }
        return null;
    }
    public static function authenticate(string $email, string $password): ?User {
        $user = self::findByEmail($email);
        if ($user && password_verify($password, $user->data['password'])) {
            return $user;
        }
        return null;
    }
    public function toArray(): array {
        return $this->data;
    }
}
?>