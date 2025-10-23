<div>
    <div class="relative flex min-h-screen w-full flex-col overflow-x-hidden bg-slate-50 text-slate-900" style='font-family: "Work Sans", "Noto Sans", sans-serif;'>
        <div class="layout-container flex h-full grow flex-col">
            <header class="sticky top-0 z-10 flex items-center justify-between whitespace-nowrap border-b border-slate-200 bg-white/80 px-4 py-3 backdrop-blur-sm sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <button type="button" class="lg:hidden inline-flex h-10 w-10 items-center justify-center rounded-md text-slate-600 hover:bg-slate-100" aria-label="Open menu" data-admin-menu-open aria-controls="admin-mobile-drawer" aria-expanded="false">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                    <svg class="h-8 w-8 text-sky-600" fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.8284 24L24 10.8284L37.1716 24L24 37.1716L10.8284 24Z" stroke="currentColor" stroke-linejoin="round" stroke-width="4"></path>
                        <path d="M4 24H44" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="4"></path>
                    </svg>
                    <h1 class="text-xl font-bold tracking-tight">{{ config('app.name') }} — Admin</h1>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-slate-600 hidden sm:block">{{ auth()->user()->name ?? 'Admin' }}</span>
                    <button class="flex h-10 w-10 items-center justify-center rounded-full text-slate-500 hover:bg-slate-100 hover:text-slate-700" title="Notifications">
                        <span class="material-symbols-outlined"> notifications </span>
                    </button>
                    @include('admin.partials.user-menu')
                </div>
            </header>

            <div class="flex flex-1">
                @include('admin.partials.sidebar', ['active' => 'cars'])

                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    @include('admin.partials.breadcrumbs', ['items' => [
                        ['label' => 'Dashboard', 'url' => route('dashboard')],
                        ['label' => 'Cars', 'url' => null],
                    ]])
                    <div class="mb-6 flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-3xl font-bold tracking-tight">Car Management</h2>
                            <p class="mt-1 text-slate-500">Search, add, edit, and manage your cars. View bookings per car.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button class="inline-flex items-center gap-2 rounded-md h-10 px-4 bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700" wire:click="openCreate">
                                <span class="material-symbols-outlined text-base">add</span>
                                <span>Add New Car</span>
                            </button>
{{--                            <a href="{{ route('admin.cars') }}#manage-options" class="inline-flex items-center gap-2 rounded-md h-10 px-4 border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50">--}}
{{--                                <span class="material-symbols-outlined text-base">tune</span>--}}
{{--                                <span>Manage option lists</span>--}}
{{--                            </a>--}}
                        </div>
                    </div>

