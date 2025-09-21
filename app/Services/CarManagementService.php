<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Car;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class CarManagementService
{
    /**
     * Paginated cars with filters and robust search.
     * Filters: q, category, transmission, fuel_type, seats, featured, minPrice, maxPrice, perPage
     */
    public function queryCars(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $q = Car::query()
            ->when(isset($filters['q']) && trim((string)$filters['q']) !== '', function (Builder $query) use ($filters) {
                $raw = trim((string)$filters['q']);
                $term = '%'.$raw.'%';
                $query->where(function(Builder $sub) use ($term) {
                    $sub->where('name', 'like', $term)
                        ->orWhere('category', 'like', $term)
                        ->orWhere('location', 'like', $term)
                        ->orWhere('transmission', 'like', $term)
                        ->orWhere('fuel_type', 'like', $term);
                });
            })
            ->when(!empty($filters['category']), fn(Builder $qb) => $qb->where('category', $filters['category']))
            ->when(!empty($filters['transmission']), fn(Builder $qb) => $qb->where('transmission', $filters['transmission']))
            ->when(!empty($filters['fuel_type']), fn(Builder $qb) => $qb->where('fuel_type', $filters['fuel_type']))
            ->when(!empty($filters['seats']), fn(Builder $qb) => $qb->where('seats', (int)$filters['seats']))
            ->when(array_key_exists('featured', $filters) && $filters['featured'] !== '' && $filters['featured'] !== null,
                fn(Builder $qb) => $qb->where('featured', (bool)$filters['featured']))
            ->when(isset($filters['minPrice']) && $filters['minPrice'] !== '', fn(Builder $qb) => $qb->where('daily_price', '>=', (float)$filters['minPrice']))
            ->when(isset($filters['maxPrice']) && $filters['maxPrice'] !== '', fn(Builder $qb) => $qb->where('daily_price', '<=', (float)$filters['maxPrice']))
            ->latest('id');

        $size = max(5, min(100, (int)($filters['perPage'] ?? $perPage)));
        return $q->paginate($size)->withQueryString();
    }

    /**
     * Recent bookings for a car (paginated) to show in modal.
     */
    public function bookingsForCar(int $carId, int $perPage = 10): LengthAwarePaginator
    {
        return Booking::with(['user','car'])
            ->where('car_id', $carId)
            ->latest('id')
            ->paginate(max(5, min(50, $perPage)))
            ->withQueryString();
    }

    /**
     * Options for dropdown filters in UI.
     */
    public function getFilterOptions(): array
    {
        // Prefer admin-managed options when available
        $opt = \App\Models\CarAttributeOption::query();
        $managed = [
            'categories' => $opt->clone()->where('type', 'category')->orderBy('value')->pluck('value')->filter()->values()->all(),
            'transmissions' => $opt->clone()->where('type', 'transmission')->orderBy('value')->pluck('value')->filter()->values()->all(),
            'fuels' => $opt->clone()->where('type', 'fuel')->orderBy('value')->pluck('value')->filter()->values()->all(),
        ];
        return [
            'categories' => !empty($managed['categories']) ? $managed['categories'] : Car::query()->distinct()->orderBy('category')->pluck('category')->filter()->values()->all(),
            'transmissions' => !empty($managed['transmissions']) ? $managed['transmissions'] : Car::query()->distinct()->orderBy('transmission')->pluck('transmission')->filter()->values()->all(),
            'fuels' => !empty($managed['fuels']) ? $managed['fuels'] : Car::query()->distinct()->orderBy('fuel_type')->pluck('fuel_type')->filter()->values()->all(),
            'seats' => Car::query()->distinct()->orderBy('seats')->pluck('seats')->filter()->values()->all(),
            'perPages' => [10,25,50,100],
        ];
    }

    /** Normalize multiple image URLs from an array of strings. */
    public function normalizeImages($value): array
    {
        if (is_array($value)) {
            return array_values(array_filter(array_map(function($v){ return is_string($v) ? trim($v) : null; }, $value)));
        }
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $this->normalizeImages($decoded);
            }
            if (str_contains($value, ',')) {
                return $this->normalizeImages(array_map('trim', explode(',', $value)));
            }
            if (filter_var($value, FILTER_VALIDATE_URL)) return [$value];
        }
        return [];
    }

    /** Create a car from validated data. */
    public function createCar(array $data): Car
    {
        $images = $this->normalizeImages($data['images'] ?? []);
        $payload = Arr::only($data, ['name','category','description','image_url','daily_price','seats','transmission','fuel_type','featured','location']);
        $payload['featured'] = (bool)($payload['featured'] ?? false);
        $payload['images'] = $images;
        return Car::create($payload);
    }

    /** Update a car with validated data. */
    public function updateCar(Car $car, array $data): Car
    {
        $images = $this->normalizeImages($data['images'] ?? []);
        $payload = Arr::only($data, ['name','category','description','image_url','daily_price','seats','transmission','fuel_type','featured','location']);
        $payload['featured'] = (bool)($payload['featured'] ?? false);
        $payload['images'] = $images;
        $car->update($payload);
        return $car->fresh();
    }

    public function deleteCar(Car $car): void
    {
        $car->delete();
    }

    /** Activate/deactivate a car. */
    public function setActive(Car $car, bool $active): Car
    {
        $car->update(['is_active' => $active]);
        return $car->fresh();
    }

    /**
     * Determine availability for today for a list of car IDs.
     * Returns an associative array: [car_id => bool available]
     */
    public function availabilityForCars(array $carIds, ?string $date = null): array
    {
        $carIds = array_values(array_unique(array_filter(array_map('intval', $carIds))));
        if (empty($carIds)) return [];
        $date = $date ?: now()->toDateString();
        // A car is unavailable if it has any non-cancelled booking overlapping the given date
        $busy = Booking::query()
            ->whereIn('car_id', $carIds)
            ->whereIn('status', ['pending','confirmed'])
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->pluck('car_id')
            ->all();
        $busySet = array_fill_keys($busy, true);
        $out = [];
        foreach ($carIds as $id) {
            $out[$id] = !isset($busySet[$id]);
        }
        return $out;
    }
}
