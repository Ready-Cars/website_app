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
                @include('admin.partials.sidebar', ['active' => 'bookings'])

                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    @include('admin.partials.breadcrumbs', ['items' => [
                        ['label' => 'Dashboard', 'url' => route('dashboard')],
                        ['label' => 'Bookings', 'url' => null],
                    ]])
                    <div class="mb-6">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h2 class="text-3xl font-bold tracking-tight">
                                    @php
                                        $selectedCar = null; if(!empty($carId)) { $selectedCar = \App\Models\Car::find($carId); }
                                        $customerCtx = null;
                                        $rawQ = isset($q) ? trim((string)$q) : (property_exists($this,'q') ? trim((string)$this->q) : '');
                                        if (!empty($rawQ)) {
                                            if (ctype_digit($rawQ)) {
                                                $customerCtx = \App\Models\User::find((int)$rawQ);
                                            } elseif (filter_var($rawQ, FILTER_VALIDATE_EMAIL)) {
                                                $customerCtx = \App\Models\User::where('email', $rawQ)->first();
                                            }
                                        }
                                    @endphp
                                    Manage Bookings{!! $selectedCar ? ' — <span class="text-slate-700 text-2xl">Car: '.e($selectedCar->name).'</span>' : '' !!}
                                    {!! $customerCtx ? ' — <span class="text-slate-700 text-2xl">Customer: '.e($customerCtx->name).' <span class="text-slate-500 text-xl">('.e($customerCtx->email).')</span></span>' : '' !!}
                                </h2>
                                <p class="mt-1 text-slate-500">View, filter, and manage customer bookings.</p>
                            </div>
                            <div class="pt-1">
                                <button type="button" class="inline-flex items-center gap-2 rounded-md h-10 px-4 bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800" wire:click="openSettings">
                                    <span class="material-symbols-outlined text-base">settings</span>
                                    <span>Settings</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="mb-4 rounded-md border border-green-300 bg-green-50 px-4 py-3 text-green-800">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 rounded-md border border-red-300 bg-red-50 px-4 py-3 text-red-800">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Filters -->
                    <div x-data="{ showAdvanced : false }" class="rounded-lg bg-white shadow-sm border border-slate-200 p-4 mb-4">
                        <!-- Primary search row -->
                        <div class="flex flex-col md:flex-row gap-3 items-stretch md:items-center">
                            <div class="relative md:flex-1">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                                <input type="search" placeholder="Search by booking #, customer, car, location or status" class="form-input w-full pl-10 pr-3 py-2.5 rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.live.debounce.400ms="q">
                            </div>
                            <div class="flex gap-2">
                                <button type="button" class="rounded-md border border-slate-300 px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50" wire:click="resetFilters">Reset</button>
                                <button type="button" class="inline-flex items-center gap-2 rounded-md px-3 py-2.5 text-sm font-medium border border-slate-300 text-slate-700 hover:bg-slate-50" x-on:click="showAdvanced = !showAdvanced" x-bind:aria-expanded="showAdvanced">
                                    <span class="material-symbols-outlined text-base" x-text="showAdvanced ? 'expand_less' : 'tune'"></span>
                                    <span x-text="showAdvanced ? 'Hide advanced' : 'Advanced filters'"></span>
                                </button>
                            </div>
                        </div>


                        <!-- Advanced grid -->
                        <div x-show="showAdvanced" class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Status</label>
                                <select class="form-select w-full px-3 py-2.5 rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.live="status">
                                    <option value="">All Statuses</option>
                                    @foreach(($options['statuses'] ?? []) as $s)
                                        <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Car</label>
                                <select class="form-select w-full px-3 py-2.5 rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.live="carId">
                                    <option value="">All Cars</option>
                                    @foreach(($options['cars'] ?? []) as $car)
                                        <option value="{{ $car->id }}">{{ $car->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Category</label>
                                <select class="form-select w-full px-3 py-2.5 rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.live="category">
                                    <option value="">All Categories</option>
                                    @foreach(($options['categories'] ?? []) as $cat)
                                        <option value="{{ $cat }}">{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">From</label>
                                <input type="date" class="form-input w-full px-3 py-2.5 rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.live="from">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">To</label>
                                <input type="date" class="form-input w-full px-3 py-2.5 rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.live="to">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Per page</label>
                                <select class="form-select w-full px-3 py-2.5 rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.live="perPage">
                                    @foreach(($options['perPages'] ?? [10,25,50,100]) as $p)
                                        <option value="{{ $p }}">{{ $p }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>

                    <!-- Mobile list (cards) -->
                    <div class="md:hidden space-y-3">
                        @forelse($bookings as $b)
                            @php
                                $st = strtolower((string)($b->status ?? ''));
                                $badge = 'bg-green-100 text-green-800';
                                if ($st === 'pending') $badge = 'bg-yellow-100 text-yellow-800';
                                if ($st === 'pending payment') $badge = 'bg-yellow-100 text-yellow-800';
                                if ($st === 'cancelled') $badge = 'bg-red-100 text-red-800';
                                if ($st === 'completed') $badge = 'bg-blue-100 text-blue-800';
                                $dates = '';
                                try {
                                    $sd = optional($b->start_date)->format('M d, Y');
                                    $ed = optional($b->end_date)->format('M d, Y');
                                    if ($sd && $ed) $dates = $sd.' — '.$ed;
                                } catch (\Throwable $e) { $dates=''; }
                            @endphp
                            <div class="rounded-lg border border-slate-200 bg-white shadow-sm p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="text-sm font-semibold text-slate-900">#{{ $b->id }} • {{ $b->user->name ?? '—' }}</div>
                                        <div class="text-sm text-slate-600">{{ $b->car->name ?? '—' }}</div>
                                        <div class="mt-1 text-xs text-slate-500">{{ $dates }}</div>
                                        <div class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">{{ ucfirst($st) }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-bold text-slate-900">₦{{ number_format((float)($b->total ?? 0), 2) }}</div>
                                        <div class="mt-2 relative inline-block text-left" data-dropdown>
                                            <button type="button" class="inline-flex items-center gap-1.5 rounded-md h-9 px-3 border border-slate-300 text-slate-700 font-medium hover:bg-slate-50" data-dropdown-button aria-haspopup="true" aria-expanded="false">
                                                <span class="material-symbols-outlined text-base">more_vert</span>
                                                <span>Actions</span>
                                            </button>
                                            <div class="absolute right-0 mt-2 w-44 origin-top-right rounded-md border border-slate-200 bg-white shadow-lg z-30 hidden" data-dropdown-menu>
                                                <div class="py-1 text-sm flex flex-col items-stretch">
                                                    <button class="w-full text-left px-3 py-2 hover:bg-slate-50" wire:click="view({{ $b->id }})">View</button>
                                                    @if($st === 'pending')
                                                        <button class="w-full text-left px-3 py-2 hover:bg-slate-50" wire:click="confirm({{ $b->id }})">Confirm</button>
                                                    @endif
                                                    @if($st === 'confirmed')
                                                        <button class="w-full text-left px-3 py-2 hover:bg-slate-50" wire:click="openComplete({{ $b->id }})">Complete</button>
                                                    @endif
                                                    @if($st !== 'cancelled' && $st !== 'completed')
                                                        <button class="w-full text-left px-3 py-2 text-red-600 hover:bg-red-50" wire:click="openCancel({{ $b->id }})">Cancel</button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-md border border-slate-200 bg-white p-6 text-center text-sm text-slate-500">No bookings found.</div>
                        @endforelse
                    </div>

                    <!-- Desktop table -->
                    <div style="min-height: 250px" class="hidden md:block overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
                        <table class="min-w-full text-left">
                            <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">ID</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Customer</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Car</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Dates</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider text-center">Status</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider text-right">Total</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider text-right">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                            @forelse($bookings as $b)
                                @php
                                    $st = strtolower((string)($b->status ?? ''));
                                    $badge = 'bg-green-100 text-green-800';
                                    if ($st === 'pending') $badge = 'bg-yellow-100 text-yellow-800';
                                    if ($st === 'cancelled') $badge = 'bg-red-100 text-red-800';
                                    if ($st === 'completed') $badge = 'bg-blue-100 text-blue-800';
                                    $dates = '';
                                    try {
                                        $sd = optional($b->start_date)->format('M d, Y');
                                        $ed = optional($b->end_date)->format('M d, Y');
                                        if ($sd && $ed) $dates = $sd.' — '.$ed;
                                    } catch (\Throwable $e) { $dates=''; }
                                @endphp
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-slate-900">#{{ $b->id }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-700">{{ $b->user->name ?? '—' }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-600">{{ $b->car->name ?? '—' }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-600">{{ $dates }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">{{ ucfirst($st) }}</span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-slate-900">₦{{ number_format((float)($b->total ?? 0), 2) }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-right">
                                        <div class="relative inline-block text-left" data-dropdown>
                                            <button type="button" class="inline-flex items-center gap-2 rounded-md h-9 px-3 border border-slate-300 text-slate-700 font-medium hover:bg-slate-50" data-dropdown-button aria-haspopup="true" aria-expanded="false">
                                                <span class="material-symbols-outlined text-base">more_vert</span>
                                                <span>Actions</span>
                                            </button>
                                            <div class="absolute right-0 mt-2 w-44 origin-top-right rounded-md border border-slate-200 bg-white shadow-lg z-30 hidden" data-dropdown-menu>
                                                <div class="py-1 text-sm flex flex-col items-stretch">
                                                    <button class="w-full text-left px-3 py-2 hover:bg-slate-50" wire:click="view({{ $b->id }})">View</button>
                                                    @if($st === 'pending')
                                                        <button class="w-full text-left px-3 py-2 hover:bg-slate-50" wire:click="confirm({{ $b->id }})">Confirm</button>
                                                    @endif
                                                    @if($st === 'confirmed')
                                                        <button class="w-full text-left px-3 py-2 hover:bg-slate-50" wire:click="openComplete({{ $b->id }})">Complete</button>
                                                    @endif
                                                    @if($st !== 'cancelled' && $st !== 'completed')
                                                        <button class="w-full text-left px-3 py-2 text-red-600 hover:bg-red-50" wire:click="openCancel({{ $b->id }})">Cancel</button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-sm text-slate-500">No bookings found.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex items-center justify-between">
                        <div class="text-xs text-slate-500">
                            @if($bookings instanceof \Illuminate\Contracts\Pagination\Paginator)
                                Showing page {{ $bookings->currentPage() }} of {{ $bookings->lastPage() }}
                            @endif
                        </div>
                        <div>
                            {{ $bookings->onEachSide(1)->links() }}
                        </div>
                    </div>
                </main>
            </div>
        </div>

        <!-- View Booking Modal -->
        @if($selected)
            <div class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="absolute inset-0 bg-black/50" wire:click="closeView"></div>
                <div class="relative z-10 w-full max-w-2xl rounded-lg bg-white shadow-xl border border-slate-200">
                    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-slate-900">Booking #{{ $selected->id }}</h3>
                        <button class="p-1 text-slate-500 hover:text-slate-700" wire:click="closeView">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                    <div class="px-5 py-4 text-sm text-slate-700">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-3">
                                <div class="rounded-md border border-slate-200 overflow-hidden">
                                    <div class="w-full bg-center bg-no-repeat aspect-video bg-cover" style="background-image: url('{{ $selected->car->image_url ?? '' }}');"></div>
                                </div>
                                <div class="space-y-1">
                                    <div class="text-slate-500">Customer</div>
                                    <div class="font-medium text-slate-900">
                                        {{ $selected->user->name ?? '—' }}
                                        (<a href="mailto:{{ $selected->user->email }}"
                                            class="text-blue-600 hover:underline">{{ $selected->user->email }}</a>,
                                        <a href="https://wa.me/{{ $selected->user->phone }}" target="_blank"
                                           class="text-blue-600 hover:underline">{{ $selected->user->phone }}</a>)
                                    </div>
                                    <div class="text-slate-500 mt-3">Car</div>
                                    <div class="font-medium text-slate-900">{{ $selected->car->name ?? '—' }}</div>
                                    <div class="text-slate-500 mt-3">Status</div>
                                    <div class="font-medium text-slate-900">{{ ucfirst($selected->status ?? '') }}</div>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <div class="text-slate-500">Pickup → Dropoff</div>
                                <div class="font-medium text-slate-900">{{ $selected->pickup_location }} → {{ $selected->dropoff_location }}</div>
                                <div class="text-slate-500 mt-3">Dates</div>
                                <div class="font-medium text-slate-900">{{ optional($selected->start_date)->format('M d, Y') }} — {{ optional($selected->end_date)->format('M d, Y') }}</div>
                                <div class="text-slate-500 mt-3">Total</div>
                                <div class="font-extrabold text-slate-900">₦{{ number_format((float)($selected->total ?? 0), 2) }}</div>
                                @if($selected->payment_evidence)
                                    <div class="text-slate-500 mt-3">Payment Evidence</div>
                                    <div class="font-medium">
                                        <a href="{{ route('admin.bookings.payment-evidence.download', $selected) }}"
                                           class="inline-flex items-center gap-2 text-sky-600 hover:text-sky-800 hover:underline"
                                           target="_blank">
                                            <span class="material-symbols-outlined text-base">download</span>
                                            Download Evidence
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @php $extras = (array)($selected->extras ?? []); @endphp
                        @if(!empty($extras))
                            <div class="mt-4">
                                <div class="text-slate-500 mb-1">Extras</div>
                                <ul class="list-disc pl-5 text-slate-700">
                                    @foreach($extras as $k => $v)
                                        @if($v)
                                            <li>{{ is_string($k) ? ucfirst(str_replace('_',' ',$k)) : (string)$k }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if(($selected->status === 'cancelled') && !empty($selected->cancellation_reason))
                            <div class="mt-4">
                                <div class="text-slate-500">Cancellation reason</div>
                                <div class="mt-1 rounded-md border border-slate-200 bg-slate-50 p-2 text-slate-700">{{ $selected->cancellation_reason }}</div>
                            </div>
                        @endif
                    </div>
                    <div class="px-5 py-4 border-t border-slate-200 flex items-center justify-end gap-2">
                        <button class="rounded-md h-10 px-4 border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50" wire:click="closeView">Close</button>
                        @if(($selected->status ?? '') === 'pending')
                            <button class="rounded-md h-10 px-4 bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700" wire:click="confirm({{ $selected->id }})">Confirm</button>
                        @endif
                        @if(($selected->status ?? '') === 'confirmed')
                            <button class="rounded-md h-10 px-4 bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700" wire:click="openComplete({{ $selected->id }})">Complete</button>
                        @endif
                        @if(($selected->status ?? '') !== 'cancelled' && ($selected->status ?? '') !== 'completed')
                            <button class="rounded-md h-10 px-4 bg-red-600 text-white text-sm font-semibold hover:bg-red-700" wire:click="openCancel({{ $selected->id }})">Cancel</button>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Cancel Modal -->
        @if($cancelOpen && $viewingId)
            <div class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="absolute inset-0 bg-black/50" wire:click="$set('cancelOpen', false)"></div>
                <div class="relative z-10 w-full max-w-md rounded-lg bg-white shadow-xl border border-slate-200">
                    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-slate-900">Cancel booking</h3>
                        <button class="p-1 text-slate-500 hover:text-slate-700" wire:click="$set('cancelOpen', false)">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                    <div class="px-5 py-4 text-sm text-slate-700">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Reason (optional)</label>
                        <textarea rows="3" class="form-textarea w-full rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" placeholder="Why is this booking being cancelled?" wire:model.defer="cancelReason"></textarea>
                        <p class="text-xs text-slate-500 mt-1">Note: If any amount was charged, it will be refunded to the customer's wallet.</p>
                    </div>
                    <div class="px-5 py-4 border-t border-slate-200 flex items-center justify-end gap-2">
                        <button class="rounded-md h-10 px-4 border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50" wire:click="$set('cancelOpen', false)">Close</button>
                        <button class="rounded-md h-10 px-4 bg-red-600 text-white text-sm font-semibold hover:bg-red-700" wire:click="cancel">Confirm cancel</button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Confirm with Price Modal -->
        @if($confirmPriceOpen && $viewingId)
            <div class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="absolute inset-0 bg-black/50" wire:click="$set('confirmPriceOpen', false)"></div>
                <div class="relative z-10 w-full max-w-md rounded-lg bg-white shadow-xl border border-slate-200">
                    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-slate-900">Confirm booking and set price</h3>
                        <button class="p-1 text-slate-500 hover:text-slate-700" wire:click="$set('confirmPriceOpen', false)">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                    <div class="px-5 py-4 text-sm text-slate-700">

                        @if (session('success'))
                            <div class="mb-4 rounded-md border border-green-300 bg-green-50 px-4 py-3 text-green-800">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="mb-4 rounded-md border border-red-300 bg-red-50 px-4 py-3 text-red-800">
                                {{ session('error') }}
                            </div>
                        @endif
                        @php $bk = \App\Models\Booking::with(['user','car'])->find($viewingId); @endphp
                        @if($bk)
                            <div class="mb-3 rounded-md border border-slate-200 bg-slate-50 p-3">
                                <div class="font-medium text-slate-900">#{{ $bk->id }} • {{ $bk->user->name ?? '—' }}</div>
                                <div class="text-slate-600 text-sm">{{ $bk->car->name ?? '—' }}</div>
                                <div class="text-slate-600 text-sm">{{ optional($bk->start_date)->format('M d, Y') }} — {{ optional($bk->end_date)->format('M d, Y') }}</div>
                                <div class="text-slate-900 text-sm mt-1">Current total: ₦{{ number_format((float)($bk->total ?? 0), 2) }}</div>
                            </div>
                        @endif
                        <label class="block text-sm font-medium text-slate-700 mb-1">Final price (₦)</label>
                        <input type="number" min="0" step="0.01" inputmode="decimal" class="form-input w-full rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" placeholder="Enter amount" wire:model.defer="confirmPrice">
                        <p class="text-xs text-slate-500 mt-1">If customer has sufficient wallet balance, booking will be confirmed immediately. Otherwise, a payment link will be sent to their email.</p>
                    </div>
                    <div class="px-5 py-4 border-t border-slate-200 flex items-center justify-end gap-2">
                        <button class="rounded-md h-10 px-4 border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50" wire:click="$set('confirmPriceOpen', false)">Close</button>
                        <button class="rounded-md h-10 px-4 bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700" wire:click="confirmPendingWithPrice">Confirm & Charge</button>
                    </div>
                </div>
            </div>
        @endif


        <!-- Success Message Modal -->

        <!-- Complete Modal -->
        @if($completeOpen && $viewingId)
            <div class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="absolute inset-0 bg-black/50" wire:click="$set('completeOpen', false)"></div>
                <div class="relative z-10 w-full max-w-md rounded-lg bg-white shadow-xl border border-slate-200">
                    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-slate-900">Mark as completed</h3>
                        <button class="p-1 text-slate-500 hover:text-slate-700" wire:click="$set('completeOpen', false)">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                    <div class="px-5 py-4 text-sm text-slate-700">
                        @php $bk = \App\Models\Booking::with(['user','car'])->find($viewingId); @endphp
                        <p>You're about to mark this booking as <strong>completed</strong>.</p>
                        @if($bk)
                            <div class="mt-3 rounded-md border border-slate-200 bg-slate-50 p-3">
                                <div class="font-medium text-slate-900">#{{ $bk->id }} • {{ $bk->user->name ?? '—' }}</div>
                                <div class="text-slate-600 text-sm">{{ $bk->car->name ?? '—' }}</div>
                                <div class="text-slate-600 text-sm">{{ optional($bk->start_date)->format('M d, Y') }} — {{ optional($bk->end_date)->format('M d, Y') }}</div>
                                <div class="text-slate-900 font-semibold mt-1">₦{{ number_format((float)($bk->total ?? 0), 2) }}</div>
                            </div>
                        @endif
                        <p class="text-xs text-slate-500 mt-3">This action signifies the trip has been completed. It does not alter wallet balances.</p>
                    </div>
                    <div class="px-5 py-4 border-t border-slate-200 flex items-center justify-end gap-2">
                        <button class="rounded-md h-10 px-4 border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50" wire:click="$set('completeOpen', false)">Close</button>
                        <button class="rounded-md h-10 px-4 bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700" wire:click="completeSelected">Confirm complete</button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Settings Modal -->
        @if($settingsOpen)
            <div class="fixed inset-0 z-[60] flex items-center justify-center">
                <div class="absolute inset-0 bg-black/50" wire:click="closeSettings"></div>
                <div class="relative z-10 w-full max-w-3xl rounded-lg bg-white shadow-xl border border-slate-200">
                    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-slate-700">settings</span>
                            <h3 class="text-lg font-semibold text-slate-900">Booking settings</h3>
                        </div>
                        <button class="p-1 text-slate-500 hover:text-slate-700" wire:click="closeSettings">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                    <div class="px-5 py-4 text-sm text-slate-700 max-h-[80vh] overflow-y-auto">
                        <livewire:admin.settings />
                    </div>
                    <div class="px-5 py-4 border-t border-slate-200 flex items-center justify-end">
                        <button class="rounded-md h-10 px-4 border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50" wire:click="closeSettings">Close</button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>


<script>
// Accessible dropdowns for action menus
(function(){
  function initDropdown(root){
    if (!root || root.__ddInited) return;
    root.__ddInited = true;
    const btn = root.querySelector('[data-dropdown-button]');
    const menu = root.querySelector('[data-dropdown-menu]');
    if (!btn || !menu) return;

    function open(){ menu.classList.remove('hidden'); btn.setAttribute('aria-expanded','true'); }
    function close(){ menu.classList.add('hidden'); btn.setAttribute('aria-expanded','false'); }
    function toggle(){ (btn.getAttribute('aria-expanded') === 'true') ? close() : open(); }

    btn.addEventListener('click', function(e){ e.stopPropagation(); toggle(); });
    // close on outside click
    document.addEventListener('click', function(e){
      if (menu.classList.contains('hidden')) return;
      if (!root.contains(e.target)) close();
    });
    // close on escape
    document.addEventListener('keydown', function(e){ if (e.key === 'Escape') close(); });
    // close after any Livewire navigation
    window.addEventListener('livewire:navigated', close);
  }
  function initAll(){ document.querySelectorAll('[data-dropdown]').forEach(initDropdown); }

  // Initial bind
  if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', initAll); } else { initAll(); }
  // Re-init on Livewire SPA navigation
  window.addEventListener('livewire:navigated', initAll);

  // Re-init when Livewire updates the DOM (via MutationObserver)
  const mo = new MutationObserver((mutations)=>{
    let needsInit = false;
    for (const m of mutations){
      if (m.addedNodes && m.addedNodes.length){
        for (const n of m.addedNodes){
          if (!(n instanceof Element)) continue;
          if (n.matches && n.matches('[data-dropdown]')) { needsInit = true; break; }
          if (n.querySelector && n.querySelector('[data-dropdown]')) { needsInit = true; break; }
        }
      }
      if (needsInit) break;
    }
    if (needsInit) initAll();
  });
  try { mo.observe(document.body, { childList: true, subtree: true }); } catch (e) {}
})();
</script>
