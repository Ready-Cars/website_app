<div class="bg-white min-h-screen">
    <main class="px-4 sm:px-6 lg:px-20 pb-20">
        <div class="mx-auto max-w-[1400px]">

            <!-- Hero Section -->
            <section class="relative -mx-4 mt-0 mb-16 sm:mx-0 sm:mt-4 sm:mb-20" x-data="{
        initFlatpickr() {
            const datepickerConfig = {
                mode: 'range',
                minDate: 'today',
                dateFormat: 'Y-m-d',
                appendTo: document.body,
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
            };

            if ($refs.datepickerDesktop) {
                flatpickr($refs.datepickerDesktop, datepickerConfig);
            }

            if ($refs.datepickerMobile) {
                flatpickr($refs.datepickerMobile, datepickerConfig);
            }
        }
    }" x-init="initFlatpickr">
                <div data-dropdown-boundary
                    class="relative h-[44vh] min-h-[320px] md:h-[44vh] md:min-h-[340px] w-full rounded-none sm:rounded-2xl overflow-hidden shadow-xl">
                    <!-- Hero Image -->
                    <div class="absolute inset-0 bg-center bg-no-repeat bg-cover"
                        style="background-image: url('https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?q=80&w=2000&auto=format&fit=crop'); background-position: center 42%;">
                    </div>

                    <!-- Overlay for Legibility -->
                    <div class="absolute inset-0 bg-gradient-to-br from-[#0e1133]/24 via-[#1d4ed8]/16 to-[#dc2626]/44">
                    </div>

                    <!-- Hero Content (Desktop) -->
                    <div
                        class="absolute inset-0 hidden md:flex flex-col items-center justify-center text-center px-4 pb-24 md:pb-28">
                        <h1
                            class="text-4xl sm:text-5xl md:text-6xl font-semibold text-white mb-3 tracking-tight drop-shadow-[0_4px_4px_rgba(0,0,0,0.45)]">
                            Ready. Set. Go.
                        </h1>
                        <p
                            class="text-sm md:text-lg text-white/95 font-medium max-w-2xl drop-shadow-[0_2px_2px_rgba(0,0,0,0.45)]">
                            Premium rentals for your next adventure
                        </p>
                    </div>

                    <!-- Hero Content (Mobile) -->
                    <div
                        class="absolute inset-0 md:hidden flex flex-col items-center justify-center text-center px-5 pb-24">
                        <h1
                            class="text-5xl font-semibold text-white leading-[0.96] tracking-tight drop-shadow-[0_4px_6px_rgba(0,0,0,0.45)]">
                            Ready. Set. Go.
                        </h1>
                        <p
                            class="mt-3 text-base text-white/95 leading-snug max-w-[19rem] drop-shadow-[0_2px_4px_rgba(0,0,0,0.45)]">
                            Premium rentals for your next adventure
                        </p>
                    </div>

                    <!-- Search Bar Overlay (Desktop) -->
                    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 w-[94%] max-w-[980px] hidden md:block">
                        <div
                            class="bg-white/95 rounded-2xl shadow-xl p-2 md:p-2.5 flex flex-col md:flex-row items-stretch md:items-center md:gap-0 border border-slate-200/80 backdrop-blur-sm">
                            <!-- Where (Location Dropdown) -->
                            <div x-data="{
                                    locationOpen: false,
                                    locationDropdownPosition: 'up',
                                    setLocationDropdownPosition() {
                                        const rect = this.$el.getBoundingClientRect();
                                        const estimatedMenuHeight = 280;
                                        const boundaryEl = this.$el.closest('[data-dropdown-boundary]');
                                        const boundaryRect = boundaryEl ? boundaryEl.getBoundingClientRect() : null;
                                        const boundaryTop = boundaryRect ? boundaryRect.top : 0;
                                        const boundaryBottom = boundaryRect ? boundaryRect.bottom : window.innerHeight;
                                        const spaceBelow = boundaryBottom - rect.bottom;
                                        const spaceAbove = rect.top - boundaryTop;

                                        this.locationDropdownPosition = (spaceBelow < estimatedMenuHeight && spaceAbove > spaceBelow) ? 'up' : 'down';
                                    },
                                    toggleLocationDropdown() {
                                        if (!this.locationOpen) {
                                            this.setLocationDropdownPosition();
                                        }
                                        this.locationOpen = !this.locationOpen;
                                    }
                                }" @click.outside="locationOpen = false"
                                class="relative flex-[1.5] px-5 md:px-7 py-3.5 md:py-4 flex flex-col justify-center transition-all duration-200 hover:bg-slate-50 cursor-pointer rounded-t-2xl md:rounded-l-2xl md:rounded-tr-none md:border-r md:border-slate-100 group">
                                <label
                                    class="block text-[10px] md:text-[11px] font-semibold text-slate-500/90 uppercase tracking-[0.14em] mb-1.5 group-hover:text-[#1173d4] transition-colors flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[15px]">location_on</span>
                                    Where
                                </label>
                                <button type="button" @click="toggleLocationDropdown()"
                                    class="w-full flex items-center justify-between text-left text-[1.02rem] md:text-[1.06rem] font-semibold text-slate-900 leading-tight">
                                    <span x-text="$wire.location || 'Anywhere'"></span>
                                    <span
                                        class="material-symbols-outlined text-base text-slate-400 transition-transform"
                                        :class="{ 'rotate-180': locationOpen }">expand_more</span>
                                </button>

                                <div x-show="locationOpen" x-transition
                                    class="absolute left-0 right-0 z-50 rounded-2xl border border-slate-200 bg-white p-2 shadow-lg transition-all duration-150"
                                    :class="locationDropdownPosition === 'up' ? 'bottom-[calc(100%+8px)] origin-bottom' : 'top-[calc(100%+8px)] origin-top'">
                                    <div class="max-h-56 overflow-auto no-scrollbar">
                                        <button type="button" @click="$wire.set('location', ''); locationOpen = false"
                                            class="w-full rounded-xl px-3 py-2.5 text-left text-sm font-medium transition-colors"
                                            :class="$wire.location ? 'text-slate-700 hover:bg-slate-50' : 'bg-slate-100 text-slate-900'">
                                            Anywhere
                                        </button>
                                        @foreach(($options['locations'] ?? []) as $loc)
                                            <button type="button"
                                                @click="$wire.set('location', @js($loc)); locationOpen = false"
                                                class="mt-1 w-full rounded-xl px-3 py-2.5 text-left text-sm font-medium transition-colors"
                                                :class="$wire.location === @js($loc) ? 'bg-slate-100 text-slate-900' : 'text-slate-700 hover:bg-slate-50'">
                                                {{ $loc }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Date Picker Trigger -->
                            <div
                                class="flex-[2] flex flex-col md:flex-row divide-y md:divide-y-0 md:divide-x divide-slate-100">
                                <div class="flex-1 px-5 md:px-7 py-3.5 md:py-4 transition-all duration-200 hover:bg-slate-50 cursor-pointer flex flex-col justify-center relative group"
                                    @click="$refs.datepickerDesktop?._flatpickr?.open()">
                                    <label
                                        class="block text-[10px] md:text-[11px] font-semibold text-slate-400 uppercase tracking-[0.14em] mb-1.5 group-hover:text-slate-600 transition-colors flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[15px]">calendar_today</span>
                                        From
                                    </label>
                                    <span
                                        class="text-[1.02rem] md:text-[1.06rem] font-semibold text-slate-800 leading-tight"
                                        x-text="$wire.startDate || 'Add dates'"></span>
                                    <input x-ref="datepickerDesktop"
                                        class="absolute inset-0 opacity-0 pointer-events-none" readonly />
                                </div>

                                <div class="flex-1 px-5 md:px-7 py-3.5 md:py-4 transition-all duration-200 hover:bg-slate-50 cursor-pointer flex flex-col justify-center group"
                                    @click="$refs.datepickerDesktop?._flatpickr?.open()">
                                    <label
                                        class="block text-[10px] md:text-[11px] font-semibold text-slate-400 uppercase tracking-[0.14em] mb-1.5 group-hover:text-slate-600 transition-colors flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[15px]">calendar_month</span>
                                        Until
                                    </label>
                                    <span
                                        class="text-[1.02rem] md:text-[1.06rem] font-semibold text-slate-800 leading-tight"
                                        x-text="$wire.endDate || 'Add dates'"></span>
                                </div>
                            </div>

                            <!-- Search Button -->
                            <div class="p-2">
                                <button wire:click="refreshSearch"
                                    @click="document.getElementById('catalog-section')?.scrollIntoView({ behavior: 'smooth', block: 'start' })"
                                    class="w-full md:w-[62px] h-12 md:h-[62px] bg-[#0e1133] hover:bg-black rounded-2xl flex items-center justify-center text-white transition-all duration-200 hover:scale-[1.02] active:scale-95 group">
                                    <span
                                        class="material-symbols-outlined text-xl md:text-2xl group-hover:scale-105 transition-transform">search</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Search Bar Overlay (Mobile) -->
                    <div class="absolute inset-x-4 bottom-3 md:hidden" x-data="{
                        mobileSearchOpen: false,
                        openMobileSearch() {
                            this.mobileSearchOpen = true;
                            document.body.classList.add('overflow-hidden');
                        },
                        closeMobileSearch() {
                            this.mobileSearchOpen = false;
                            document.body.classList.remove('overflow-hidden');
                        }
                    }">
                        <div x-show="!mobileSearchOpen" x-transition.opacity
                            class="rounded-3xl border border-white/70 bg-white/95 p-2 shadow-xl backdrop-blur-sm">
                            <div class="flex items-center gap-2">
                                <button type="button" @click="openMobileSearch()"
                                    class="flex-1 min-w-0 rounded-2xl px-4 py-3 text-left">
                                    <span class="block truncate text-base font-semibold text-slate-800">
                                        {{ $location ?: 'Anywhere' }}
                                    </span>
                                    <span class="mt-0.5 block truncate text-xs font-medium text-slate-500">
                                        @if($startDate && $endDate)
                                            {{ $startDate }} to {{ $endDate }}
                                        @elseif($startDate)
                                            Starts {{ $startDate }}
                                        @else
                                            Add your trip dates
                                        @endif
                                    </span>
                                </button>
                                <button type="button" @click="openMobileSearch()"
                                    class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-[#4f46e5] text-white">
                                    <span class="material-symbols-outlined text-xl">search</span>
                                </button>
                            </div>
                        </div>

                        <div x-show="mobileSearchOpen" x-transition.opacity class="fixed inset-0 z-[85] bg-black/45"
                            @click="closeMobileSearch()"></div>
                        <div x-show="mobileSearchOpen" x-transition @keydown.escape.window="closeMobileSearch()"
                            class="fixed inset-x-0 bottom-0 z-[90] rounded-t-[1.8rem] bg-white px-5 pb-5 pt-4 shadow-2xl md:hidden max-h-[84vh] overflow-auto">
                            <div class="mx-auto mb-4 h-1.5 w-12 rounded-full bg-slate-200"></div>
                            <button type="button" class="absolute right-5 top-4 text-slate-500"
                                @click="closeMobileSearch()">
                                <span class="material-symbols-outlined text-2xl">close</span>
                            </button>

                            <div class="pt-1">
                                <div class="mb-4 flex items-center justify-between pr-10">
                                    <div>
                                        <h3 class="text-lg font-semibold text-slate-900">Find your car</h3>
                                        @if($activeFilterCount > 0)
                                            <p class="text-xs font-medium text-slate-500">{{ $activeFilterCount }} active
                                                filter{{ $activeFilterCount > 1 ? 's' : '' }}</p>
                                        @endif
                                    </div>
                                    @if($isSearching)
                                        <button type="button" wire:click="resetFilters"
                                            class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                            Clear all
                                        </button>
                                    @endif
                                </div>

                                <div class="space-y-3">
                                    <div class="w-full rounded-2xl border border-slate-200 p-3 text-left">
                                        <span
                                            class="block text-[11px] font-medium uppercase tracking-[0.08em] text-slate-500">Where</span>
                                        <div class="mt-1.5">
                                            <select wire:model.live="location"
                                                class="w-full border-0 bg-transparent p-0 text-base font-semibold text-slate-800 focus:outline-none focus:ring-0 appearance-none">
                                                <option value="">Anywhere</option>
                                                @foreach(($options['locations'] ?? []) as $loc)
                                                    <option value="{{ $loc }}">{{ $loc }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="w-full rounded-2xl border border-slate-200 p-3 text-left">
                                        <span
                                            class="block text-[11px] font-medium uppercase tracking-[0.08em] text-slate-500">From</span>
                                        <div class="mt-1.5">
                                            <input type="date" wire:model.live="startDate"
                                                class="w-full border-0 bg-transparent p-0 text-base font-semibold text-slate-800 focus:outline-none focus:ring-0" />
                                        </div>
                                    </div>

                                    <div class="w-full rounded-2xl border border-slate-200 p-3 text-left">
                                        <span
                                            class="block text-[11px] font-medium uppercase tracking-[0.08em] text-slate-500">Until</span>
                                        <div class="mt-1.5">
                                            <input type="date" wire:model.live="endDate"
                                                class="w-full border-0 bg-transparent p-0 text-base font-semibold text-slate-800 focus:outline-none focus:ring-0" />
                                        </div>
                                    </div>

                                    <div
                                        class="sticky bottom-0 -mx-5 mt-5 border-t border-slate-200 bg-white/95 px-5 pt-3 pb-1 backdrop-blur">
                                        <div class="flex items-center gap-2">
                                            @if($isSearching)
                                                <button type="button" wire:click="resetFilters" @click="closeMobileSearch()"
                                                    class="inline-flex h-11 flex-1 items-center justify-center rounded-xl border border-slate-300 bg-white text-sm font-semibold text-slate-700">
                                                    Clear
                                                </button>
                                            @endif
                                            <button type="button" wire:click="refreshSearch"
                                                @click="closeMobileSearch(); document.getElementById('catalog-section')?.scrollIntoView({ behavior: 'smooth', block: 'start' })"
                                                class="inline-flex h-11 items-center justify-center rounded-xl bg-[#4f46e5] px-4 text-sm font-semibold text-white {{ $isSearching ? 'flex-1' : 'w-full' }}">
                                                Show cars
                                            </button>
                                        </div>
                                    </div>
                                </div>
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

                .flatpickr-day.selected,
                .flatpickr-day.startRange,
                .flatpickr-day.endRange {
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
            <section class="mb-10 md:mb-12 px-2">
                <div
                    class="flex items-center justify-start md:justify-center gap-3 md:gap-5 overflow-x-auto no-scrollbar pb-2 snap-x snap-mandatory">
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

                    <button class="flex-shrink-0 snap-start flex flex-col items-center gap-1 group"
                        wire:click="$set('category', '')">
                        <div
                            class="w-11 h-11 md:w-12 md:h-12 flex items-center justify-center rounded-2xl border transition-all {{ !$category ? 'bg-[#0e1133] border-[#0e1133]' : 'bg-white border-slate-200 group-hover:border-slate-300 group-hover:bg-slate-50' }}">
                            <span
                                class="material-symbols-outlined text-[17px] md:text-[19px] {{ !$category ? 'text-white' : 'text-slate-500 group-hover:text-slate-700' }}">apps</span>
                        </div>
                        <span
                            class="text-[0.58rem] md:text-[0.64rem] font-semibold uppercase tracking-[0.12em] leading-none {{ !$category ? 'text-[#0e1133]' : 'text-slate-400 group-hover:text-slate-700' }}">All</span>
                    </button>

                    @foreach(($options['categories'] ?? []) as $cat)

                        <button class="flex-shrink-0 snap-start flex flex-col items-center gap-1 group"
                            wire:click="$set('category', '{{ $cat }}')">
                            <div
                                class="w-11 h-11 md:w-12 md:h-12 flex items-center justify-center rounded-2xl border transition-all {{ $category == $cat ? 'bg-[#0e1133] border-[#0e1133]' : 'bg-white border-slate-200 group-hover:border-slate-300 group-hover:bg-slate-50' }}">
                                <span
                                    class="material-symbols-outlined text-[17px] md:text-[19px] {{ $category == $cat ? 'text-white' : 'text-slate-500 group-hover:text-slate-700' }}">
                                    {{ $catIcons[$cat] ?? 'directions_car' }}
                                </span>
                            </div>
                            <span
                                class="text-[0.58rem] md:text-[0.64rem] font-semibold uppercase tracking-[0.12em] leading-none {{ $category == $cat ? 'text-[#0e1133]' : 'text-slate-400 group-hover:text-slate-700' }}">
                                {{ $cat }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </section>

            <!-- Featured Cars Section -->
            @if($showFeatured)
                <section class="mb-14 md:mb-16">
                    <div class="flex justify-between items-center mb-5 md:mb-6">
                        <div>
                            <h2 class="text-base md:text-[1.4rem] font-semibold text-[#0e1133] tracking-tight">Best daily
                                deals</h2>
                            <p class="text-xs md:text-sm font-medium text-slate-500">Exceptional value, vetted hosts</p>
                        </div>
                        <div class="flex gap-2">
                            <button
                                class="w-9 h-9 md:w-10 md:h-10 rounded-full border border-slate-200 flex items-center justify-center hover:bg-slate-50 transition-colors shadow-sm">
                                <span
                                    class="material-symbols-outlined text-[18px] md:text-[20px] text-slate-600">chevron_left</span>
                            </button>
                            <button
                                class="w-9 h-9 md:w-10 md:h-10 rounded-full border border-slate-200 flex items-center justify-center hover:bg-slate-50 transition-colors shadow-sm">
                                <span
                                    class="material-symbols-outlined text-[18px] md:text-[20px] text-slate-600">chevron_right</span>
                            </button>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="relative">
                            <!-- Loading State (Skeletons) -->
                            <div wire:loading wire:target="refreshSearch, category, q, startDate, endDate, resetFilters"
                                class="w-full" style="display: none;">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 w-full">
                                    @for($i = 0; $i < 4; $i++)
                                        <div class="animate-pulse w-full">
                                            <div class="aspect-[4/3] bg-slate-100 rounded-2xl mb-4 w-full"></div>
                                            <div class="h-5 bg-slate-100 rounded w-3/4 mb-2"></div>
                                            <div class="h-4 bg-slate-50 rounded w-1/2 mb-3"></div>
                                            <div class="h-6 bg-slate-100 rounded w-1/4"></div>
                                        </div>
                                    @endfor
                                </div>
                            </div>

                            <div wire:loading.remove
                                wire:target="refreshSearch, category, q, startDate, endDate, resetFilters" class="w-full">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                    @forelse($featured as $car)
                                        <a href="{{ route('rent.show', $car) }}" class="group block" wire:navigate>
                                            <div class="relative aspect-[4/3] rounded-2xl overflow-hidden mb-3">
                                                <img src="{{ $car->image_url }}" alt="{{ $car->name }}"
                                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                                                <div
                                                    class="absolute top-4 right-4 w-8 h-8 rounded-full bg-white/90 backdrop-blur-sm flex items-center justify-center text-slate-900 shadow-sm opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <span class="material-symbols-outlined text-lg">favorite</span>
                                                </div>
                                            </div>
                                            <h3 class="text-[1.1rem] font-semibold text-slate-900 leading-snug tracking-tight">
                                                {{ $car->name }}
                                            </h3>
                                            <div class="flex items-center gap-1.5 mt-1 text-[0.92rem] text-slate-500">
                                                <span class="font-semibold text-slate-700">{{ $car->year ?? '2023' }}</span>
                                                <span>•</span>
                                                <div class="flex items-center">
                                                    <span class="material-symbols-outlined text-xs text-amber-500">star</span>
                                                    <span class="font-semibold text-slate-700 ml-0.5">5.0</span>
                                                    <span class="ml-0.5 text-slate-400 font-normal">(12)</span>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <span
                                                    class="text-xl font-semibold text-[#0e1133] tracking-tight">₦{{ number_format($car->daily_price, 0) }}</span>
                                                <span class="text-sm font-medium text-slate-500">/day</span>
                                            </div>
                                        </a>
                                    @empty
                                        <p class="text-slate-400">No deals available right now.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            <!-- Main Catalog -->
            <section id="catalog-section" class="pt-1 md:pt-2">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-base md:text-[1.45rem] font-semibold text-[#0e1133] tracking-tight">Available
                            Cars</h2>
                        @if($isSearching && $activeFilterCount > 0)
                            <p class="mt-1 text-xs font-medium text-slate-500">{{ $activeFilterCount }} active
                                filter{{ $activeFilterCount > 1 ? 's' : '' }}</p>
                        @endif
                    </div>
                    @if($isSearching)
                        <button type="button" wire:click="resetFilters"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-50">
                            <span class="material-symbols-outlined text-base">close</span>
                            Clear search
                        </button>
                    @endif
                </div>

                <div class="relative">
                    <!-- Loading State (Skeletons) -->
                    <div wire:loading wire:target="refreshSearch, category, q, startDate, endDate, resetFilters"
                        class="w-full" style="display: none;">
                        <div
                            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-6 gap-y-12 w-full">
                            @for($i = 0; $i < 8; $i++)
                                <div class="animate-pulse w-full">
                                    <div class="aspect-video bg-slate-100 rounded-2xl mb-4 w-full"></div>
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

                    <div wire:loading.remove wire:target="refreshSearch, category, q, startDate, endDate, resetFilters"
                        class="w-full">
                        <div
                            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-6 gap-y-10 md:gap-y-12">
                            @forelse($catalog as $car)
                                <a wire:key="car-{{ $car->id }}" href="{{ route('rent.show', $car) }}"
                                    class="group block overflow-hidden rounded-2xl bg-white" wire:navigate>
                                    <div class="relative aspect-video overflow-hidden bg-slate-100">
                                        <img src="{{ $car->image_url }}" alt="{{ $car->name }}"
                                            class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105" />
                                        <div class="absolute inset-0 bg-black/5"></div>
                                        <div class="absolute bottom-4 left-4">
                                            <span
                                                class="rounded-full bg-white/95 px-3 py-1 text-[0.65rem] font-semibold uppercase tracking-[0.16em] text-slate-800">
                                                {{ $car->category }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="space-y-2.5 px-2 pt-3.5 pb-3">
                                        <div class="flex items-start justify-between gap-3">
                                            <h3
                                                class="text-[1.12rem] font-semibold leading-snug tracking-tight text-slate-900">
                                                {{ $car->name }}
                                            </h3>
                                            <div class="text-right">
                                                <p
                                                    class="text-[1.35rem] font-semibold leading-none tracking-tight text-slate-900">
                                                    ₦{{ number_format($car->daily_price, 0) }}
                                                </p>
                                                <p
                                                    class="mt-1 text-[0.7rem] font-medium uppercase tracking-wide leading-none text-slate-500">
                                                    per day</p>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-3 gap-2 text-[0.83rem] text-slate-600">
                                            <div
                                                class="flex items-center justify-center gap-1 rounded-2xl bg-slate-50/70 px-2 py-1.5">
                                                <span
                                                    class="material-symbols-outlined text-[13px] text-slate-400">airline_seat_recline_normal</span>
                                                <span class="font-medium leading-none">{{ $car->seats }}</span>
                                            </div>
                                            <div
                                                class="flex items-center justify-center gap-1 rounded-2xl bg-slate-50/70 px-2 py-1.5">
                                                <span
                                                    class="material-symbols-outlined text-[13px] text-slate-400">settings</span>
                                                <span class="font-medium leading-none">{{ $car->transmission }}</span>
                                            </div>
                                            <div
                                                class="flex items-center justify-center gap-1 rounded-2xl bg-slate-50/70 px-2 py-1.5">
                                                <span
                                                    class="material-symbols-outlined text-[13px] text-slate-400">local_gas_station</span>
                                                <span class="font-medium leading-none">{{ $car->fuel_type ?? 'Gas' }}</span>
                                            </div>
                                        </div>

                                        <div
                                            class="inline-flex items-center gap-1.5 text-[0.95rem] font-medium text-slate-700 transition-colors group-hover:text-[#0e1133]">
                                            <span>Rent this car</span>
                                            <span
                                                class="material-symbols-outlined leading-none transition-transform group-hover:translate-x-1"
                                                style="font-size: 14px; font-variation-settings: 'wght' 300, 'opsz' 20;">arrow_forward</span>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div
                                    class="col-span-full py-20 text-center bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
                                    <span class="material-symbols-outlined text-6xl text-slate-300 mb-4">search_off</span>
                                    <p class="text-slate-500 font-bold text-xl">No cars found matching your search</p>
                                    <button wire:click="resetFilters"
                                        class="mt-4 text-[#0e1133] font-bold hover:underline">Clear all filters</button>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Infinite Scroll Sentinel -->
                @if($catalog->hasMorePages())
                    <div wire:key="sentinel" x-data="{
                                                         isLoading: false,
                                                         init() {
                                                             let observer = new IntersectionObserver((entries) => {
                                                                 entries.forEach(entry => {
                                                                     if (entry.isIntersecting && !this.isLoading) {
                                                                         this.isLoading = true;
                                                                         @this.call('loadMore').then(() => {
                                                                             setTimeout(() => { this.isLoading = false; }, 500);
                                                                         })
                                                                     }
                                                                 })
                                                             }, {
                                                                 rootMargin: '100px'
                                                             })
                                                             observer.observe($el)
                                                         }
                                                     }"
                        class="py-20 flex flex-col items-center justify-center min-h-[160px]">
                        <div wire:loading wire:target="loadMore" class="flex flex-col items-center">
                            <div class="w-10 h-10 border-4 border-slate-200 border-t-[#0e1133] rounded-full animate-spin">
                            </div>
                            <p class="mt-4 text-sm font-bold text-slate-500 uppercase tracking-widest">Discovering more
                                cars...</p>
                        </div>
                    </div>
                @else
                    <div wire:key="sentinel-end" class="py-20 text-center">
                        <p class="text-slate-400 font-bold uppercase tracking-widest text-xs">You've reached the end of the
                            road. No more cars to show.</p>
                    </div>
                @endif
            </section>
        </div>
    </main>

    @include('partials.footer')

    <div x-data="{
            showScrollTop: false,
            updateScrollTopVisibility() {
                this.showScrollTop = window.innerWidth >= 1024 && window.scrollY > 500;
            },
            init() {
                this.updateScrollTopVisibility();
                window.addEventListener('scroll', () => this.updateScrollTopVisibility());
                window.addEventListener('resize', () => this.updateScrollTopVisibility());
            }
        }" class="pointer-events-none fixed bottom-8 right-8 z-50 hidden lg:block">
        <button type="button" x-show="showScrollTop" x-transition.opacity.scale
            @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
            class="pointer-events-auto inline-flex h-12 w-12 items-center justify-center rounded-full bg-[#0e1133] text-white shadow-lg transition hover:bg-black focus:outline-none focus:ring-2 focus:ring-[#0e1133]/30"
            aria-label="Scroll to top">
            <span class="material-symbols-outlined text-[22px]">keyboard_arrow_up</span>
        </button>
    </div>
</div>