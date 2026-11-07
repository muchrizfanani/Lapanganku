<?php
/**
 * Booking model – handles bookings stored in JSON.
 */
class Booking {
    private array $data;
    public function __construct(array $data) {
        $this->data = $data;
    }
    public static function getAll(): array {
        $store = DataStore::getInstance();
        return $store->read('data/bookings.json');
    }
    public static function create(array $payload): array {
        $store = DataStore::getInstance();
        $bookings = $store->read('data/bookings.json');
        $newId = "BK-" . date('Ym') . "-" . str_pad(count($bookings) + 1, 3, '0', STR_PAD_LEFT);
        $newBooking = array_merge([
            'id' => $newId,
            'created_at' => date('Y-m-d H:i:s'),
            'status' => 'pending',
            'payment_method' => 'Bayar di Tempat (COD)'
        ], $payload);
        array_unshift($bookings, $newBooking);
        $store->write('data/bookings.json', $bookings);
        return $newBooking;
    }
    public static function findByUser(int $userId): array {
        $all = self::getAll();
        $result = [];
        foreach ($all as $b) {
            if (isset($b['user_id']) && $b['user_id'] == $userId) {
                $result[] = $b;
            }
        }
        return $result;
    }
    public function toArray(): array {
        return $this->data;
    }
    public static function statusBadge(string $status): array {
        if ($status === "pending") {
            return ["label" => "Menunggu Konfirmasi", "class" => "bg-yellow-500 text-white"];
        } elseif ($status === "confirmed") {
            return ["label" => "Dikonfirmasi", "class" => "bg-emerald-500 text-white"];
        } else {
            return ["label" => "Selesai", "class" => "bg-gray-500 text-white"];
        }
    }
}
?>