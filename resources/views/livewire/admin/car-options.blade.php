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
                @include('admin.partials.sidebar', ['active' => 'car-options'])

                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    @include('admin.partials.breadcrumbs', ['items' => [
                        ['label' => 'Dashboard', 'url' => route('dashboard')],
                        ['label' => 'Car Options', 'url' => null],
                    ]])
                    <div class="mb-6">
                        <h2 class="text-3xl font-bold tracking-tight">Car Option Lists</h2>
                        <p class="mt-1 text-slate-500">Manage dropdown options for Category, Transmission, and Fuel Type.</p>
                    </div>

                    @if (session('success'))
                        <div class="mb-4 rounded-md border border-green-300 bg-green-50 px-4 py-3 text-green-800">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 rounded-md border border-red-300 bg-red-50 px-4 py-3 text-red-800">{{ session('error') }}</div>
                    @endif

                    <!-- Tabs header -->
                    @php
                        $tabs = [
                            'categories' => 'Categories',
                            'transmissions' => 'Transmissions',
                            'fuels' => 'Fuels',
                            'locations' => 'Locations',
                            'extras' => 'Extras',
                            'serviceTypes' => 'Service Types',
                            'settings' => 'Settings',
                        ];
                    @endphp
                    <div class="mb-4 border-b border-slate-200">
                        <nav class="-mb-px flex flex-wrap gap-2" aria-label="Tabs">
                            @foreach($tabs as $key => $label)
                                @php $active = ($tab === $key); @endphp
                                <button type="button"
                                        class="whitespace-nowrap border-b-2 px-3 py-2 text-sm font-medium focus:outline-none {{ $active ? 'border-sky-600 text-sky-700' : 'border-transparent text-slate-600 hover:text-slate-800 hover:border-slate-300' }}"
                                        wire:click="setTab('{{ $key }}')"
                                        aria-current="{{ $active ? 'page' : 'false' }}">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </nav>
                    </div>

                    <!-- Tab panels -->
                    @if($tab === 'categories')
                        <section class="rounded-lg bg-white shadow-sm border border-slate-200">
                            <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                                <h3 class="text-base font-semibold">Categories</h3>
                                <button class="inline-flex items-center gap-2 rounded-md h-9 px-3 bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700" wire:click="addRow('categories')"><span class="material-symbols-outlined text-base">add</span><span>Add</span></button>
                            </div>
                            <div class="p-4 overflow-x-auto">
                                <table class="min-w-full text-left">
                                    <thead>
                                        <tr class="bg-slate-50">
                                            <th class="px-3 py-2 text-xs font-semibold text-slate-600 uppercase">Value</th>
                                            <th class="px-3 py-2 text-xs font-semibold text-slate-600 uppercase text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200">
                                        @forelse($categories as $i => $row)
                                            <tr>
                                                <td class="px-3 py-2"><input type="text" class="form-input w-full rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.defer="categories.{{ $i }}.value"></td>
                                                <td class="px-3 py-2 text-right space-x-2">
                                                    <button class="inline-flex items-center gap-2 rounded-md h-9 px-3 bg-sky-600 text-white text-xs font-semibold hover:bg-sky-700" wire:click="saveRow('categories', {{ $i }})"><span class="material-symbols-outlined text-base">save</span><span>Save</span></button>
                                                    <button class="inline-flex items-center gap-2 rounded-md h-9 px-3 bg-red-600 text-white text-xs font-semibold hover:bg-red-700" wire:click="requestDelete('categories', {{ $i }})"><span class="material-symbols-outlined text-base">delete</span><span>Delete</span></button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="2" class="px-3 py-6 text-center text-sm text-slate-500">No items yet.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    @endif

                    @if($tab === 'settings')
                        <section class="rounded-lg bg-white shadow-sm border border-slate-200">
                            <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                                <h3 class="text-base font-semibold">Settings</h3>
                            </div>
                            <div class="p-4 space-y-4">
                                <div class="max-w-md">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Tax rate (%)</label>
                                    <div class="flex gap-2">
                                        <input type="number" step="0.01" min="0" max="100" class="form-input w-full rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.defer="taxRate" placeholder="e.g. 8 or 10.5">
                                        <button class="inline-flex items-center gap-2 rounded-md h-9 px-3 bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700" wire:click="saveTaxRate"><span class="material-symbols-outlined text-base">save</span><span>Save</span></button>
                                    </div>
                                    <p class="mt-1 text-xs text-slate-500">Enter the tax rate as a percentage. Example: 8 for 8%.</p>
                                </div>
                            </div>
                        </section>
                    @endif

                    @if($tab === 'transmissions')
                        <section class="rounded-lg bg-white shadow-sm border border-slate-200">
                            <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                                <h3 class="text-base font-semibold">Transmissions</h3>
                                <button class="inline-flex items-center gap-2 rounded-md h-9 px-3 bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700" wire:click="addRow('transmissions')"><span class="material-symbols-outlined text-base">add</span><span>Add</span></button>
                            </div>
                            <div class="p-4 overflow-x-auto">
                                <table class="min-w-full text-left">
                                    <thead>
                                        <tr class="bg-slate-50">
                                            <th class="px-3 py-2 text-xs font-semibold text-slate-600 uppercase">Value</th>
                                            <th class="px-3 py-2 text-xs font-semibold text-slate-600 uppercase text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200">
                                        @forelse($transmissions as $i => $row)
                                            <tr>
                                                <td class="px-3 py-2"><input type="text" class="form-input w-full rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.defer="transmissions.{{ $i }}.value"></td>
                                                <td class="px-3 py-2 text-right space-x-2">
                                                    <button class="inline-flex items-center gap-2 rounded-md h-9 px-3 bg-sky-600 text-white text-xs font-semibold hover:bg-sky-700" wire:click="saveRow('transmissions', {{ $i }})"><span class="material-symbols-outlined text-base">save</span><span>Save</span></button>
                                                    <button class="inline-flex items-center gap-2 rounded-md h-9 px-3 bg-red-600 text-white text-xs font-semibold hover:bg-red-700" wire:click="requestDelete('transmissions', {{ $i }})"><span class="material-symbols-outlined text-base">delete</span><span>Delete</span></button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="2" class="px-3 py-6 text-center text-sm text-slate-500">No items yet.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    @endif

                    @if($tab === 'locations')
                        <section class="rounded-lg bg-white shadow-sm border border-slate-200">
                            <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                                <h3 class="text-base font-semibold">Locations</h3>
                                <button class="inline-flex items-center gap-2 rounded-md h-9 px-3 bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700" wire:click="addRow('locations')"><span class="material-symbols-outlined text-base">add</span><span>Add</span></button>
                            </div>
                            <div class="p-4 overflow-x-auto">
                                <table class="min-w-full text-left">
                                    <thead>
                                        <tr class="bg-slate-50">
                                            <th class="px-3 py-2 text-xs font-semibold text-slate-600 uppercase">Value</th>
                                            <th class="px-3 py-2 text-xs font-semibold text-slate-600 uppercase text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200">
                                        @forelse($locations as $i => $row)
                                            <tr>
                                                <td class="px-3 py-2"><input type="text" class="form-input w-full rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.defer="locations.{{ $i }}.value"></td>
                                                <td class="px-3 py-2 text-right space-x-2">
                                                    <button class="inline-flex items-center gap-2 rounded-md h-9 px-3 bg-sky-600 text-white text-xs font-semibold hover:bg-sky-700" wire:click="saveRow('locations', {{ $i }})"><span class="material-symbols-outlined text-base">save</span><span>Save</span></button>
                                                    <button class="inline-flex items-center gap-2 rounded-md h-9 px-3 bg-red-600 text-white text-xs font-semibold hover:bg-red-700" wire:click="requestDelete('locations', {{ $i }})"><span class="material-symbols-outlined text-base">delete</span><span>Delete</span></button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="2" class="px-3 py-6 text-center text-sm text-slate-500">No items yet.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    @endif

                    @if($tab === 'fuels')
                        <section class="rounded-lg bg-white shadow-sm border border-slate-200">
                            <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                                <h3 class="text-base font-semibold">Fuels</h3>
                                <button class="inline-flex items-center gap-2 rounded-md h-9 px-3 bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700" wire:click="addRow('fuels')"><span class="material-symbols-outlined text-base">add</span><span>Add</span></button>
                            </div>
                            <div class="p-4 overflow-x-auto">
                                <table class="min-w-full text-left">
                                    <thead>
                                        <tr class="bg-slate-50">
                                            <th class="px-3 py-2 text-xs font-semibold text-slate-600 uppercase">Value</th>
                                            <th class="px-3 py-2 text-xs font-semibold text-slate-600 uppercase text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200">
                                        @forelse($fuels as $i => $row)
                                            <tr>
                                                <td class="px-3 py-2"><input type="text" class="form-input w-full rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.defer="fuels.{{ $i }}.value"></td>
                                                <td class="px-3 py-2 text-right space-x-2">
                                                    <button class="inline-flex items-center gap-2 rounded-md h-9 px-3 bg-sky-600 text-white text-xs font-semibold hover:bg-sky-700" wire:click="saveRow('fuels', {{ $i }})"><span class="material-symbols-outlined text-base">save</span><span>Save</span></button>
                                                    <button class="inline-flex items-center gap-2 rounded-md h-9 px-3 bg-red-600 text-white text-xs font-semibold hover:bg-red-700" wire:click="requestDelete('fuels', {{ $i }})"><span class="material-symbols-outlined text-base">delete</span><span>Delete</span></button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="2" class="px-3 py-6 text-center text-sm text-slate-500">No items yet.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    @endif

                    @if($tab === 'extras')
                        <section class="rounded-lg bg-white shadow-sm border border-slate-200">
                            <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                                <h3 class="text-base font-semibold">Extras</h3>
                                <button class="inline-flex items-center gap-2 rounded-md h-9 px-3 bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700" wire:click="addRow('extras')"><span class="material-symbols-outlined text-base">add</span><span>Add</span></button>
                            </div>
                            <div class="p-4 overflow-x-auto">
                                <table class="min-w-full text-left">
                                    <thead>
                                        <tr class="bg-slate-50">
                                            <th class="px-3 py-2 text-xs font-semibold text-slate-600 uppercase">Name</th>
                                            <th class="px-3 py-2 text-xs font-semibold text-slate-600 uppercase">Price / day</th>
                                            <th class="px-3 py-2 text-xs font-semibold text-slate-600 uppercase">Active</th>
                                            <th class="px-3 py-2 text-xs font-semibold text-slate-600 uppercase">Default</th>
                                            <th class="px-3 py-2 text-xs font-semibold text-slate-600 uppercase text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200">
                                        @forelse($extras as $i => $row)
                                            <tr>
                                                <td class="px-3 py-2"><input type="text" class="form-input w-full rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.defer="extras.{{ $i }}.name"></td>
                                                <td class="px-3 py-2">
                                                    <div class="flex items-center"><span class="mr-1">₦</span><input type="number" min="0" step="0.01" class="form-input w-32 rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.defer="extras.{{ $i }}.price_per_day"></div>
                                                </td>
                                                <td class="px-3 py-2">
                                                    <input type="checkbox" class="rounded border-slate-300 text-sky-600 focus:ring-sky-600" wire:model.defer="extras.{{ $i }}.is_active">
                                                </td>
                                                <td class="px-3 py-2">
                                                    <input type="checkbox" class="rounded border-slate-300 text-sky-600 focus:ring-sky-600" wire:model.defer="extras.{{ $i }}.default_selected">
                                                </td>
                                                <td class="px-3 py-2 text-right space-x-2">
                                                    <button class="inline-flex items-center gap-2 rounded-md h-9 px-3 bg-sky-600 text-white text-xs font-semibold hover:bg-sky-700" wire:click="saveRow('extras', {{ $i }})"><span class="material-symbols-outlined text-base">save</span><span>Save</span></button>
                                                    <button class="inline-flex items-center gap-2 rounded-md h-9 px-3 bg-red-600 text-white text-xs font-semibold hover:bg-red-700" wire:click="requestDelete('extras', {{ $i }})"><span class="material-symbols-outlined text-base">delete</span><span>Delete</span></button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5" class="px-3 py-6 text-center text-sm text-slate-500">No extras yet.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    @endif

                    @if($tab === 'serviceTypes')
                        <section class="rounded-lg bg-white shadow-sm border border-slate-200">
                            <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                                <h3 class="text-base font-semibold">Service Types</h3>
                                <button class="inline-flex items-center gap-2 rounded-md h-9 px-3 bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700" wire:click="addRow('serviceTypes')"><span class="material-symbols-outlined text-base">add</span><span>Add</span></button>
                            </div>
                            <div class="p-4 overflow-x-auto">
                                <table class="min-w-full text-left">
                                    <thead>
                                        <tr class="bg-slate-50">
                                            <th class="px-3 py-2 text-xs font-semibold text-slate-600 uppercase">Name</th>
                                            <th class="px-3 py-2 text-xs font-semibold text-slate-600 uppercase">Pricing Type</th>
                                            <th class="px-3 py-2 text-xs font-semibold text-slate-600 uppercase">Active</th>
                                            <th class="px-3 py-2 text-xs font-semibold text-slate-600 uppercase text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200">
                                        @forelse($serviceTypes as $i => $row)
                                            <tr>
                                                <td class="px-3 py-2"><input type="text" class="form-input w-full rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.defer="serviceTypes.{{ $i }}.name"></td>
                                                <td class="px-3 py-2">
                                                    <select class="form-select w-40 rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.defer="serviceTypes.{{ $i }}.pricing_type">
                                                        <option value="fixed">Fixed</option>
                                                        <option value="negotiable">Negotiable</option>
                                                    </select>
                                                </td>
                                                <td class="px-3 py-2">
                                                    <input type="checkbox" class="rounded border-slate-300 text-sky-600 focus:ring-sky-600" wire:model.defer="serviceTypes.{{ $i }}.is_active">
                                                </td>
                                                <td class="px-3 py-2 text-right space-x-2">
                                                    <button class="inline-flex items-center gap-2 rounded-md h-9 px-3 bg-sky-600 text-white text-xs font-semibold hover:bg-sky-700" wire:click="saveRow('serviceTypes', {{ $i }})"><span class="material-symbols-outlined text-base">save</span><span>Save</span></button>
                                                    <button class="inline-flex items-center gap-2 rounded-md h-9 px-3 bg-red-600 text-white text-xs font-semibold hover:bg-red-700" wire:click="requestDelete('serviceTypes', {{ $i }})"><span class="material-symbols-outlined text-base">delete</span><span>Delete</span></button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="px-3 py-6 text-center text-sm text-slate-500">No service types yet.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    @endif
                </main>
            </div>
        </div>
    </div>

    @if($deleteConfirmOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50" wire:click="closeDeleteConfirm"></div>
            <div class="relative z-10 w-full max-w-md rounded-lg bg-white shadow-xl border border-slate-200">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-900">Confirm deletion</h3>
                    <button class="p-1 text-slate-500 hover:text-slate-700" wire:click="closeDeleteConfirm">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <div class="px-5 py-4 text-sm text-slate-700">
                    @php
                        $typeLabels = [
                            'categories' => 'this category',
                            'transmissions' => 'this transmission',
                            'fuels' => 'this fuel type',
                            'locations' => 'this location',
                            'extras' => 'this extra',
                            'serviceTypes' => 'this service type',
                        ];
                        $label = $typeLabels[$deleteType ?? 'categories'] ?? 'this item';
                    @endphp
                    <p>Are you sure you want to delete {{ $label }}? This action cannot be undone.</p>
                </div>
                <div class="px-5 py-4 border-t border-slate-200 flex items-center justify-end gap-2">
                    <button class="rounded-md h-10 px-4 border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50" wire:click="closeDeleteConfirm">Cancel</button>
                    <button class="rounded-md h-10 px-4 bg-red-600 text-white text-sm font-semibold hover:bg-red-700" wire:click="confirmDelete">Confirm delete</button>
                </div>
            </div>
        </div>
    @endif
</div>
