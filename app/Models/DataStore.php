<?php
/**
 * DataStore singleton for JSON file read/write
 */
class DataStore {
    private static ?DataStore $instance = null;
    private function __construct() {}
    public static function getInstance(): DataStore {
        if (self::$instance === null) {
            self::$instance = new DataStore();
        }
        return self::$instance;
    }
    /**
     * Read JSON file relative to project root.
     * @param string $relativePath path like 'data/bookings.json'
     * @return array decoded data or empty array
     */
    public function read(string $relativePath): array {
        $path = __DIR__ . '/../../' . $relativePath;
        if (!file_exists($path)) {
            return [];
        }
        $json = file_get_contents($path);
        return json_decode($json, true) ?: [];
    }
    /**
     * Write array data to JSON file.
     */
    public function write(string $relativePath, array $data): void {
        $path = __DIR__ . '/../../' . $relativePath;
        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));
    }
}
?>