<div>
    <div class="relative flex min-h-screen w-full flex-col overflow-x-hidden bg-slate-50 text-slate-900" style='font-family: "Work Sans", "Noto Sans", sans-serif;'>
        <div class="layout-container flex h-full grow flex-col">
            <header class="sticky top-0 z-10 flex items-center justify-between whitespace-nowrap border-b border-slate-200 bg-white/80 px-4 py-3 backdrop-blur-sm sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <button type="button" class="lg:hidden inline-flex h-10 w-10 items-center justify-center rounded-md text-slate-600 hover:bg-slate-100" aria-label="Open menu" data-admin-menu-open aria-controls="admin-mobile-drawer" aria-expanded="false">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                    <span class="material-symbols-outlined text-sky-600 text-3xl"> assessment </span>
                    <h1 class="text-xl font-bold tracking-tight">{{ config('app.name') }} — Admin Reports</h1>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-slate-600 hidden sm:block">{{ auth()->user()->name ?? 'Admin' }}</span>
                    @include('admin.partials.user-menu')
                </div>
            </header>

            <div class="flex flex-1">
                @include('admin.partials.sidebar', ['active' => 'reports'])

                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    @include('admin.partials.breadcrumbs', ['items' => [
                        ['label' => 'Dashboard', 'url' => route('dashboard')],
                        ['label' => 'Reports', 'url' => null],
                    ]])
                    <div class="mb-6">
                        <h2 class="text-3xl font-bold tracking-tight">Reporting & Analytics</h2>
                        <p class="mt-1 text-slate-500">Visualize booking trends, revenue, status distribution and top-performing cars.</p>
                    </div>

                    <!-- Filters -->
                    <style>
                        .no-scrollbar::-webkit-scrollbar { display: none; }
                        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
                    </style>
                    @php
                        $now = \Carbon\Carbon::now();
                        $vf = isset($from) ? (string)$from : (property_exists($this, 'from') ? (string)$this->from : '');
                        $vt = isset($to) ? (string)$to : (property_exists($this, 'to') ? (string)$this->to : '');
                        $isPreset = function(string $key) use ($now, $vf, $vt): bool {
                            $f = $vf; $t = $vt;
                            switch ($key) {
                                case 'last_7_days':
                                    return $f === $now->copy()->subDays(7)->toDateString() && $t === $now->toDateString();
                                case 'this_month':
                                    return $f === $now->copy()->startOfMonth()->toDateString() && $t === $now->toDateString();
                                case 'last_30_days':
                                    return $f === $now->copy()->subDays(30)->toDateString() && $t === $now->toDateString();
                                case 'this_year':
                                    return $f === $now->copy()->startOfYear()->toDateString() && $t === $now->toDateString();
                                case 'all_time':
                                    return empty($f) && empty($t);
                                default:
                                    return false;
                            }
                        };
                    @endphp
                    <div class="rounded-lg bg-white shadow-sm border border-slate-200 p-4 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
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
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-slate-600 mb-1">Date range</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <input type="date" class="form-input w-full px-3 py-2.5 rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.live="from" placeholder="From">
                                    <input type="date" class="form-input w-full px-3 py-2.5 rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.live="to" placeholder="To">
                                </div>
                            </div>
                            <div class="md:col-span-6">
                                <label class="block text-xs font-medium text-slate-600 mb-1">Quick ranges</label>
                                <div class="flex flex-wrap items-center gap-2">
                                    @php
                                        $presets = [
                                            ['key' => 'last_7_days', 'label' => 'Last 7 days'],
                                            ['key' => 'this_month', 'label' => 'This month'],
                                            ['key' => 'last_30_days', 'label' => 'Last 30 days'],
                                            ['key' => 'this_year', 'label' => 'This year'],
                                            ['key' => 'all_time', 'label' => 'All time'],
                                        ];
                                    @endphp
                                    @foreach($presets as $p)
                                        @php $active = $isPreset($p['key']); @endphp
                                        <button
                                            type="button"
                                            aria-pressed="{{ $active ? 'true' : 'false' }}"
                                            class="rounded-full px-3 py-2 text-sm font-medium border transition-colors {{ $active ? 'bg-sky-600 border-sky-600 text-white shadow' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100' }}"
                                            wire:click="quickRange('{{ $p['key'] }}')">
                                            {{ $p['label'] }}
                                        </button>
                                    @endforeach
                                    <span class="flex-1"></span>
                                    <button wire:click="exportCsv" type="button" class="inline-flex items-center gap-2 rounded-md h-10 px-3 bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700">
                                        <span class="material-symbols-outlined text-base"> download </span>
                                        <span class="hidden sm:inline">Export CSV</span>
                                        <span class="sm:hidden">CSV</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- KPIs -->
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
                        <div class="rounded-lg border border-slate-200 bg-white p-4">
                            <div class="text-xs text-slate-500">Total Bookings</div>
                            <div class="text-2xl font-bold mt-1">{{ number_format($totalBookings) }}</div>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-white p-4">
                            <div class="text-xs text-slate-500">Total Revenue</div>
                            <div class="text-2xl font-bold mt-1">₦{{ number_format($totalRevenue, 2) }}</div>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-white p-4">
                            <div class="text-xs text-slate-500">Date Range</div>
                            <div class="text-sm mt-1">{{ $from ?: '—' }} — {{ $to ?: '—' }}</div>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-white p-4">
                            <div class="text-xs text-slate-500">Filters</div>
                            <div class="text-sm mt-1">Status: {{ $status ?: 'Any' }} • Car: {{ $carId ? (\App\Models\Car::find($carId)->name ?? '—') : 'Any' }}</div>
                        </div>
                    </div>

                    <!-- Charts -->
                    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                        <div class="xl:col-span-2 rounded-lg border border-slate-200 bg-white p-4">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-slate-900">Bookings & Revenue (Bar)</h3>
                            </div>
                            <div style="height: 32rem;">
                            <livewire:livewire-column-chart
                                 key="{{ $lwColumn->reactiveKey() }}"
                                :column-chart-model="$lwColumn"
                            />
                            </div>
{{--                            <livewire:ui.chart-bar :labels="$labels" :series="$barSeries" :height="360" />--}}
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-white p-4">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-slate-900">Status Breakdown</h3>
                            </div>

                            <div style="height: 32rem;">
                            <livewire:livewire-pie-chart
                                key="{{ $lwPie->reactiveKey() }}"
                                :pie-chart-model="$lwPie"
                            />
                            </div>
                        </div>
                    </div>

                    <!-- Top Cars -->
                    <div class="mt-6 rounded-lg border border-slate-200 bg-white p-4">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-slate-900">Top Cars by Bookings</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left mt-3">
                                <thead>
                                <tr class="bg-slate-50 border-b border-slate-200">
                                    <th class="px-4 py-2 text-xs font-semibold text-slate-600 uppercase tracking-wider">Car</th>
                                    <th class="px-4 py-2 text-xs font-semibold text-slate-600 uppercase tracking-wider">Bookings</th>
                                    <th class="px-4 py-2 text-xs font-semibold text-slate-600 uppercase tracking-wider">Revenue</th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200">
                                @forelse($topCars as $row)
                                    <tr>
                                        <td class="px-4 py-2 text-sm text-slate-700">{{ $row['car'] }}</td>
                                        <td class="px-4 py-2 text-sm text-slate-900 font-semibold">{{ $row['count'] }}</td>
                                        <td class="px-4 py-2 text-sm text-slate-900 font-semibold">₦{{ number_format($row['revenue'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-4 text-center text-sm text-slate-500">No data for selected filters.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>

</div>
