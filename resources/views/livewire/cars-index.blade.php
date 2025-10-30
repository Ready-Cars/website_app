<div>
    <div class="relative flex h-auto min-h-screen w-full flex-col overflow-x-hidden group/design-root">
        <div class="layout-container flex h-full grow flex-col">
            <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8">
                <div class="max-w-7xl mx-auto">
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-slate-900 mb-2">Find Your Perfect Ride</h1>
                        <p class="text-slate-500">Browse our selection of available cars for your next adventure.</p>
                    </div>
                    <div class="flex flex-col gap-4 mb-6">
                        <div class="relative flex-1">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"> search </span>
                            <input wire:model.debounce.400ms="q" class="form-input w-full rounded-md border-slate-300 pl-10 pr-28 py-2.5 text-slate-900 placeholder:text-slate-400 focus:border-sky-600 focus:ring-sky-600" placeholder="Search by name, category, or location" type="text"/>
                            <button wire:click="refreshSearch" class="absolute right-1.5 top-1/2 -translate-y-1/2 flex items-center justify-center rounded-md h-9 px-4 bg-[#1173d4] text-white text-sm font-semibold hover:bg-opacity-90">Search</button>
                        </div>
                            @include('partials.car-filter')
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @forelse($cars as $car)
                            <div class="flex flex-col rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300 bg-white">
                                <div class="w-full bg-center bg-no-repeat aspect-video bg-cover" style="background-image: url('{{ $car->image_url }}');"></div>
                                <div class="p-4 flex flex-col flex-1">
                                    <h3 class="text-slate-900 text-lg font-semibold leading-snug">{{ $car->name }}</h3>
                                    <p class="text-slate-500 text-sm mt-1 mb-4 flex-1">{{ $car->location ? $car->location.' • ' : '' }}{{ $car->category }} • {{ $car->transmission }} • {{ $car->seats }} seats</p>
                                    <div class="flex justify-between items-center">
                                        <p class="text-lg font-bold text-slate-900">From ₦{{ number_format($car->daily_price, 0) }}<span class="text-sm font-normal text-slate-500">/day</span></p>
                                        <flux:tooltip content="Instant booking" position="top">
                                            <flux:button
                                                href="{{ route('rent.show', $car) }}"
                                                variant="primary"
                                                size="sm"
                                                class="rounded-full"
                                                icon:trailing="arrow-right"
                                                aria-label="Rent {{ $car->name }} now"
                                                wire:navigate
                                            >
                                                Rent Now
                                            </flux:button>
                                        </flux:tooltip>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-slate-600">No cars found.</p>
                        @endforelse
                    </div>
                    <div class="flex items-center justify-center pt-8">
                        {{ $cars->onEachSide(1)->links() }}
                    </div>
                </div>
            </main>
            @include('partials.footer')
        </div>
    </div>
</div>
