<div>
    <div class="relative flex min-h-screen w-full flex-col overflow-x-hidden bg-slate-50 text-slate-900" style='font-family: "Work Sans", "Noto Sans", sans-serif;'>
        <div class="layout-container flex h-full grow flex-col">
            <header class="sticky top-0 z-10 flex items-center justify-between whitespace-nowrap border-b border-slate-200 bg-white/80 px-4 py-3 backdrop-blur-sm sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <button type="button" class="lg:hidden inline-flex h-10 w-10 items-center justify-center rounded-md text-slate-600 hover:bg-slate-100" aria-label="Open menu" data-admin-menu-open aria-controls="admin-mobile-drawer" aria-expanded="false">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                    <span class="material-symbols-outlined text-sky-600 text-3xl"> group </span>
                    <h1 class="text-xl font-bold tracking-tight">{{ config('app.name') }} — Admin</h1>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-slate-600 hidden sm:block">{{ auth()->user()->name ?? 'Admin' }}</span>
                    @include('admin.partials.user-menu')
                </div>
            </header>

            <div class="flex flex-1">
                @include('admin.partials.sidebar', ['active' => 'customers'])

                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    @include('admin.partials.breadcrumbs', ['items' => [
                        ['label' => 'Dashboard', 'url' => route('dashboard')],
                        ['label' => 'Customers', 'url' => null],
                    ]])
                    <div class="mb-6">
                        <h2 class="text-3xl font-bold tracking-tight">Customer Management</h2>
                        <p class="mt-1 text-slate-500">Search, review bookings, track spending, and ban/unban customers.</p>
                    </div>

                    @if (session('success'))
                        <div class="mb-4 rounded-md border border-green-300 bg-green-50 px-4 py-3 text-green-800">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Filters -->
                    <div class="rounded-lg bg-white shadow-sm border border-slate-200 p-4 mb-4">
                        <div class="flex flex-col md:flex-row gap-3 items-stretch md:items-center">
                            <div class="relative md:flex-1">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                                <input type="search" placeholder="Search by name, email or ID" class="form-input w-full pl-10 pr-3 py-2.5 rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.live.debounce.400ms="q">
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
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Status</label>
                                <select class="form-select w-full px-3 py-2.5 rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.live="status">
                                    <option value="">All</option>
                                    <option value="active">Active</option>
                                    <option value="banned">Banned</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Joined From</label>
                                <input type="date" class="form-input w-full px-3 py-2.5 rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.live="from">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Joined To</label>
                                <input type="date" class="form-input w-full px-3 py-2.5 rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.live="to">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Per page</label>
                                <select class="form-select w-full px-3 py-2.5 rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" wire:model.live="perPage">
                                    @foreach([10,25,50,100] as $p)
                                        <option value="{{ $p }}">{{ $p }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Desktop table -->
                    <div style="min-height: 250px" class="hidden md:block overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
                        <table class="min-w-full text-left">
                            <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">ID</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Customer</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Email</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Joined</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider text-right">Bookings</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider text-right">Total Spent</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider text-right">Wallet</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider text-center">Status</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider text-right">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                            @forelse($users as $u)
                                @php
                                    $isBanned = !empty($u->banned_at);
                                @endphp
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-slate-900">#{{ $u->id }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-700">{{ $u->name }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-600">{{ $u->email }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-600">{{ optional($u->created_at)->format('M d, Y') }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-slate-900">{{ number_format((int)($u->bookings_count ?? 0)) }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-slate-900">₦{{ number_format((float)($u->spent_sum ?? 0), 2) }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-slate-900">₦{{ number_format((float)($u->wallet_balance ?? 0), 2) }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        @if($isBanned)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Banned</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-right">
                                        <div class="relative inline-block text-left" data-dropdown>
                                            <button type="button" class="inline-flex items-center gap-2 rounded-md h-9 px-3 border border-slate-300 text-slate-700 font-medium hover:bg-slate-50" data-dropdown-button aria-haspopup="true" aria-expanded="false">
                                                <span class="material-symbols-outlined text-base">more_vert</span>
                                                <span>Actions</span>
                                            </button>
                                            <div class="absolute right-0 mt-2 w-48 origin-top-right rounded-md border border-slate-200 bg-white shadow-lg z-30 hidden" data-dropdown-menu>
                                                <div class="py-1 text-sm flex flex-col items-stretch">
                                                    <button class="w-full text-left px-3 py-2 hover:bg-slate-50" wire:click="view({{ $u->id }})">View</button>
                                                    <a class="w-full text-left px-3 py-2 hover:bg-slate-50" href="{{ route('admin.bookings', ['q' => $u->email]) }}" wire:navigate title="View all bookings for {{ $u->name }}">All bookings</a>
                                                    @if($isBanned)
                                                        <button class="w-full text-left px-3 py-2 text-green-700 hover:bg-green-50" wire:click="unban({{ $u->id }})">Unban</button>
                                                    @else
                                                        <button class="w-full text-left px-3 py-2 text-red-600 hover:bg-red-50" wire:click="ban({{ $u->id }})">Ban</button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-6 text-center text-sm text-slate-500">No customers found.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile list -->
                    <div class="md:hidden space-y-3">
                        @forelse($users as $u)
                            @php $isBanned = !empty($u->banned_at); @endphp
                            <div class="rounded-lg border border-slate-200 bg-white shadow-sm p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="text-sm font-semibold text-slate-900">#{{ $u->id }} • {{ $u->name }}</div>
                                        <div class="text-sm text-slate-600">{{ $u->email }}</div>
                                        <div class="text-xs text-slate-500">Joined: {{ optional($u->created_at)->format('M d, Y') }}</div>
                                        <div class="mt-2 text-xs">Spent: <strong>₦{{ number_format((float)($u->spent_sum ?? 0),2) }}</strong> • Wallet: <strong>₦{{ number_format((float)($u->wallet_balance ?? 0),2) }}</strong></div>
                                        <div class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $isBanned ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">{{ $isBanned ? 'Banned' : 'Active' }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="relative inline-block text-left" data-dropdown>
                                            <button type="button" class="inline-flex items-center gap-1.5 rounded-md h-9 px-3 border border-slate-300 text-slate-700 font-medium hover:bg-slate-50" data-dropdown-button aria-haspopup="true" aria-expanded="false">
                                                <span class="material-symbols-outlined text-base">more_vert</span>
                                                <span>Actions</span>
                                            </button>
                                            <div class="absolute right-0 mt-2 w-44 origin-top-right rounded-md border border-slate-200 bg-white shadow-lg z-30 hidden" data-dropdown-menu>
                                                <div class="py-1 text-sm flex flex-col items-stretch">
                                                    <button class="w-full text-left px-3 py-2 hover:bg-slate-50" wire:click="view({{ $u->id }})">View</button>
                                                    <a class="w-full text-left px-3 py-2 hover:bg-slate-50" href="{{ route('admin.bookings', ['q' => $u->email]) }}" wire:navigate>All bookings</a>
                                                    @if($isBanned)
                                                        <button class="w-full text-left px-3 py-2 text-green-700 hover:bg-green-50" wire:click="unban({{ $u->id }})">Unban</button>
                                                    @else
                                                        <button class="w-full text-left px-3 py-2 text-red-600 hover:bg-red-50" wire:click="ban({{ $u->id }})">Ban</button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-md border border-slate-200 bg-white p-6 text-center text-sm text-slate-500">No customers found.</div>
                        @endforelse
                    </div>

                    <div class="mt-4 flex items-center justify-between">
                        <div class="text-xs text-slate-500">
                            @if($users instanceof \Illuminate\Contracts\Pagination\Paginator)
                                Showing page {{ $users->currentPage() }} of {{ $users->lastPage() }}
                            @endif
                        </div>
                        <div>
                            {{ $users->onEachSide(1)->links() }}
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <!-- Drawer: Customer details & report -->
    @if($selected)
        <div class="fixed inset-0 z-50 flex items-center justify-end">
            <div class="absolute inset-0 bg-black/50" wire:click="closeView"></div>
            <div class="relative z-10 w-full max-w-3xl h-full overflow-y-auto rounded-l-lg bg-white shadow-xl border-l border-slate-200">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between sticky top-0 bg-white/90 backdrop-blur">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">{{ $selected->name }} <span class="text-slate-500 text-sm">(#{{ $selected->id }})</span></h3>
                        <div class="text-xs text-slate-500">Joined: {{ optional($selected->created_at)->format('M d, Y') }}</div>
                    </div>
                    <button class="p-1 text-slate-500 hover:text-slate-700" wire:click="closeView"><span class="material-symbols-outlined">close</span></button>
                </div>
                <div class="px-5 py-4 text-sm text-slate-700">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="rounded-lg border border-slate-200 bg-white p-4">
                            <div class="text-xs text-slate-500">Total Spent</div>
                            <div class="text-2xl font-bold mt-1">₦{{ number_format((float)($selected->spent_sum ?? 0), 2) }}</div>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-white p-4">
                            <div class="text-xs text-slate-500">Wallet Balance</div>
                            <div class="text-2xl font-bold mt-1">₦{{ number_format((float)($selected->wallet_balance ?? 0), 2) }}</div>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-white p-4">
                            <div class="text-xs text-slate-500">Completed Trips</div>
                            <div class="text-2xl font-bold mt-1">{{ number_format((int)($selected->bookings_count ?? 0)) }}</div>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="rounded-lg border border-slate-200 bg-white p-4">
                            <div class="flex items-center justify-between">
                                <h4 class="font-semibold text-slate-900">Bookings Over Time (Bar)</h4>
                            </div>
                            @if(!empty($custColumn))
                                <div style="height: 220px;">
                                    <livewire:livewire-column-chart
                                        key="{{ $custColumn->reactiveKey() }}"
                                        :column-chart-model="$custColumn"
                                    />
                                </div>
                            @else
                                <div class="text-sm text-slate-500 mt-2">No data to display.</div>
                            @endif
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-white p-4">
                            <div class="flex items-center justify-between">
                                <h4 class="font-semibold text-slate-900">Status Breakdown</h4>
                            </div>
                            @if(!empty($custPie))
                                <div style="height: 220px;">
                                    <livewire:livewire-pie-chart
                                        key="{{ $custPie->reactiveKey() }}"
                                        :pie-chart-model="$custPie"
                                    />
                                </div>
                            @else
                                <div class="text-sm text-slate-500 mt-2">No data to display.</div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6 rounded-lg border border-slate-200 bg-white p-4">
                        <div class="flex items-center justify-between">
                            <h4 class="font-semibold text-slate-900">Recent Bookings</h4>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left mt-3">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-200">
                                        <th class="px-4 py-2 text-xs font-semibold text-slate-600 uppercase tracking-wider">ID</th>
                                        <th class="px-4 py-2 text-xs font-semibold text-slate-600 uppercase tracking-wider">Car</th>
                                        <th class="px-4 py-2 text-xs font-semibold text-slate-600 uppercase tracking-wider">Dates</th>
                                        <th class="px-4 py-2 text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-2 text-xs font-semibold text-slate-600 uppercase tracking-wider text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200">
                                    @forelse($recent ?? [] as $b)
                                        @php $st = strtolower((string)($b->status ?? '')); @endphp
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-slate-700">#{{ $b->id }}</td>
                                            <td class="px-4 py-2 text-sm text-slate-700">{{ $b->car->name ?? '—' }}</td>
                                            <td class="px-4 py-2 text-sm text-slate-600">{{ optional($b->start_date)->format('M d, Y') }} — {{ optional($b->end_date)->format('M d, Y') }}</td>
                                            <td class="px-4 py-2 text-sm">{{ ucfirst($st) }}</td>
                                            <td class="px-4 py-2 text-sm text-right font-semibold text-slate-900">₦{{ number_format((float)($b->total ?? 0), 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-4 text-center text-sm text-slate-500">No recent bookings.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-between gap-2">
                        <a class="rounded-md h-10 px-4 border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50 inline-flex items-center" href="{{ route('admin.bookings', ['q' => $selected->email]) }}" wire:navigate title="View all bookings for {{ $selected->name }}">
                            <span class="material-symbols-outlined text-base mr-1">book_online</span>
                            <span>View all bookings</span>
                        </a>
                        <div class="flex items-center gap-2">
                            @php $isBanned = !empty($selected->banned_at); @endphp
                            @if($isBanned)
                                <button class="rounded-md h-10 px-4 bg-green-600 text-white text-sm font-semibold hover:bg-green-700" wire:click="unban({{ $selected->id }})">Unban</button>
                            @else
                                <button class="rounded-md h-10 px-4 bg-red-600 text-white text-sm font-semibold hover:bg-red-700" wire:click="ban({{ $selected->id }})">Ban</button>
                            @endif
                            <button class="rounded-md h-10 px-4 border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50" wire:click="closeView">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endif
</div>

<script>
// Accessible dropdowns for action menus (copied from bookings page for consistency)
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
    document.addEventListener('click', function(e){ if (!root.contains(e.target)) close(); });
    document.addEventListener('keydown', function(e){ if (e.key === 'Escape') close(); });
    window.addEventListener('livewire:navigated', close);
  }
  function initAll(){ document.querySelectorAll('[data-dropdown]').forEach(initDropdown); }

  if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', initAll); } else { initAll(); }
  window.addEventListener('livewire:navigated', initAll);

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
