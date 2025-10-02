<?php

namespace App\Services;

use App\Models\Car;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class CarSearchService
{
    public function buildQuery(array $params): Builder
    {
        $q = Car::query()
            ->where('is_active', true)
            ->search($params['q'] ?? null)
            ->filter([
                'category' => $params['category'] ?? null,
                'location' => $params['location'] ?? null,
                'min_price' => $params['minPrice'] ?? $params['min_price'] ?? null,
                'max_price' => $params['maxPrice'] ?? $params['max_price'] ?? null,
                'seats' => $params['seats'] ?? null,
                'transmission' => $params['transmission'] ?? null,
                'fuel_type' => $params['fuelType'] ?? $params['fuel_type'] ?? null,
                'featured' => $params['featured'] ?? null,
            ])
            ->sort($params['sort'] ?? null);

        return $q;
    }

    public function paginate(array $params, int $perPage = 12): LengthAwarePaginator
    {
        return $this->buildQuery($params)->paginate($perPage)->withQueryString();
    }

    public function featured(int $limit = 3)
    {
        return Car::query()->where('is_active', true)->where('featured', true)->latest('id')->limit($limit)->get();
    }

    public function options(): array
    {
        // Prefer admin-managed options when available, falling back to data derived from cars
        $opt = \App\Models\CarAttributeOption::query();
        $managedLocations = $opt->clone()->where('type', 'location')->orderBy('value')->pluck('value')->filter()->values()->all();

        return [
            'categories' => Car::query()->whereNotNull('category')->distinct()->orderBy('category')->pluck('category')->all(),
            'transmissions' => Car::query()->whereNotNull('transmission')->distinct()->orderBy('transmission')->pluck('transmission')->all(),
            'fuels' => Car::query()->whereNotNull('fuel_type')->distinct()->orderBy('fuel_type')->pluck('fuel_type')->all(),
            'seats' => Car::query()->distinct()->orderBy('seats')->pluck('seats')->all(),
            'locations' => ! empty($managedLocations)
                ? $managedLocations
                : Car::query()->whereNotNull('location')->distinct()->orderBy('location')->pluck('location')->filter()->values()->all(),
            'sorts' => [
                'newest' => 'Newest',
                'price_asc' => 'Price: Low to High',
                'price_desc' => 'Price: High to Low',
            ],
        ];
    }
}
