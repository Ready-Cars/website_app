<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Car extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'category',
        'description',
        'image_url',
        'daily_price',
        'seats',
        'transmission',
        'fuel_type',
        'featured',
        'is_active',
        'location',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'is_active' => 'boolean',
        'daily_price' => 'decimal:2',
        'images' => 'array',
    ];

    /**
     * Scope: full-text like search across name, category, and location.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);
        if ($term === '') {
            return $query;
        }
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('category', 'like', "%{$term}%")
              ->orWhere('location', 'like', "%{$term}%");
        });
    }

    /**
     * Scope: apply structured filters.
     * Supported keys: category, location, min_price, max_price, seats, transmission, fuel_type, featured
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when(!empty($filters['category']), fn($q) => $q->where('category', $filters['category']))
            ->when(!empty($filters['location']), fn($q) => $q->where('location', 'like', "%{$filters['location']}%"))
            ->when(isset($filters['min_price']) && $filters['min_price'] !== '', fn($q) => $q->where('daily_price', '>=', (float) $filters['min_price']))
            ->when(isset($filters['max_price']) && $filters['max_price'] !== '', fn($q) => $q->where('daily_price', '<=', (float) $filters['max_price']))
            ->when(!empty($filters['seats']), fn($q) => $q->where('seats', (int) $filters['seats']))
            ->when(!empty($filters['transmission']), fn($q) => $q->where('transmission', $filters['transmission']))
            ->when(!empty($filters['fuel_type']), fn($q) => $q->where('fuel_type', $filters['fuel_type']))
            ->when(array_key_exists('featured', $filters) && $filters['featured'] !== null && $filters['featured'] !== '', fn($q) => $q->where('featured', (bool) $filters['featured']));
    }

    /**
     * Scope: sort by a given option: newest, price_asc, price_desc.
     */
    public function scopeSort(Builder $query, ?string $sort): Builder
    {
        return match ($sort) {
            'price_asc' => $query->orderBy('daily_price', 'asc'),
            'price_desc' => $query->orderBy('daily_price', 'desc'),
            default => $query->latest('id'),
        };
    }
}
