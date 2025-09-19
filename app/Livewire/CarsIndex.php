<?php

namespace App\Livewire;

use App\Services\CarSearchService;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CarsIndex extends Component
{
    use WithPagination;

    // URL-synced search and filters
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
    public ?string $sort = 'newest';

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
        // Lazily resolve service if Livewire hydration didn't run mount yet in some edge path
        return $this->service ??= app(CarSearchService::class);
    }

    public function updating($name, $value): void
    {
        $this->resetPage();
    }

    public function refreshSearch(): void
    {
        // Explicitly trigger re-render and reset pagination when user clicks Search
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
        $this->sort = 'newest';
        $this->resetPage();
    }

    public function render()
    {
        $options = $this->service()->options();

        return view('livewire.cars-index', [
            'cars' => $this->service()->paginate($this->params(), 12),
            'options' => $options,
        ]);
    }
}
