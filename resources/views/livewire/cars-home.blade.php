<div class="bg-white min-h-screen">
    <main class="px-4 sm:px-6 lg:px-20 pb-20">
        <div class="mx-auto max-w-[1400px]">

    <!-- Hero Section -->
    <section class="relative mt-4 mb-20 px-2 sm:px-0" x-data="{
        initFlatpickr() {
            flatpickr($refs.datepicker, {
                mode: 'range',
                minDate: 'today',
                dateFormat: 'Y-m-d',
                nextArrow: '<span class=\'material-symbols-outlined\'>chevron_right</span>',
                prevArrow: '<span class=\'material-symbols-outlined\'>chevron_left</span>',
                onChange: (selectedDates) => {
                    if (selectedDates.length === 2) {
                        @this.set('startDate', flatpickr.formatDate(selectedDates[0], 'Y-m-d'));
                        @this.set('endDate', flatpickr.formatDate(selectedDates[1], 'Y-m-d'));
                    }
                },
                onOpen: [
                    function(selectedDates, dateStr, instance) {
                        instance.calendarContainer.classList.add('premium-calendar');
                    }
                ]
            });
        }
    }" x-init="initFlatpickr">
        <div class="relative h-[70vh] min-h-[550px] w-full rounded-[2.5rem] overflow-hidden shadow-2xl">
            <!-- Hero Image -->
            <div class="absolute inset-0 bg-center bg-no-repeat bg-cover transform hover:scale-105 transition-transform duration-1000"
                 style="background-image: url('https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?q=80&w=2000&auto=format&fit=crop');">
            </div>

            <!-- Overlay for Legibility -->
            <div class="absolute inset-0 bg-gradient-to-b from-black/20 via-black/40 to-black/70"></div>

            <!-- Hero Content -->
            <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-4 pb-32">
                <h1 class="text-5xl md:text-8xl font-black text-white mb-6 tracking-tighter drop-shadow-[0_4px_4px_rgba(0,0,0,0.5)]">
                    Ready. Set. Go.
                </h1>
                <p class="text-xl md:text-2xl text-white/95 font-bold max-w-2xl drop-shadow-[0_2px_2px_rgba(0,0,0,0.5)]">
                    Premium rentals for your next adventure
                </p>
            </div>

            <!-- Search Bar Overlay -->
            <div class="absolute bottom-10 left-1/2 -translate-x-1/2 w-[94%] md:w-full max-w-5xl">
                <div class="bg-white rounded-2xl shadow-2xl p-2 flex flex-col md:flex-row items-stretch md:items-center gap-1 md:gap-0 border border-slate-200/50 backdrop-blur-sm">
                    <!-- Where -->
                    <div class="flex-[1.5] px-8 md:px-10 py-5 flex flex-col justify-center transition-all hover:bg-slate-50 cursor-pointer rounded-t-2xl md:rounded-l-2xl md:rounded-tr-none md:border-r md:border-slate-100 group">
                        <label class="block text-[11px] md:text-[12px] font-black text-[#0e1133] uppercase tracking-widest mb-2 group-hover:text-[#1173d4] transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined text-base">location_on</span>
                            Where
                        </label>
                        <input wire:model.debounce.400ms="q"
                               type="text"
                               placeholder="Airport, city, or address"
                               class="w-full border-0 p-0 text-lg font-bold text-slate-900 placeholder:text-slate-400 focus:ring-0 bg-transparent" />
                    </div>

                    <!-- Date Picker Trigger -->
                    <div class="flex-[2] flex flex-col md:flex-row divide-y md:divide-y-0 md:divide-x divide-slate-100">
                        <div class="flex-1 px-8 md:px-10 py-5 transition-all hover:bg-slate-50 cursor-pointer flex flex-col justify-center relative group"
                             @click="$refs.datepicker.click()">
                            <label class="block text-[11px] md:text-[12px] font-black text-slate-400 uppercase tracking-widest mb-2 group-hover:text-slate-600 transition-colors flex items-center gap-2">
                                <span class="material-symbols-outlined text-base">calendar_today</span>
                                From
                            </label>
                            <span class="text-base md:text-lg font-bold text-slate-800" x-text="$wire.startDate || 'Add dates'"></span>
                            <input x-ref="datepicker" class="absolute inset-0 opacity-0 cursor-pointer" readonly />
                        </div>

                        <div class="flex-1 px-8 md:px-10 py-5 transition-all hover:bg-slate-50 cursor-pointer flex flex-col justify-center group"
                             @click="$refs.datepicker.click()">
                            <label class="block text-[11px] md:text-[12px] font-black text-slate-400 uppercase tracking-widest mb-2 group-hover:text-slate-600 transition-colors flex items-center gap-2">
                                <span class="material-symbols-outlined text-base">calendar_month</span>
                                Until
                            </label>
                            <span class="text-base md:text-lg font-bold text-slate-800" x-text="$wire.endDate || 'Add dates'"></span>
                        </div>
                    </div>

                    <!-- Search Button -->
                    <div class="p-2">
                        <button wire:click="refreshSearch"
                                @click="document.getElementById('catalog-section')?.scrollIntoView({ behavior: 'smooth', block: 'start' })"
                                class="w-full md:w-20 h-14 md:h-20 bg-[#0e1133] hover:bg-black rounded-xl md:rounded-2xl flex items-center justify-center text-white transition-all shadow-lg active:scale-95 group">
                            <span class="material-symbols-outlined text-3xl md:text-4xl font-bold group-hover:scale-110 transition-transform">search</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Flatpickr CSS & JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        .premium-calendar {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
            border: 1px solid #f1f5f9 !important;
            border-radius: 1.5rem !important;
            padding: 1rem !important;
            background: white !important;
            font-family: inherit !important;
        }
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange {
            background: #0e1133 !important;
            border-color: #0e1133 !important;
            border-radius: 0.5rem !important;
        }
        .flatpickr-day.inRange {
            background: rgba(14, 17, 51, 0.05) !important;
            box-shadow: none !important;
        }
    </style>


            <!-- Quick Filters (Categories) -->
            <section class="mb-14 px-2">
                <div class="flex items-center justify-start md:justify-center gap-2 md:gap-8 overflow-x-auto no-scrollbar pb-2">
                    @php
                        $catIcons = [
                            'SUV' => 'directions_car',
                            'Sedan' => 'minor_crash',
                            'Luxury' => 'diamond',
                            'Sport' => 'speed',
                            'Truck' => 'local_shipping',
                            'Van' => 'airport_shuttle',
                            'Electric' => 'electric_car',
                        ];
                    @endphp

                    <button class="flex-shrink-0 flex flex-col items-center gap-1 group"
                            wire:click="$set('category', '')">
                        <div class="w-14 h-10 flex items-center justify-center rounded-lg transition-all {{ !$category ? 'bg-[#0e1133] shadow-md' : 'bg-slate-50 hover:bg-slate-100' }}">
                            <span class="material-symbols-outlined text-[20px] {{ !$category ? 'text-white' : 'text-slate-500' }}">apps</span>
                        </div>
                        <span class="text-[9px] font-black uppercase tracking-widest leading-none {{ !$category ? 'text-[#0e1133] border-b-2 border-[#0e1133]' : 'text-slate-400 group-hover:text-slate-700' }}">All</span>
                    </button>

                    @foreach(($options['categories'] ?? []) as $cat)
                        <button class="flex-shrink-0 flex flex-col items-center gap-1 group"
                                wire:click="$set('category', '{{ $cat }}')">
                            <div class="w-14 h-10 flex items-center justify-center rounded-lg transition-all {{ $category == $cat ? 'bg-[#0e1133] shadow-md' : 'bg-slate-50 hover:bg-slate-100' }}">
                                <span class="material-symbols-outlined text-[20px] {{ $category == $cat ? 'text-white' : 'text-slate-500 group-hover:text-slate-800' }}">
                                    {{ $catIcons[$cat] ?? 'directions_car' }}
                                </span>
                            </div>
                            <span class="text-[9px] font-black uppercase tracking-widest leading-none {{ $category == $cat ? 'text-[#0e1133] border-b-2 border-[#0e1133]' : 'text-slate-400 group-hover:text-slate-700' }}">
                                {{ $cat }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </section>

            <!-- Featured Cars Section -->
            @if($showFeatured)
            <section class="mb-20">
                <div class="flex justify-between items-end mb-8">
                    <div>
                        <h2 class="text-3xl font-black text-[#0e1133] tracking-tight">Best daily deals</h2>
                        <p class="text-slate-500 font-medium mt-1">Exceptional value, vetted hosts</p>
                    </div>
                    <div class="flex gap-2">
                        <button class="w-10 h-10 rounded-full border border-slate-200 flex items-center justify-center hover:bg-slate-50 transition-colors shadow-sm">
                            <span class="material-symbols-outlined text-slate-600">chevron_left</span>
                        </button>
                        <button class="w-10 h-10 rounded-full border border-slate-200 flex items-center justify-center hover:bg-slate-50 transition-colors shadow-sm">
                            <span class="material-symbols-outlined text-slate-600">chevron_right</span>
                        </button>
                    </div>
                </div>

                <div class="relative">
                <div class="relative">
                    <!-- Loading State (Skeletons) -->
                    <div wire:loading wire:target="refreshSearch, category, q, startDate, endDate, resetFilters, gotoPage, nextPage, previousPage" style="display: none;">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            @for($i = 0; $i < 4; $i++)
                                <div class="animate-pulse">
                                    <div class="aspect-[4/3] bg-slate-100 rounded-2xl mb-4"></div>
                                    <div class="h-5 bg-slate-100 rounded w-3/4 mb-2"></div>
                                    <div class="h-4 bg-slate-50 rounded w-1/2 mb-3"></div>
                                    <div class="h-6 bg-slate-100 rounded w-1/4"></div>
                                </div>
                            @endfor
                        </div>
                    </div>

                    <div wire:loading.remove wire:target="refreshSearch, category, q, startDate, endDate, resetFilters, gotoPage, nextPage, previousPage">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            @forelse($featured as $car)
                                <a href="{{ route('rent.show', $car) }}" class="group block" wire:navigate>
                                    <div class="relative aspect-[4/3] rounded-2xl overflow-hidden mb-3">
                                        <img src="{{ $car->image_url }}" alt="{{ $car->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                                        <div class="absolute top-4 right-4 w-8 h-8 rounded-full bg-white/90 backdrop-blur-sm flex items-center justify-center text-slate-900 shadow-sm opacity-0 group-hover:opacity-100 transition-opacity">
                                            <span class="material-symbols-outlined text-lg">favorite</span>
                                        </div>
                                    </div>
                                    <h3 class="text-lg font-black text-slate-900 leading-tight">{{ $car->name }}</h3>
                                    <div class="flex items-center gap-1.5 mt-1 text-sm text-slate-500">
                                        <span class="font-bold text-slate-700">{{ $car->year ?? '2023' }}</span>
                                        <span>•</span>
                                        <div class="flex items-center">
                                            <span class="material-symbols-outlined text-xs text-amber-500 font-bold">star</span>
                                            <span class="font-bold text-slate-700 ml-0.5">5.0</span>
                                            <span class="ml-0.5 text-slate-400 font-normal">(12)</span>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <span class="text-lg font-black text-[#0e1133]">₦{{ number_format($car->daily_price, 0) }}</span>
                                        <span class="text-sm font-medium text-slate-500">/day</span>
                                    </div>
                                </a>
                            @empty
                                <p class="text-slate-400">No deals available right now.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </section>
            @endif

            <!-- Main Catalog -->
            <section id="catalog-section">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-3xl font-black text-[#0e1133] tracking-tight">Available Cars</h2>
                    <div class="flex items-center gap-2">
                        @include('partials.car-filter')
                    </div>
                </div>

                <div class="relative">
                    <!-- Loading State (Skeletons) -->
                    <div wire:loading wire:target="refreshSearch, category, q, startDate, endDate, resetFilters, gotoPage, nextPage, previousPage" style="display: none;">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-6 gap-y-12">
                            @for($i = 0; $i < 8; $i++)
                                <div class="animate-pulse">
                                    <div class="aspect-video bg-slate-100 rounded-3xl mb-4"></div>
                                    <div class="px-1 space-y-3">
                                        <div class="flex justify-between items-center">
                                            <div class="h-6 bg-slate-100 rounded w-1/2"></div>
                                            <div class="h-6 bg-slate-100 rounded w-1/4"></div>
                                        </div>
                                        <div class="flex gap-3">
                                            <div class="h-4 bg-slate-50 rounded w-12"></div>
                                            <div class="h-4 bg-slate-50 rounded w-12"></div>
                                            <div class="h-4 bg-slate-50 rounded w-12"></div>
                                        </div>
                                        <div class="h-4 bg-slate-50 rounded w-1/3"></div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>

                    <div wire:loading.remove wire:target="refreshSearch, category, q, startDate, endDate, resetFilters, gotoPage, nextPage, previousPage">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-6 gap-y-12">
                            @forelse($catalog as $car)
                            <a href="{{ route('rent.show', $car) }}" class="group flex flex-col" wire:navigate>
                                <div class="relative aspect-video rounded-3xl overflow-hidden mb-4 bg-slate-100">
                                    <img src="{{ $car->image_url }}" alt="{{ $car->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
                                    <div class="absolute inset-0 bg-black/5 group-hover:bg-transparent transition-colors"></div>
                                    <div class="absolute bottom-4 left-4">
                                        <span class="px-3 py-1 rounded-full bg-white/90 backdrop-blur-sm text-[10px] font-black uppercase tracking-widest text-slate-900 shadow-sm">
                                            {{ $car->category }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-1 px-1">
                                    <div class="flex justify-between items-start mb-1">
                                        <h3 class="text-lg font-black text-slate-900">{{ $car->name }}</h3>
                                        <span class="text-lg font-black text-[#0e1133]">₦{{ number_format($car->daily_price, 0) }}</span>
                                    </div>
                                    <div class="flex items-center gap-3 text-sm font-medium text-slate-50">
                                        <div class="flex items-center gap-1 text-slate-500">
                                            <span class="material-symbols-outlined text-base">airline_seat_recline_normal</span>
                                            <span>{{ $car->seats }}</span>
                                        </div>
                                        <div class="flex items-center gap-1 text-slate-500">
                                            <span class="material-symbols-outlined text-base">settings</span>
                                            <span>{{ $car->transmission }}</span>
                                        </div>
                                        <div class="flex items-center gap-1 text-slate-500">
                                            <span class="material-symbols-outlined text-base">local_gas_station</span>
                                            <span>{{ $car->fuel_type ?? 'Gas' }}</span>
                                        </div>
                                    </div>
                                    <p class="text-xs font-bold text-[#0e1133] mt-2 group-hover:underline underline-offset-4 decoration-2">Rent this car →</p>
                                </div>
                            </a>
                            @empty
                                <div class="col-span-full py-20 text-center bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200">
                                    <span class="material-symbols-outlined text-6xl text-slate-300 mb-4">search_off</span>
                                    <p class="text-slate-500 font-bold text-xl">No cars found matching your search</p>
                                    <button wire:click="resetFilters" class="mt-4 text-[#0e1133] font-bold hover:underline">Clear all filters</button>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Infinite Scroll Sentinel -->
                @if($catalog->hasMorePages())
                    <div x-data="{
                            init() {
                                let observer = new IntersectionObserver((entries) => {
                                    entries.forEach(entry => {
                                        if (entry.isIntersecting) {
                                            @this.call('loadMore')
                                        }
                                    })
                                }, {
                                    rootMargin: '200px'
                                })
                                observer.observe($el)
                            }
                        }" class="py-20 flex flex-col items-center justify-center">
                        <div wire:loading wire:target="loadMore" class="flex flex-col items-center">
                            <div class="w-10 h-10 border-4 border-slate-200 border-t-[#0e1133] rounded-full animate-spin"></div>
                            <p class="mt-4 text-sm font-bold text-slate-500 uppercase tracking-widest">Discovering more cars...</p>
                        </div>
                        <div wire:loading.remove wire:target="loadMore" class="h-10"></div>
                    </div>
                @else
                    <div class="py-20 text-center">
                        <p class="text-slate-400 font-bold uppercase tracking-widest text-xs">You've reached the end of the road. No more cars to show.</p>
                    </div>
                @endif
            </section>
        </div>
    </main>

    @include('partials.footer')
</div>

