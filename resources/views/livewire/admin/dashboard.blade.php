<div>
    <div class="relative flex min-h-screen w-full flex-col overflow-x-hidden bg-slate-50 text-slate-900" style='font-family: "Work Sans", sans-serif;'>
        <div class="layout-container flex h-full grow flex-col">
            <header class="sticky top-0 z-10 flex items-center justify-between whitespace-nowrap border-b border-slate-200 bg-white/80 px-4 py-3 backdrop-blur-sm sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <svg class="h-8 w-8 text-sky-600" fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.8284 24L24 10.8284L37.1716 24L24 37.1716L10.8284 24Z" stroke="currentColor" stroke-linejoin="round" stroke-width="4"></path>
                        <path d="M4 24H44" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="4"></path>
                    </svg>
                    <h1 class="text-xl font-bold tracking-tight">{{ config('app.name') }}</h1>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-slate-600 hidden sm:block">{{ auth()->user()->name ?? 'Admin' }}</span>
                    <button class="flex h-10 w-10 items-center justify-center rounded-full text-slate-500 hover:bg-slate-100 hover:text-slate-700" title="Notifications">
                        <span class="material-symbols-outlined"> notifications </span>
                    </button>
                    <div class="h-10 w-10 overflow-hidden rounded-full bg-slate-200 flex items-center justify-center text-slate-700 font-bold">
                        {{ auth()->user()?->initials() }}
                    </div>
                </div>
            </header>

            <div class="flex flex-1">
                @include('admin.partials.sidebar', ['active' => 'dashboard'])

                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold tracking-tight">Admin Dashboard</h2>
                        <p class="mt-1 text-slate-500">Welcome back{{ auth()->user() ? ", ".e(auth()->user()->name) : '' }}. Here's a concise overview of your platform.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                        <div class="lg:col-span-2">
                            <div class="space-y-6">
                                <div>
                                    <h3 class="text-lg font-semibold leading-6">Key Metrics</h3>
                                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                                        <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                                            <p class="text-sm font-medium text-slate-500">Total Bookings</p>
                                            <p class="mt-1 text-3xl font-bold">{{ number_format($metrics['totalBookings'] ?? 0) }}</p>
                                        </div>
                                        <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                                            <p class="text-sm font-medium text-slate-500">Active Cars</p>
                                            <p class="mt-1 text-3xl font-bold">{{ number_format($metrics['totalCars'] ?? 0) }}</p>
                                        </div>
                                        <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                                            <p class="text-sm font-medium text-slate-500">Customers</p>
                                            <p class="mt-1 text-3xl font-bold">{{ number_format($metrics['totalCustomers'] ?? 0) }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h3 class="text-lg font-semibold leading-6">Recent Bookings</h3>
                                    <div class="mt-4 overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
                                        <table class="min-w-full divide-y divide-slate-200">
                                            <thead class="bg-slate-50">
                                                <tr>
                                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Customer</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Car</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Duration</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Status</th>
                                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-slate-500">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-200 bg-white">
                                                @forelse($recent as $b)
                                                @php
                                                    $st = strtolower((string)($b->status ?? ''));
                                                    $badge = 'bg-green-100 text-green-800';
                                                    if ($st === 'pending') $badge = 'bg-yellow-100 text-yellow-800';
                                                    if ($st === 'cancelled') $badge = 'bg-red-100 text-red-800';
                                                    $durationText = '';
                                                    try {
                                                        $sd = optional($b->start_date)->format('M d');
                                                        $ed = optional($b->end_date)->format('M d');
                                                        if ($sd && $ed) { $durationText = $sd.' - '.$ed; }
                                                    } catch (\Throwable $e) { $durationText = ''; }
                                                @endphp
                                                <tr>
                                                    <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-slate-900">{{ $b->user->name ?? '—' }}</td>
                                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-600">{{ $b->car->name ?? '—' }}</td>
                                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-600">{{ $durationText }}</td>
                                                    <td class="whitespace-nowrap px-4 py-3 text-sm"><span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 {{ $badge }}">{{ ucfirst($st) }}</span></td>
                                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-right text-slate-900">₦{{ number_format((float)($b->total ?? 0), 2) }}</td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="5" class="px-4 py-6 text-center text-sm text-slate-500">No bookings yet.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-1">
                            <div class="space-y-6">
                                <div>
                                    <h3 class="text-lg font-semibold leading-6">Quick Actions</h3>
                                    <div class="mt-4 flex flex-col space-y-4">
                                        <a class="flex items-center gap-4 rounded-lg border border-slate-200 bg-white p-4 shadow-sm transition-all hover:shadow-md" href="#">
                                            <span class="material-symbols-outlined text-2xl text-sky-600">book_online</span>
                                            <span class="font-medium">Manage Bookings</span>
                                        </a>
                                        <a class="flex items-center gap-4 rounded-lg border border-slate-200 bg-white p-4 shadow-sm transition-all hover:shadow-md" href="#">
                                            <span class="material-symbols-outlined text-2xl text-sky-600">directions_car</span>
                                            <span class="font-medium">Manage Cars</span>
                                        </a>
                                        <a class="flex items-center gap-4 rounded-lg border border-slate-200 bg-white p-4 shadow-sm transition-all hover:shadow-md" href="#">
                                            <span class="material-symbols-outlined text-2xl text-sky-600">group</span>
                                            <span class="font-medium">Manage Customers</span>
                                        </a>
                                        <a class="flex items-center gap-4 rounded-lg border border-slate-200 bg-white p-4 shadow-sm transition-all hover:shadow-md" href="#">
                                            <span class="material-symbols-outlined text-2xl text-sky-600">assessment</span>
                                            <span class="font-medium">View Reports</span>
                                        </a>
                                    </div>
                                </div>

                                <div>
                                    <h3 class="text-lg font-semibold leading-6">Car Availability</h3>
                                    <div class="mt-4 rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                                        <div class="flex items-center justify-between">
                                            <p class="font-medium">Available Cars</p>
                                            <p class="text-2xl font-bold">{{ number_format($availability['available'] ?? 0) }}
                                                <span class="text-base font-normal text-slate-500">/
                                                    {{ number_format($availability['total'] ?? 0) }}</span></p>
                                        </div>
                                        <div class="mt-2 h-2 w-full rounded-full bg-slate-200" aria-label="availability">
                                            <div class="h-2 rounded-full bg-sky-600" style="width: {{ (int)($availability['percent'] ?? 0) }}%;"></div>
                                        </div>
                                        <p class="mt-2 text-sm text-slate-500">{{ (int)($availability['percent'] ?? 0) }}% of cars are currently available.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
</div>
