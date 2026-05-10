<?php

namespace App\Livewire;

use App\Services\CarSearchService;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CarsHome extends Component
{
    use WithPagination;

    // Search and filters synced to URL
    #[Url]
    public string $q = '';
    #[Url]
    public ?string $category = null;
    #[Url]
    public ?string $location = null;
    #[Url]
    public ?string $minPrice = null;
    #[Url]
    public ?string $maxPrice = null;
    #[Url]
    public ?int $seats = null;
    #[Url]
    public ?string $transmission = null;
    #[Url]
    public ?string $fuelType = null;
    #[Url]
    public ?string $startDate = null;
    #[Url]
    public ?string $endDate = null;
    #[Url]
    public ?string $sort = 'newest';

    public int $perPage = 8;

    // UI state
    public bool $showAdvanced = false;

    protected ?CarSearchService $service = null;

    public function mount(CarSearchService $service): void
    {
        // Use mount for DI to ensure compatibility and avoid serialization issues
        $this->service = $service;
    }

    protected function service(): CarSearchService
    {
        // Lazily resolve service in case mount hasn't run in an edge lifecycle path
        return $this->service ??= app(CarSearchService::class);
    }

    public function updating($name, $value): void
    {
        // Reset page and count on any filter change
        $this->perPage = 8;
        $this->resetPage();
    }

    public function loadMore(): void
    {
        $this->perPage += 8;
    }

    public function refreshSearch(): void
    {
        // Explicitly trigger re-render and reset pagination when user clicks Search
        $this->perPage = 8;
        $this->resetPage();
    }

    public function toggleAdvanced(): void
    {
        $this->showAdvanced = ! $this->showAdvanced;
    }

    protected function params(): array
    {
        return [
            'q' => $this->q,
            'category' => $this->category,
            'location' => $this->location,
            'minPrice' => $this->minPrice,
            'maxPrice' => $this->maxPrice,
            'seats' => $this->seats,
            'transmission' => $this->transmission,
            'fuelType' => $this->fuelType,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'sort' => $this->sort,
        ];
    }

    public function resetFilters(): void
    {
        $this->q = '';
        $this->category = null;
        $this->location = null;
        $this->minPrice = null;
        $this->maxPrice = null;
        $this->seats = null;
        $this->transmission = null;
        $this->fuelType = null;
        $this->startDate = null;
        $this->endDate = null;
        $this->sort = 'newest';
        $this->perPage = 8;
        $this->resetPage();
    }

    protected function isSearching(): bool
    {
        return trim((string) $this->q) !== ''
            || !empty($this->category)
            || !empty($this->location)
            || ($this->minPrice !== null && $this->minPrice !== '')
            || ($this->maxPrice !== null && $this->maxPrice !== '')
            || !empty($this->seats)
            || !empty($this->transmission)
            || !empty($this->fuelType)
            || !empty($this->startDate)
            || !empty($this->endDate);
    }

    public function render()
    {
        $options = $this->service()->options();
        $showFeatured = ! $this->isSearching();

        return view('livewire.cars-home', [
            'featured' => $showFeatured ? $this->service()->featured(3) : collect(),
            'catalog' => $this->service()->paginate($this->params(), $this->perPage),
            'options' => $options,
            'showFeatured' => $showFeatured,
        ]);
    }
}
