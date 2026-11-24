<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use JsonException;
use RuntimeException;

#[Signature('app:import-legacy-data')]
#[Description('Import LapanganKu JSON venue and booking data into MySQL.')]
class ImportLegacyData extends Command
{
    public function handle(): int
    {
        try {
            $venues = $this->readRows('data/venues.json');
            $bookings = $this->readRows('data/bookings.json');
        } catch (RuntimeException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        DB::transaction(function () use ($venues, $bookings): void {
            foreach ($venues as $row) {
                if (! isset($row['id'], $row['name'])) {
                    throw new RuntimeException('Each legacy venue must include an id and name.');
                }

                Venue::updateOrCreate(
                    ['legacy_id' => (int) $row['id']],
                    [
                        'name' => $row['name'],
                        'sport' => $row['sport'] ?? 'Olahraga',
                        'price' => (int) ($row['price'] ?? 0),
                        'location' => $row['location'] ?? 'Indonesia',
                        'address' => $row['address'] ?? '',
                        'facilities' => $row['facilities'] ?? null,
                        'rating' => $row['rating'] ?? null,
                        'reviews' => (int) ($row['reviews'] ?? 0),
                        'status' => $row['status'] ?? 'available',
                        'gradient' => $row['gradient'] ?? null,
                        'sport_badge' => $row['sport_badge'] ?? null,
                    ],
                );
            }

            foreach ($bookings as $row) {
                if (! isset($row['id'], $row['player_name'], $row['booking_date'])) {
                    throw new RuntimeException('Each legacy booking must include an id, player name, and booking date.');
                }

                $venue = Venue::query()
                    ->where('name', $row['venue_name'] ?? '')
                    ->where('location', $row['venue_location'] ?? '')
                    ->first();
                $slots = $row['slots'] ?? [];
                if (! is_array($slots)) {
                    throw new RuntimeException("Booking {$row['id']} has an invalid slots value.");
                }
                $userId = isset($row['user_id']) ? User::query()->whereKey($row['user_id'])->value('id') : null;

                Booking::updateOrCreate(
                    ['legacy_id' => (string) $row['id']],
                    [
                        'user_id' => $userId,
                        'venue_id' => $venue?->id,
                        'venue_name' => $row['venue_name'] ?? 'Venue LapanganKu',
                        'venue_location' => $row['venue_location'] ?? 'Indonesia',
                        'sport' => $row['sport'] ?? 'Futsal',
                        'player_name' => $row['player_name'],
                        'player_phone' => $row['player_phone'] ?? '',
                        'booking_date' => $row['booking_date'],
                        'slots' => $slots,
                        'total_slots' => (int) ($row['total_slots'] ?? count($slots)),
                        'price_per_hour' => (int) ($row['price_per_hour'] ?? 0),
                        'total_price' => (int) ($row['total_price'] ?? 0),
                        'status' => $row['status'] ?? 'pending',
                        'payment_method' => $row['payment_method'] ?? 'Bayar di Tempat (COD)',
                        'created_at' => $row['created_at'] ?? now(),
                    ],
                );
            }
        });

        $this->info(sprintf('Imported %d venues and %d bookings. Source JSON files were left unchanged.', count($venues), count($bookings)));

        return self::SUCCESS;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function readRows(string $relativePath): array
    {
        $path = base_path('../'.$relativePath);
        if (! is_file($path) || ! is_readable($path)) {
            throw new RuntimeException("Cannot read legacy data file: {$path}");
        }

        $contents = file_get_contents($path);
        if ($contents === false) {
            throw new RuntimeException("Failed to load legacy data file: {$path}");
        }

        try {
            $rows = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException("Invalid JSON in {$path}: {$exception->getMessage()}", previous: $exception);
        }

        if (! is_array($rows) || ! array_is_list($rows)) {
            throw new RuntimeException("Legacy data must be a JSON list: {$path}");
        }

        return $rows;
    }
}
