<x-layouts.main
    title="Rent {{ $car->name }} - {{ config('app.name') }}"
    description="{{ \Illuminate\Support\Str::limit(strip_tags((string) ($car->description ?: ('Rent ' . $car->name . ' in ' . ($car->location ?: 'Nigeria') . ' from ReadyCars.'))), 155) }}"
    keywords="{{ implode(', ', array_filter([$car->name . ' rental', $car->category ? strtolower($car->category) . ' rental' : null, $car->location ? strtolower($car->location) . ' car hire' : null, 'readycars'])) }}"
    canonical="{{ route('rent.show', $car) }}"
    og-type="product"
    og-image="{{ $car->image_url ?: asset('favicon.ico') }}"
>
    <livewire:rent-car :car="$car" />

</x-layouts.main>

