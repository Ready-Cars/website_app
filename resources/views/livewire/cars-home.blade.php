<div>
    <div class="relative flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-hidden">
        <div class="layout-container flex h-full grow flex-col">

            <main class="flex-1 px-4 sm:px-6 lg:px-24 pt-0 pb-12">
                <div class="mx-auto max-w-5xl">


                    <!-- Full-Width Hero Carousel -->
                    <section class="relative -mx-4 sm:-mx-6 lg:-mx-24 mb-6" x-data="{
                            current: 0,
                            slides: [
                              { image: 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?q=80&w=2000&auto=format&fit=crop', title: 'Luxury, On Your Terms', tagline: 'Premium cars. Clear pricing. Total flexibility.' },

                                { image: 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?q=80&w=2000&auto=format&fit=crop', title: 'Find Your Perfect Ride', tagline: 'Rent a car for your next adventure with ease.' },
                                { image: 'https://images.unsplash.com/photo-1580414057403-c5f451f30e1c?q=80&w=2000&auto=format&fit=crop', title: 'Drive Your Story', tagline: 'From city zips to cross‑country trips.' },
                                        ],
                            next() { this.current = (this.current + 1) % this.slides.length; },
                            prev() { this.current = (this.current - 1 + this.slides.length) % this.slides.length; }
                        }" x-init="setInterval(() => next(), 6000)">
                        <div class="relative h-[58vh] min-h-[360px] w-screen left-1/2 right-1/2 -translate-x-1/2 overflow-hidden">
                            <!-- Slides -->
                            <template x-for="(slide, i) in slides" :key="i">
                                <div class="absolute inset-0 transition-opacity duration-700 ease-in-out"
                                     x-show="current === i"
                                     x-transition:enter="opacity-0" x-transition:enter-end="opacity-100"
                                     x-transition:leave="opacity-100" x-transition:leave-end="opacity-0">
                                    <div class="h-full w-full bg-center bg-no-repeat bg-cover" :style="`background-image: url('${slide.image}')`"></div>
                                </div>
                            </template>

                            <!-- Gradient overlay -->
                            <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-black/20 to-black/40 pointer-events-none"></div>

                            <!-- Content overlay -->
                            <div class="absolute inset-0 flex items-center">
                                <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-24 w-full">
                                    <div class="text-center select-none">
                                        <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-3 tracking-tight" x-text="slides[current].title"></h1>
                                        <p class="text-lg text-white/90" x-text="slides[current].tagline"></p>
                                    </div>
                                    <div class="mt-6 max-w-3xl mx-auto">
                                        <div class="relative">
                                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-white/70">search</span>
                                            <input wire:model.debounce.400ms="q" class="form-input w-full rounded-full border-white/30 bg-white/90 backdrop-blur py-4 pl-12 pr-28 text-base text-slate-900 shadow-sm focus:border-sky-600 focus:ring-sky-600 placeholder:text-slate-500" placeholder="Search by name, category or location" type="text" />
                                            <button wire:click="refreshSearch" class="absolute right-2 top-1/2 -translate-y-1/2 flex min-w-[84px] max-w-[480px] items-center justify-center overflow-hidden rounded-full h-10 px-6 bg-[#1173d4] text-white text-sm font-bold tracking-wide hover:bg-[#0f63b9] transition-colors" type="button">
                                                <span class="truncate">Search</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Controls -->
                            <button type="button" @click="prev()" aria-label="Previous slide" class="absolute left-4 top-1/2 -translate-y-1/2 inline-flex items-center justify-center h-10 w-10 rounded-full bg-black/40 text-white hover:bg-black/60">
                                <span class="material-symbols-outlined">chevron_left</span>
                            </button>
                            <button type="button" @click="next()" aria-label="Next slide" class="absolute right-4 top-1/2 -translate-y-1/2 inline-flex items-center justify-center h-10 w-10 rounded-full bg-black/40 text-white hover:bg-black/60">
                                <span class="material-symbols-outlined">chevron_right</span>
                            </button>

                            <!-- Indicators -->
                            <div class="absolute bottom-5 inset-x-0 flex items-center justify-center gap-2">
                                <template x-for="(slide, i) in slides" :key="i">
                                    <button type="button" class="h-2.5 rounded-full transition-all" :class="current === i ? 'bg-white w-6' : 'bg-white/60 w-2.5'" @click="current = i" :aria-label="'Go to slide ' + (i + 1)"></button>
                                </template>
                            </div>
                        </div>
                    </section>

                    <!-- Advanced filters toggle just below hero -->
                    <div class="mb-4">
                        <button type="button" wire:click="toggleAdvanced" aria-expanded="{{ $showAdvanced ? 'true' : 'false' }}" class="inline-flex items-center gap-2 text-sm font-medium text-[#1173d4] hover:text-[#0f63b9]">
                            <span class="material-symbols-outlined text-base">{{ $showAdvanced ? 'expand_less' : 'tune' }}</span>
                            <span>{{ $showAdvanced ? 'Hide advanced filters' : 'Show advanced filters' }}</span>
                        </button>
                    </div>

                    @if($showAdvanced)
                    <div class="mb-10 grid grid-cols-1 md:grid-cols-5 gap-3">
                        <select wire:model.live="category" class="form-select rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600">
                            <option value="">All Categories</option>
                            @foreach(($options['categories'] ?? []) as $c)
                                <option value="{{ $c }}">{{ $c }}</option>
                            @endforeach
                        </select>
                        <select wire:model.live="transmission" class="form-select rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600">
                            <option value="">Any Transmission</option>
                            @foreach(($options['transmissions'] ?? []) as $t)
                                <option value="{{ $t }}">{{ $t }}</option>
                            @endforeach
                        </select>
                        <select wire:model.live="fuelType" class="form-select rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600">
                            <option value="">Any Fuel</option>
                            @foreach(($options['fuels'] ?? []) as $f)
                                <option value="{{ $f }}">{{ $f }}</option>
                            @endforeach
                        </select>
                        <select wire:model.live="seats" class="form-select rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600">
                            <option value="">Any Seats</option>
                            @foreach(($options['seats'] ?? []) as $s)
                                <option value="{{ $s }}">{{ $s }} seats</option>
                            @endforeach
                        </select>
{{--                        ww--}}
                        <select wire:model.live="sort" class="form-select rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600">
                            @foreach(($options['sorts'] ?? []) as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <div class="md:col-span-5 grid grid-cols-2 md:grid-cols-4 gap-3 mt-1">
                            <select wire:model.live="location" class="form-select rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600">
                                                        <option value="">All Locations</option>
                                                        @foreach(($options['locations'] ?? []) as $loc)
                                                            <option value="{{ $loc }}">{{ $loc }}</option>
                                                        @endforeach
                                                    </select>
                            <input wire:model.debounce.400ms="minPrice" type="number" min="0" placeholder="Min ₦/day" class="form-input rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" />
                            <input wire:model.debounce.400ms="maxPrice" type="number" min="0" placeholder="Max ₦/day" class="form-input rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" />
                            <button wire:click="resetFilters" type="button" class="rounded-md border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Reset</button>
                        </div>
                    </div>
                    @endif

                    @if($showFeatured)
                    <section class="mb-16">
                        <h2 class="text-3xl font-bold text-slate-900 mb-8 tracking-tight">Featured Cars</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                            @forelse($featured as $car)
                                <div class="flex flex-col gap-4 rounded-lg overflow-hidden bg-white shadow-md hover:shadow-xl transition-shadow duration-300">
                                    <div class="w-full bg-center bg-no-repeat aspect-video bg-cover" style="background-image: url('{{ $car->image_url }}');"></div>
                                    <div class="p-5">
                                        <h3 class="text-lg font-bold text-slate-900">{{ $car->name }}</h3>
                                        <p class="text-slate-600 text-sm">{{ $car->location ? $car->location.' • ' : '' }}{{ $car->category }} • {{ $car->transmission }} • {{ $car->seats }} seats</p>
                                        <div class="mt-3 flex justify-between items-center">
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
                                <p class="text-slate-600">No featured cars yet.</p>
                            @endforelse
                        </div>
                    </section>
                    @endif

                    <section>
                        <div class="flex justify-between items-center mb-8">
                            <h2 class="text-3xl font-bold text-slate-900 tracking-tight">Explore Our Catalog</h2>
                            <a href="{{ route('cars.index') }}" class="flex min-w-[84px] max-w-[480px] items-center justify-center overflow-hidden rounded-md h-10 px-4 bg-slate-200 text-slate-900 text-sm font-bold tracking-wide hover:bg-slate-300 transition-colors" wire:navigate>
                                <span class="truncate">View all cars</span>
                            </a>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @forelse($catalog as $car)
                                <div class="flex flex-col gap-3 group rounded-lg overflow-hidden bg-white shadow-sm hover:shadow-lg transition-shadow duration-300">
                                    <div class="w-full bg-center bg-no-repeat aspect-video bg-cover" style="background-image: url('{{ $car->image_url }}');"></div>
                                    <div class="p-4 pt-3">
                                        <h3 class="text-base font-bold text-slate-900">{{ $car->name }}</h3>
                                        <p class="text-slate-600 text-sm">{{ $car->location ? $car->location.' • ' : '' }}{{ $car->category }} • {{ $car->transmission }} • {{ $car->seats }} seats</p>
                                        <div class="mt-3 flex justify-between items-center">
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
                                <p class="text-slate-600">No cars match your search.</p>
                            @endforelse
                        </div>

                        <div class="mt-6">
                            {{ $catalog->onEachSide(1)->links() }}
                        </div>
                    </section>
                </div>
            </main>

         @include('partials.footer')
        </div>
    </div>
</div>
