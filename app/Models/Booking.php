<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'car_id',
        'service_type_id',
        'pickup_location',
        'dropoff_location',
        'start_date',
        'end_date',
        'extras',
        'notes',
        'subtotal',
        'taxes',
        'total',
        'status',
        'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'extras' => 'array',
            'start_date' => 'date',
            'end_date' => 'date',
            'subtotal' => 'decimal:2',
            'taxes' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function getStatusAttribute($value)
    {
        return strtolower($value);
    }
}
