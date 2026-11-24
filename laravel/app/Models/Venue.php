<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = [
        'legacy_id',
        'owner_id',
        'name',
        'sport',
        'price',
        'location',
        'address',
        'facilities',
        'rating',
        'reviews',
        'status',
        'gradient',
        'sport_badge',
    ];

    protected function casts(): array
    {
        return [
            'legacy_id' => 'integer',
            'price' => 'integer',
            'rating' => 'decimal:2',
            'reviews' => 'integer',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
