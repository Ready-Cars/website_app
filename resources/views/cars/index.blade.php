<x-layouts.main
    title="Browse Available Cars - {{ config('app.name') }}"
    description="Explore the ReadyCars fleet and compare pricing, categories, and locations to rent the right car for your trip."
    keywords="rent a car nigeria, car hire fleet, suv rental lagos, sedan rental nigeria"
    canonical="{{ route('cars.index') }}"
>
    <livewire:cars-index />
</x-layouts.main>