{{--                    <div id="manage-options" class="mb-6 rounded-lg bg-white shadow-sm border border-slate-200 p-4">--}}
{{--                        <h3 class="text-lg font-semibold text-slate-900 mb-3">Manage option lists</h3>--}}
{{--                        <p class="text-sm text-slate-600 mb-3">These dropdown options are admin-managed. Add or remove values as needed.</p>--}}
{{--                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">--}}
{{--                            <div>--}}
{{--                                <div class="text-sm font-medium text-slate-800 mb-2">Categories</div>--}}
{{--                                <form wire:submit.prevent="saveOptions">--}}
{{--                                    <div class="space-y-2">--}}
{{--                                        @php $cats = ($options['categories'] ?? []); @endphp--}}
{{--                                        @foreach($cats as $i => $val)--}}
{{--                                            <div class="flex gap-2">--}}
{{--                                                <input type="text" class="form-input w-full rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.defer="options.categories.{{ $i }}">--}}
{{--                                                <button type="button" class="rounded-md px-2 border border-slate-300 text-slate-700 text-xs hover:bg-slate-50" wire:click="$set('options.categories.{{ $i }}', null)">Remove</button>--}}
{{--                                            </div>--}}
{{--                                        @endforeach--}}
{{--                                        <button type="button" class="rounded-md px-3 h-9 border border-slate-300 text-slate-700 text-sm hover:bg-slate-50" wire:click="$set('options.categories', array_merge(($options['categories'] ?? []), ['']))">Add</button>--}}
{{--                                    </div>--}}
{{--                                </form>--}}
{{--                            </div>--}}
{{--                            <div>--}}
{{--                                <div class="text-sm font-medium text-slate-800 mb-2">Transmissions</div>--}}
{{--                                <div class="space-y-2">--}}
{{--                                    @php $trs = ($options['transmissions'] ?? []); @endphp--}}
{{--                                    @foreach($trs as $i => $val)--}}
{{--                                        <div class="flex gap-2">--}}
{{--                                            <input type="text" class="form-input w-full rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.defer="options.transmissions.{{ $i }}">--}}
{{--                                            <button type="button" class="rounded-md px-2 border border-slate-300 text-slate-700 text-xs hover:bg-slate-50" wire:click="$set('options.transmissions.{{ $i }}', null)">Remove</button>--}}
{{--                                        </div>--}}
{{--                                    @endforeach--}}
{{--                                    <button type="button" class="rounded-md px-3 h-9 border border-slate-300 text-slate-700 text-sm hover:bg-slate-50" wire:click="$set('options.transmissions', array_merge(($options['transmissions'] ?? []), ['']))">Add</button>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div>--}}
{{--                                <div class="text-sm font-medium text-slate-800 mb-2">Fuels</div>--}}
{{--                                <div class="space-y-2">--}}
{{--                                    @php $fu = ($options['fuels'] ?? []); @endphp--}}
{{--                                    @foreach($fu as $i => $val)--}}
{{--                                        <div class="flex gap-2">--}}
{{--                                            <input type="text" class="form-input w-full rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.defer="options.fuels.{{ $i }}">--}}
{{--                                            <button type="button" class="rounded-md px-2 border border-slate-300 text-slate-700 text-xs hover:bg-slate-50" wire:click="$set('options.fuels.{{ $i }}', null)">Remove</button>--}}
{{--                                        </div>--}}
{{--                                    @endforeach--}}
{{--                                    <button type="button" class="rounded-md px-3 h-9 border border-slate-300 text-slate-700 text-sm hover:bg-slate-50" wire:click="$set('options.fuels', array_merge(($options['fuels'] ?? []), ['']))">Add</button>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="mt-4">--}}
{{--                            <button class="rounded-md h-10 px-4 bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700" wire:click="persistOptions">Save option lists</button>--}}
{{--                        </div>--}}
{{--                    </div>--}}

                    @if (session('success'))
                        <div class="mb-4 rounded-md border border-green-300 bg-green-50 px-4 py-3 text-green-800">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 rounded-md border border-red-300 bg-red-50 px-4 py-3 text-red-800">{{ session('error') }}</div>
                    @endif

                    <!-- Filters -->
                    <div class="rounded-lg bg-white shadow-sm border border-slate-200 p-4 mb-4">
                        <div class="flex flex-col md:flex-row gap-3 items-stretch md:items-center">
                            <div class="relative md:flex-1">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                                <input type="search" placeholder="Search by name, category, location, transmission or fuel" class="form-input w-full pl-10 pr-3 py-2.5 rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.live.debounce.400ms="q">
                            </div>
                            <div class="flex gap-2">
                                <button type="button" class="rounded-md border border-slate-300 px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50" wire:click="resetFilters">Reset</button>
                                <button type="button" class="inline-flex items-center gap-2 rounded-md px-3 py-2.5 text-sm font-medium border border-slate-300 text-slate-700 hover:bg-slate-50" wire:click="toggleAdvanced" aria-expanded="{{ $showAdvanced ? 'true' : 'false' }}">
                                    <span class="material-symbols-outlined text-base">{{ $showAdvanced ? 'expand_less' : 'tune' }}</span>
                                    <span>{{ $showAdvanced ? 'Hide advanced' : 'Advanced filters' }}</span>
                                </button>
                            </div>
                        </div>
                        @if($showAdvanced)
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-8 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Category</label>
                                <select class="form-select w-full px-3 py-2.5 rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.live="category">
                                    <option value="">All</option>
                                    @foreach(($options['categories'] ?? []) as $c)
                                        <option value="{{ $c }}">{{ $c }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Location</label>
                                <select class="form-select w-full px-3 py-2.5 rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.live="locationFilter">
                                    <option value="">All</option>
                                    @foreach(($options['locations'] ?? []) as $loc)
                                        <option value="{{ $loc }}">{{ $loc }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Transmission</label>
                                <select class="form-select w-full px-3 py-2.5 rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.live="transmission">
                                    <option value="">Any</option>
                                    @foreach(($options['transmissions'] ?? []) as $t)
                                        <option value="{{ $t }}">{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Fuel</label>
                                <select class="form-select w-full px-3 py-2.5 rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.live="fuel_type">
                                    <option value="">Any</option>
                                    @foreach(($options['fuels'] ?? []) as $f)
                                        <option value="{{ $f }}">{{ $f }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Seats</label>
                                <select class="form-select w-full px-3 py-2.5 rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.live="seats">
                                    <option value="">Any</option>
                                    @foreach(($options['seats'] ?? []) as $s)
                                        <option value="{{ $s }}">{{ $s }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Featured</label>
                                <select class="form-select w-full px-3 py-2.5 rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.live="featured">
                                    <option value="">Any</option>
                                    <option value="1">Featured</option>
                                    <option value="0">Not Featured</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Min ₦/day</label>
                                <input type="number" class="form-input w-full px-3 py-2.5 rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.debounce.400ms="minPrice" min="0">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Max ₦/day</label>
                                <input type="number" class="form-input w-full px-3 py-2.5 rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.debounce.400ms="maxPrice" min="0">
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Mobile list -->
                    <div class="md:hidden space-y-3">
                        @forelse($cars as $car)
                            <div class="rounded-lg border border-slate-200 bg-white shadow-sm p-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-24 h-16 bg-slate-100 rounded-md bg-cover bg-center" style="background-image: url('{{ ($car->images[0] ?? $car->image_url) ?: 'https://via.placeholder.com/320x180?text=Car' }}');"></div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <div class="text-sm font-semibold text-slate-900">{{ $car->name }}</div>
                                            @if(!$car->is_active)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-slate-200 text-slate-800">Inactive</span>
                                            @endif
                                            @php $av = $availability[$car->id] ?? true; @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium {{ $av ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $av ? 'Available' : 'Unavailable' }}</span>
                                        </div>
                                        <div class="text-xs text-slate-600">{{ $car->category }} • {{ $car->transmission }} • {{ $car->seats }} seats</div>
                                        <div class="mt-1 text-sm font-bold">₦{{ number_format((float)($car->daily_price ?? 0), 0) }}<span class="text-xs font-normal text-slate-500">/day</span></div>
                                    </div>
                                    <div class="relative inline-block text-left" data-dropdown>
                                        <button type="button" class="inline-flex items-center gap-1.5 rounded-md h-9 px-3 border border-slate-300 text-slate-700 font-medium hover:bg-slate-50" data-dropdown-button aria-haspopup="true" aria-expanded="false">
                                            <span class="material-symbols-outlined text-base">more_vert</span>
                                            <span>Actions</span>
                                        </button>
                                        <div class="absolute right-0 mt-2 w-44 origin-top-right rounded-md border border-slate-200 bg-white shadow-lg z-30 hidden" data-dropdown-menu>
                                            <div class="py-1 text-sm flex flex-col items-stretch">
                                                <button class="w-full text-left px-3 py-2 hover:bg-slate-50" wire:click="openEdit({{ $car->id }})">Edit</button>
                                                <a class="w-full text-left px-3 py-2 hover:bg-slate-50 text-left" href="{{ route('admin.bookings', ['car' => $car->id]) }}" wire:navigate>Manage bookings</a>
                                                @if($car->is_active)
                                                    <button class="w-full text-left px-3 py-2 hover:bg-slate-50" wire:click="openDisable({{ $car->id }})">Disable</button>
                                                @else
                                                    <button class="w-full text-left px-3 py-2 hover:bg-slate-50" wire:click="openEnable({{ $car->id }})">Enable</button>
                                                @endif
                                                <button class="w-full text-left px-3 py-2 text-red-600 hover:bg-red-50" wire:click="openDelete({{ $car->id }})">Delete</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-md border border-slate-200 bg-white p-6 text-center text-sm text-slate-500">No cars found.</div>
                        @endforelse
                    </div>

                    <!-- Desktop table -->
                    <div class="hidden md:block overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
                        <table class="min-w-full text-left">
                            <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Image</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Name</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Category</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Trans.</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Seats</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Availability</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider text-right">₦/day</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider text-right">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                            @forelse($cars as $car)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="w-16 h-12 bg-slate-100 rounded-md bg-cover bg-center" style="background-image: url('{{ ($car->images[0] ?? $car->image_url) ?: 'https://via.placeholder.com/320x180?text=Car' }}');"></div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-slate-900">
                                        <div class="flex items-center gap-2">
                                            <span>{{ $car->name }}</span>
                                            @if(!$car->is_active)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-slate-200 text-slate-800">Inactive</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-700">{{ $car->category }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-700">{{ $car->transmission }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-700">{{ $car->seats }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        @php $av = $availability[$car->id] ?? true; @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $av ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $av ? 'Available' : 'Unavailable' }}</span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-slate-900">₦{{ number_format((float)($car->daily_price ?? 0), 0) }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-right">
                                        <div class="relative inline-block text-left" data-dropdown>
                                            <button type="button" class="inline-flex items-center gap-2 rounded-md h-9 px-3 border border-slate-300 text-slate-700 font-medium hover:bg-slate-50" data-dropdown-button aria-haspopup="true" aria-expanded="false">
                                                <span class="material-symbols-outlined text-base">more_vert</span>
                                                <span>Actions</span>
                                            </button>
                                            <div class="absolute right-0 mt-2 w-44 origin-top-right rounded-md border border-slate-200 bg-white shadow-lg z-30 hidden" data-dropdown-menu>
                                                <div class="py-1 text-sm flex flex-col items-stretch">
                                                    <button class="w-full text-left px-3 py-2 hover:bg-slate-50" wire:click="openEdit({{ $car->id }})">Edit</button>
                                                    <a class="w-full text-left px-3 py-2 hover:bg-slate-50 text-left" href="{{ route('admin.bookings', ['car' => $car->id]) }}" wire:navigate>Manage bookings</a>
                                                    @if($car->is_active)
                                                        <button class="w-full text-left px-3 py-2 hover:bg-slate-50" wire:click="openDisable({{ $car->id }})">Disable</button>
                                                    @else
                                                        <button class="w-full text-left px-3 py-2 hover:bg-slate-50" wire:click="openEnable({{ $car->id }})">Enable</button>
                                                    @endif
                                                    <button class="w-full text-left px-3 py-2 text-red-600 hover:bg-red-50" wire:click="openDelete({{ $car->id }})">Delete</button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-sm text-slate-500">No cars found.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex items-center justify-between">
                        <div class="text-xs text-slate-500">
                            @if($cars instanceof \Illuminate\Contracts\Pagination\Paginator)
                                Showing page {{ $cars->currentPage() }} of {{ $cars->lastPage() }}
                            @endif
                        </div>
                        <div>
                            {{ $cars->onEachSide(1)->links() }}
                        </div>
                    </div>
                </main>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        @if($editOpen)
            <div class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="absolute inset-0 bg-black/50" wire:click="$set('editOpen', false)"></div>
                <div class="relative z-10 w-full max-w-2xl rounded-lg bg-white shadow-xl border border-slate-200 max-h-[85vh] flex flex-col">
                    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-slate-900">{{ $editingId ? 'Edit Car' : 'Add New Car' }}</h3>
                        <button class="p-1 text-slate-500 hover:text-slate-700" wire:click="$set('editOpen', false)">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                    <div class="px-5 py-4 text-sm text-slate-700 overflow-y-auto max-h-[65vh]">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Name</label>
                                <input type="text" class="form-input w-full rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.defer="name">
                                @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Category</label>
                                <select class="form-select w-full rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.defer="category_field">
                                    <option value="">Select category</option>
                                    @foreach(($options['categories'] ?? []) as $c)
                                        <option value="{{ $c }}">{{ $c }}</option>
                                    @endforeach
                                </select>
                                @error('category_field') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                                <textarea rows="3" class="form-textarea w-full rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.defer="description"></textarea>
                                @error('description') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            @if($editingId)
                                @php
                                    $adminGallery = array_values(array_filter(array_merge([
                                        ($image_url ?? '') !== '' ? $image_url : null,
                                    ], (array)($images ?? []))));
                                    if (empty($adminGallery)) {
                                        $adminGallery = ['https://via.placeholder.com/1280x720?text=No+Image'];
                                    }
                                @endphp
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Existing images</label>
                                    <div class="rounded-lg overflow-hidden bg-white border border-slate-200">
                                        <div class="relative">
                                            <img id="admin-edit-main-image" src="{{ $adminGallery[0] }}" alt="Existing image" class="w-full aspect-video object-cover" />
                                            @if(count($adminGallery) > 1)
                                                <button type="button" class="absolute left-2 top-1/2 -translate-y-1/2 inline-flex items-center justify-center rounded-full bg-black/40 text-white hover:bg-black/60 w-8 h-8" data-admin-gal-prev aria-label="Previous image">
                                                    <span class="material-symbols-outlined text-sm">chevron_left</span>
                                                </button>
                                                <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 inline-flex items-center justify-center rounded-full bg-black/40 text-white hover:bg-black/60 w-8 h-8" data-admin-gal-next aria-label="Next image">
                                                    <span class="material-symbols-outlined text-sm">chevron_right</span>
                                                </button>
                                            @endif
                                        </div>
                                        @if(count($adminGallery) > 1)
                                        <div class="p-2 border-t border-slate-200">
                                            <div class="flex gap-2 overflow-x-auto" id="admin-edit-thumbs" aria-label="Existing image thumbnails">
                                                @foreach($adminGallery as $i => $src)
                                                    <button type="button" class="shrink-0 rounded-md overflow-hidden border {{ $i === 0 ? 'ring-2 ring-sky-600 border-sky-200' : 'border-slate-200' }}" data-admin-gal-thumb data-index="{{ $i }}" aria-label="Show image {{ $i + 1 }}">
                                                        <img src="{{ $src }}" alt="Existing image {{ $i + 1 }}" class="w-16 h-12 object-cover" />
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    <p class="mt-2 text-xs text-slate-500">These are the currently saved images for this car. Upload new images below to replace/add.</p>
                                </div>
                            @endif

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Primary image</label>
                                <input id="primaryUploadInput" type="file" class="hidden" wire:model="primaryUpload" accept="image/*">
                                <div
                                    class="group rounded-md border-2 border-dashed border-slate-300 bg-slate-50/50 hover:bg-slate-50 transition-colors p-4 cursor-pointer"
                                    role="button"
                                    tabindex="0"
                                    data-dropzone
                                    data-dropzone-target="#primaryUploadInput"
                                    onclick="document.getElementById('primaryUploadInput')?.click()"
                                    onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();document.getElementById('primaryUploadInput')?.click()}"
                                >
                                    @if($primaryUpload)
                                        <div class="relative">
                                            <img src="{{ $primaryUpload->temporaryUrl() }}" alt="Primary preview" class="w-full aspect-video object-cover rounded" />
                                            <div class="mt-2 flex items-center justify-between">
                                                <span class="text-xs text-slate-600">Selected: {{ $primaryUpload->getClientOriginalName() }}</span>
                                                <button type="button" class="text-xs text-red-600 hover:underline" wire:click="$set('primaryUpload', null)">Remove</button>
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex flex-col items-center justify-center text-center py-8">
                                            <span class="material-symbols-outlined text-4xl text-slate-400 mb-2">upload_file</span>
                                            <p class="text-sm text-slate-700"><span class="font-medium">Click to upload</span> or drag & drop</p>
                                            <p class="text-xs text-slate-500 mt-1">PNG, JPG up to 2MB</p>
                                        </div>
                                    @endif
                                    <div class="mt-2">
                                        <div class="text-xs text-slate-500" wire:loading wire:target="primaryUpload">Uploading...</div>
                                    </div>
                                </div>
                                @error('primaryUpload') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Daily price (₦)</label>
                                <input type="number" min="0" step="0.01" class="form-input w-full rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.defer="daily_price">
                                @error('daily_price') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Seats</label>
                                <input type="number" min="1" class="form-input w-full rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.defer="seats_field">
                                @error('seats_field') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Transmission</label>
                                <select class="form-select w-full rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.defer="transmission_field">
                                    <option value="">Select transmission</option>
                                    @foreach(($options['transmissions'] ?? []) as $t)
                                        <option value="{{ $t }}">{{ $t }}</option>
                                    @endforeach
                                </select>
                                @error('transmission_field') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Fuel type</label>
                                <select class="form-select w-full rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.defer="fuel_type_field">
                                    <option value="">Select fuel</option>
                                    @foreach(($options['fuels'] ?? []) as $f)
                                        <option value="{{ $f }}">{{ $f }}</option>
                                    @endforeach
                                </select>
                                @error('fuel_type_field') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Location</label>
                                <select class="form-select w-full rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.defer="location">
                                    <option value="">Select location</option>
                                    @foreach(($options['locations'] ?? []) as $loc)
                                        <option value="{{ $loc }}">{{ $loc }}</option>
                                    @endforeach
                                </select>
                                @error('location') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="flex items-center gap-2">
                                <input id="featured" type="checkbox" class="rounded border-slate-300 text-sky-600 focus:ring-sky-600" wire:model.live="featured_field">
                                <label for="featured" class="text-sm text-slate-700">Featured</label>
                                @error('featured_field') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Additional images</label>
                                <input id="galleryUploadsInput" type="file" class="hidden" wire:model="newGalleryUpload" accept="image/*">
                                <div
                                    class="group rounded-md border-2 border-dashed border-slate-300 bg-slate-50/50 hover:bg-slate-50 transition-colors p-4 cursor-pointer"
                                    role="button"
                                    tabindex="0"
                                    data-dropzone
                                    data-dropzone-target="#galleryUploadsInput"
                                    onclick="document.getElementById('galleryUploadsInput')?.click()"
                                    onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();document.getElementById('galleryUploadsInput')?.click()}"
                                >
                                    @if(is_array($galleryUploads) && count($galleryUploads) > 0)
                                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                                            @foreach($galleryUploads as $f)
                                                <div class="relative rounded overflow-hidden border border-slate-200 bg-white">
                                                    <img src="{{ $f->temporaryUrl() }}" alt="Gallery preview" class="w-full aspect-video object-cover" />
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="mt-2 flex items-center justify-between">
                                            <span class="text-xs text-slate-600">{{ count($galleryUploads) }} image(s) selected</span>
{{--                                            <button type="button" class="text-xs text-red-600 hover:underline" wire:click="$set('galleryUploads', [])">Remove all</button>--}}
                                        </div>
                                    @else
                                        <div class="flex flex-col items-center justify-center text-center py-8">
                                            <span class="material-symbols-outlined text-4xl text-slate-400 mb-2">imagesmode</span>
                                            <p class="text-sm text-slate-700"><span class="font-medium">Click to add an image</span> or drag & drop (add one at a time)</p>
                                            <p class="text-xs text-slate-500 mt-1">PNG, JPG up to 2MB each</p>
                                        </div>
                                    @endif
                                    <div class="mt-2">
                                        <div class="text-xs text-slate-500" wire:loading wire:target="newGalleryUpload, galleryUploads">Uploading...</div>
                                    </div>
                                </div>
                                @error('galleryUploads.*') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        @if(is_array($galleryUploads) && count($galleryUploads) > 0)
                        <button type="button" class="text-xs text-red-600 hover:underline" wire:click="$set('galleryUploads', [])">Remove all</button>
                        @endif
                    </div>
                    <div class="px-5 py-4 border-t border-slate-200 flex items-center justify-end gap-2">
                        <button class="rounded-md h-10 px-4 border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50" wire:click="$set('editOpen', false)">Close</button>
                        <button class="rounded-md h-10 px-4 bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700" wire:click="openSaveConfirm">Save</button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Save confirm modal -->
        @if($saveConfirmOpen)
            <div class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="absolute inset-0 bg-black/50" wire:click="closeSaveConfirm"></div>
                <div class="relative z-10 w-full max-w-md rounded-lg bg-white shadow-xl border border-slate-200">
                    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-slate-900">Confirm save</h3>
                        <button class="p-1 text-slate-500 hover:text-slate-700" wire:click="closeSaveConfirm">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                    <div class="px-5 py-4 text-sm text-slate-700">
                        <p>Are you sure you want to {{ $editingId ? 'update this car' : 'create this car' }} with the current details?</p>
                        <p class="text-xs text-slate-500 mt-2">You can edit these details later if needed.</p>
                    </div>
                    <div class="px-5 py-4 border-t border-slate-200 flex items-center justify-end gap-2">
                        <button class="rounded-md h-10 px-4 border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50" wire:click="closeSaveConfirm">Cancel</button>
                        <button class="rounded-md h-10 px-4 bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700" wire:click="confirmSave">Confirm save</button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Delete confirm modal -->
        @if($deleteOpen && $deleteId)
            <div class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="absolute inset-0 bg-black/50" wire:click="$set('deleteOpen', false)"></div>
                <div class="relative z-10 w-full max-w-md rounded-lg bg-white shadow-xl border border-slate-200">
                    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-slate-900">Delete car</h3>
                        <button class="p-1 text-slate-500 hover:text-slate-700" wire:click="$set('deleteOpen', false)">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                    <div class="px-5 py-4 text-sm text-slate-700">
                        <p>Are you sure you want to delete this car? This action will remove it from the admin list and customers will no longer see it. You can restore it later from the database since it is a soft delete.</p>
                    </div>
                    <div class="px-5 py-4 border-t border-slate-200 flex items-center justify-end gap-2">
                        <button class="rounded-md h-10 px-4 border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50" wire:click="$set('deleteOpen', false)">Cancel</button>
                        <button class="rounded-md h-10 px-4 bg-red-600 text-white text-sm font-semibold hover:bg-red-700" wire:click="confirmDelete">Confirm delete</button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Disable confirm modal -->
        @if($toggleOpen && $toggleMode === 'disable')
            <div class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="absolute inset-0 bg-black/50" wire:click="$set('toggleOpen', false)"></div>
                <div class="relative z-10 w-full max-w-md rounded-lg bg-white shadow-xl border border-slate-200">
                    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-slate-900">Disable car</h3>
                        <button class="p-1 text-slate-500 hover:text-slate-700" wire:click="$set('toggleOpen', false)">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                    <div class="px-5 py-4 text-sm text-slate-700">
                        <p>Are you sure you want to disable this car? It will be hidden from customers and cannot be booked until re-enabled.</p>
                    </div>
                    <div class="px-5 py-4 border-t border-slate-200 flex items-center justify-end gap-2">
                        <button class="rounded-md h-10 px-4 border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50" wire:click="$set('toggleOpen', false)">Cancel</button>
                        <button class="rounded-md h-10 px-4 bg-red-600 text-white text-sm font-semibold hover:bg-red-700" wire:click="confirmDisable">Confirm disable</button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Enable confirm modal -->
        @if($toggleOpen && $toggleMode === 'enable')
            <div class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="absolute inset-0 bg-black/50" wire:click="$set('toggleOpen', false)"></div>
                <div class="relative z-10 w-full max-w-md rounded-lg bg-white shadow-xl border border-slate-200">
                    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-slate-900">Enable car</h3>
                        <button class="p-1 text-slate-500 hover:text-slate-700" wire:click="$set('toggleOpen', false)">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                    <div class="px-5 py-4 text-sm text-slate-700">
                        <p>Are you sure you want to enable this car? It will become visible to customers and available for booking.</p>
                    </div>
                    <div class="px-5 py-4 border-t border-slate-200 flex items-center justify-end gap-2">
                        <button class="rounded-md h-10 px-4 border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50" wire:click="$set('toggleOpen', false)">Cancel</button>
                        <button class="rounded-md h-10 px-4 bg-green-600 text-white text-sm font-semibold hover:bg-green-700" wire:click="confirmEnable">Confirm enable</button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
// Accessible dropdowns for action menus (re-usable init similar to bookings page)
(function(){
  function initDropdown(root){
    if (!root || root.__ddInited) return; root.__ddInited = true;
    const btn = root.querySelector('[data-dropdown-button]');
    const menu = root.querySelector('[data-dropdown-menu]');
    if (!btn || !menu) return;
    function open(){ menu.classList.remove('hidden'); btn.setAttribute('aria-expanded','true'); }
    function close(){ menu.classList.add('hidden'); btn.setAttribute('aria-expanded','false'); }
    function toggle(){ (btn.getAttribute('aria-expanded') === 'true') ? close() : open(); }
    btn.addEventListener('click', function(e){ e.stopPropagation(); toggle(); });
    document.addEventListener('click', function(e){ if (menu.classList.contains('hidden')) return; if (!root.contains(e.target)) close(); });
    document.addEventListener('keydown', function(e){ if (e.key === 'Escape') close(); });
    window.addEventListener('livewire:navigated', close);
  }
  function initAll(){ document.querySelectorAll('[data-dropdown]').forEach(initDropdown); }
  if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', initAll); } else { initAll(); }
  window.addEventListener('livewire:navigated', initAll);
})();
</script>

<script>
// Drag & Drop file pickers for image uploads (primary and gallery)
(function(){
  function bindDropzone(el){
    if (!el || el.__dzBound) return; el.__dzBound = true;
    var targetSel = el.getAttribute('data-dropzone-target');
    var input = targetSel ? document.querySelector(targetSel) : null;
    if (!input) return;
    function prevent(e){ e.preventDefault(); e.stopPropagation(); }
    function onOver(e){ prevent(e); el.classList.add('border-sky-400','bg-sky-50/50'); el.classList.remove('border-slate-300'); }
    function onLeave(e){ prevent(e); el.classList.remove('border-sky-400','bg-sky-50/50'); el.classList.add('border-slate-300'); }
    function onDrop(e){
      prevent(e);
      try { el.classList.remove('border-sky-400','bg-sky-50/50'); el.classList.add('border-slate-300'); } catch(_){ }
      var files = (e.dataTransfer && e.dataTransfer.files) ? e.dataTransfer.files : null;
      if (!files || files.length === 0) return;
      try {
        // Assign dropped files to input and trigger change
        input.files = files;
      } catch(_){
        // Fallback: open native picker as a graceful degradation
      }
      try { input.dispatchEvent(new Event('change', { bubbles: true })); } catch(_){ }
    }
    el.addEventListener('dragenter', onOver);
    el.addEventListener('dragover', onOver);
    el.addEventListener('dragleave', onLeave);
    el.addEventListener('drop', onDrop);
  }
  function initAll(){ document.querySelectorAll('[data-dropzone]').forEach(bindDropzone); }
  if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', initAll); } else { initAll(); }
  window.addEventListener('livewire:navigated', initAll);
  window.addEventListener('livewire:load', initAll);
  window.addEventListener('livewire:update', initAll);
})();
</script>

<script>
// Lightweight gallery for admin edit modal (robust init via Livewire events + MutationObserver)
(function(){
  function initAdminEditGallery(){
    const main = document.getElementById('admin-edit-main-image');
    if (!main) return;
    if (main.__galInited) return; // prevent double-init on Livewire/DOM updates
    main.__galInited = true;
    const thumbs = document.getElementById('admin-edit-thumbs');
    const prev = document.querySelector('[data-admin-gal-prev]');
    const next = document.querySelector('[data-admin-gal-next]');
    const imgs = thumbs ? Array.from(thumbs.querySelectorAll('[data-admin-gal-thumb] img')).map(img => img.getAttribute('src')) : [main.getAttribute('src')];
    let idx = 0;
    function setIndex(i){
      if (!imgs.length) return;
      idx = (i + imgs.length) % imgs.length;
      main.src = imgs[idx];
      if (thumbs){
        thumbs.querySelectorAll('[data-admin-gal-thumb]').forEach((btn, j) => {
          if (j === idx){ btn.classList.add('ring-2','ring-sky-600'); }
          else { btn.classList.remove('ring-2','ring-sky-600'); }
        });
      }
    }
    if (thumbs){
      thumbs.querySelectorAll('[data-admin-gal-thumb]').forEach((btn) => {
        btn.addEventListener('click', () => {
          const i = parseInt(btn.getAttribute('data-index') || '0', 10) || 0;
          setIndex(i);
        });
      });
    }
    if (prev) prev.addEventListener('click', () => setIndex(idx - 1));
    if (next) next.addEventListener('click', () => setIndex(idx + 1));
    document.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowLeft' && prev) setIndex(idx - 1);
      if (e.key === 'ArrowRight' && next) setIndex(idx + 1);
    });
  }

  function tryInit(){
    // Defer so Livewire DOM changes are in place
    setTimeout(initAdminEditGallery, 0);
  }

  // 1) Traditional lifecycle hooks (may vary by Livewire version)
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', tryInit); else tryInit();
  window.addEventListener('livewire:navigated', tryInit);
  window.addEventListener('livewire:load', tryInit);
  window.addEventListener('livewire:update', tryInit);

  // 2) MutationObserver fallback to catch modal content insertion
  const mo = new MutationObserver(() => {
    const el = document.getElementById('admin-edit-main-image');
    if (el && !el.__galInited) {
      tryInit();
    }
  });
  try {
    mo.observe(document.documentElement || document.body, { childList: true, subtree: true });
  } catch (e) { /* no-op */ }
})();
</script>
