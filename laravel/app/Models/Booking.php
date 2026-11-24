<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'legacy_id',
        'user_id',
        'venue_id',
        'venue_name',
        'venue_location',
        'sport',
        'player_name',
        'player_phone',
        'booking_date',
        'slots',
        'total_slots',
        'price_per_hour',
        'total_price',
        'status',
        'payment_method',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'slots' => 'array',
            'total_slots' => 'integer',
            'price_per_hour' => 'integer',
            'total_price' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }
}
