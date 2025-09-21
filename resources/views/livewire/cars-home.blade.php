<div>
    <div class="relative flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-hidden">
        <div class="layout-container flex h-full grow flex-col">
            @include('partials.header')

            <main class="flex-1 px-4 sm:px-6 lg:px-24 py-12">
                <div class="mx-auto max-w-5xl">
                    <!-- Parallax Hero Section -->
                    <section class="relative -mx-4 sm:-mx-6 lg:-mx-24 mb-6">
                        <div class="h-[46vh] min-h-[320px] w-full bg-center bg-no-repeat bg-cover bg-fixed" style="background-image: url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?q=80&w=1600&auto=format&fit=crop');"></div>
                        <div class="absolute inset-0 bg-gradient-to-b from-black/50 to-black/30"></div>
                        <div class="absolute inset-0 flex items-center">
                            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-24 w-full">
                                <div class="text-center">
                                    <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-3 tracking-tight">Find Your Perfect Ride</h1>
                                    <p class="text-lg text-white/90">Rent a car for your next adventure with ease.</p>
                                </div>
                                <div class="mt-6 max-w-3xl mx-auto">
                                    <div class="relative">
                                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-white/70">search</span>
                                        <input wire:model.debounce.400ms="q" class="form-input w-full rounded-full border-white/30 bg-white/90 backdrop-blur py-4 pl-12 pr-28 text-base text-slate-900 shadow-sm focus:border-[#1173d4] focus:ring-[#1173d4] placeholder:text-slate-500" placeholder="Search by name, category or location" type="text" />
                                        <button wire:click="refreshSearch" class="absolute right-2 top-1/2 -translate-y-1/2 flex min-w-[84px] max-w-[480px] items-center justify-center overflow-hidden rounded-full h-10 px-6 bg-[#1173d4] text-white text-sm font-bold tracking-wide hover:bg-[#0f63b9] transition-colors" type="button">
                                            <span class="truncate">Search</span>
                                        </button>
                                    </div>
                                </div>
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
                        <select wire:model.live="category" class="form-select rounded-md border-slate-300 focus:border-[#1173d4] focus:ring-[#1173d4]">
                            <option value="">All Categories</option>
                            @foreach(($options['categories'] ?? []) as $c)
                                <option value="{{ $c }}">{{ $c }}</option>
                            @endforeach
                        </select>
                        <select wire:model.live="transmission" class="form-select rounded-md border-slate-300 focus:border-[#1173d4] focus:ring-[#1173d4]">
                            <option value="">Any Transmission</option>
                            @foreach(($options['transmissions'] ?? []) as $t)
                                <option value="{{ $t }}">{{ $t }}</option>
                            @endforeach
                        </select>
                        <select wire:model.live="fuelType" class="form-select rounded-md border-slate-300 focus:border-[#1173d4] focus:ring-[#1173d4]">
                            <option value="">Any Fuel</option>
                            @foreach(($options['fuels'] ?? []) as $f)
                                <option value="{{ $f }}">{{ $f }}</option>
                            @endforeach
                        </select>
                        <select wire:model.live="seats" class="form-select rounded-md border-slate-300 focus:border-[#1173d4] focus:ring-[#1173d4]">
                            <option value="">Any Seats</option>
                            @foreach(($options['seats'] ?? []) as $s)
                                <option value="{{ $s }}">{{ $s }} seats</option>
                            @endforeach
                        </select>
{{--                        ww--}}
                        <select wire:model.live="sort" class="form-select rounded-md border-slate-300 focus:border-[#1173d4] focus:ring-[#1173d4]">
                            @foreach(($options['sorts'] ?? []) as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <div class="md:col-span-5 grid grid-cols-2 md:grid-cols-4 gap-3 mt-1">
                            <input wire:model.debounce.400ms="location" type="text" placeholder="Location" class="form-input rounded-md border-slate-300 focus:border-[#1173d4] focus:ring-[#1173d4]" />
                            <input wire:model.debounce.400ms="minPrice" type="number" min="0" placeholder="Min ₦/day" class="form-input rounded-md border-slate-300 focus:border-[#1173d4] focus:ring-[#1173d4]" />
                            <input wire:model.debounce.400ms="maxPrice" type="number" min="0" placeholder="Max ₦/day" class="form-input rounded-md border-slate-300 focus:border-[#1173d4] focus:ring-[#1173d4]" />
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
                                        <p class="text-slate-600 text-sm">{{ $car->category }} • {{ $car->transmission }} • {{ $car->seats }} seats</p>
                                        <div class="mt-3 flex justify-between items-center">
                                            <p class="text-lg font-bold text-slate-900">₦{{ number_format($car->daily_price, 0) }}<span class="text-sm font-normal text-slate-500">/day</span></p>
                                            <a href="{{ route('rent.show', $car) }}" class="bg-[#1173d4] text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-opacity-90" wire:navigate>Rent Now</a>
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
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            @forelse($catalog as $car)
                                <div class="flex flex-col gap-3 group rounded-lg overflow-hidden bg-white shadow-sm hover:shadow-lg transition-shadow duration-300">
                                    <div class="w-full bg-center bg-no-repeat aspect-video bg-cover" style="background-image: url('{{ $car->image_url }}');"></div>
                                    <div class="p-4 pt-3">
                                        <h3 class="text-base font-bold text-slate-900">{{ $car->name }}</h3>
                                        <p class="text-slate-600 text-sm">{{ $car->category }} • {{ $car->transmission }} • {{ $car->seats }} seats</p>
                                        <div class="mt-3 flex justify-between items-center">
                                            <p class="text-lg font-bold text-slate-900">₦{{ number_format($car->daily_price, 0) }}<span class="text-sm font-normal text-slate-500">/day</span></p>
                                            <a href="{{ route('rent.show', $car) }}" class="bg-[#1173d4] text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-opacity-90" wire:navigate>Rent Now</a>
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

            <footer class="bg-slate-100 border-t border-slate-200">
                <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-8">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                        <div class="flex items-center gap-3 text-slate-900 col-span-2 md:col-span-1">
                            <svg class="h-8 w-8 text-[#1173d4]" fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 42.4379C4 42.4379 14.0962 36.0744 24 41.1692C35.0664 46.8624 44 42.2078 44 42.2078L44 7.01134C44 7.01134 35.068 11.6577 24.0031 5.96913C14.0971 0.876274 4 7.27094 4 7.27094L4 42.4379Z" fill="currentColor"></path>
                            </svg>
                            <h2 class="text-xl font-bold tracking-tight">{{ config('app.name') }}</h2>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-slate-800 tracking-wider uppercase">Company</h3>
                            <ul class="mt-4 space-y-2">
                                <li><a class="text-base text-slate-600 hover:text-[#1173d4]" href="#">About</a></li>
                                <li><a class="text-base text-slate-600 hover:text-[#1173d4]" href="#">Careers</a></li>
                                <li><a class="text-base text-slate-600 hover:text-[#1173d4]" href="#">Press</a></li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-slate-800 tracking-wider uppercase">Support</h3>
                            <ul class="mt-4 space-y-2">
                                <li><a class="text-base text-slate-600 hover:text-[#1173d4]" href="#">Contact Us</a></li>
                                <li><a class="text-base text-slate-600 hover:text-[#1173d4]" href="#">FAQ</a></li>
                                <li><a class="text-base text-slate-600 hover:text-[#1173d4]" href="#">Terms of Service</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="mt-8 border-t border-slate-200 pt-8 flex flex-col md:flex-row items-center justify-between">
                        <p class="text-sm text-slate-500">© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                        <div class="flex space-x-6 mt-4 md:mt-0">
                            <a class="text-slate-500 hover:text-[#1173d4]" href="#"><span class="sr-only">Facebook</span><svg aria-hidden="true" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path clip-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" fill-rule="evenodd"></path></svg></a>
                            <a class="text-slate-500 hover:text-[#1173d4]" href="#"><span class="sr-only">Twitter</span><svg aria-hidden="true" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.71v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"></path></svg></a>
                            <a class="text-slate-500 hover:text-[#1173d4]" href="#"><span class="sr-only">Instagram</span><svg aria-hidden="true" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path clip-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.024.06 1.378.06 3.808s-.012 2.784-.06 3.808c-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.024.048-1.378.06-3.808.06s-2.784-.013-3.808-.06c-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.048-1.024-.06-1.378-.06-3.808s.012-2.784.06-3.808c.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 016.08 2.525c.636-.247 1.363-.416 2.427-.465C9.53 2.013 9.884 2 12.315 2zm-1.125 1.625h2.25c2.32 0 2.613.01 3.53.056.89.043 1.447.203 1.84.368.447.182.792.402 1.127.737.335.335.555.68.737 1.127.165.393.325.95.368 1.84.046.917.055 1.21.055 3.53s-.01 2.613-.056 3.53c-.043.89-.203 1.447-.368 1.84a3.32 3.32 0 01-.737 1.127 3.32 3.32 0 01-1.127.737c-.393.165-.95.325-1.84.368-.917.046-1.21.055-3.53.055s-2.613-.01-3.53-.056c-.89-.043-1.447-.203-1.84-.368a3.32 3.32 0 01-1.127-.737 3.32 3.32 0 01-.737-1.127c-.165-.393-.325-.95-.368-1.84-.046-.917-.055-1.21-.055-3.53s.01-2.613.056-3.53c.043-.89.203-1.447.368-1.84.182-.447.402-.792.737-1.127.335-.335.68-.555 1.127-.737.393-.165.95-.325 1.84-.368.917-.046 1.21-.055 3.53-.055z" fill-rule="evenodd"></path><path d="M12 8.25a3.75 3.75 0 100 7.5 3.75 3.75 0 000-7.5zM8.25 12a3.75 3.75 0 117.5 0 3.75 3.75 0 01-7.5 0z"></path></svg></a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</div>
