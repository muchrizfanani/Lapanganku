<?php
/**
 * Venue model – reads venue data from JSON.
 */
class Venue {
    private array $data;
    public function __construct(array $data) {
        $this->data = $data;
    }
    public static function getAll(): array {
        $store = DataStore::getInstance();
        return $store->read('data/venues.json');
    }
    public static function find(int $id): ?Venue {
        foreach (self::getAll() as $item) {
            if ($item['id'] === $id) {
                return new Venue($item);
            }
        }
        return null;
    }
    public function toArray(): array {
        return $this->data;
    }
    // Helper for price formatting
    public static function formatRupiah(int $angka): string {
        return "Rp " . number_format($angka, 0, ',', '.');
    }
    public static function statusBadge(string $status): array {
        if ($status === "available") {
            return ["label" => "Tersedia Hari Ini", "class" => "bg-emerald-500 text-white"];
        } elseif ($status === "limited") {
            return ["label" => "⚠️ Terisi Sebagian", "class" => "bg-amber-500 text-amber-900 font-extrabold"];
        } else {
            return ["label" => "Penuh Hari Ini", "class" => "bg-rose-500 text-white"];
        }
    }
}
?>