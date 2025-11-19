<aside class="hidden w-64 flex-col border-r border-slate-200 bg-white p-4 lg:flex">
    <nav class="flex flex-col space-y-2">
        @php
            $active = $active ?? '';
            $linkBase = 'flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium';
            $inactive = $linkBase.' text-slate-600 hover:bg-slate-100';
            $activeCls = $linkBase.' bg-sky-50 text-sky-700';
        @endphp
        <a class="{{ $active === 'dashboard' ? $activeCls : $inactive }}" href="{{ route('dashboard') }}" wire:navigate>
            <span class="material-symbols-outlined"> dashboard </span>
            <span>Dashboard</span>
        </a>
        <a class="{{ $active === 'bookings' ? $activeCls : $inactive }}" href="{{ route('admin.bookings') }}" >
            <span class="material-symbols-outlined"> book_online </span>
            <span>Booking Management</span>
        </a>
        <a class="{{ $active === 'cars' ? $activeCls : $inactive }}" href="{{ route('admin.cars') }}" wire:navigate>
            <span class="material-symbols-outlined"> directions_car </span>
            <span>Car Management</span>
        </a>
        <a class="{{ $active === 'customers' ? $activeCls : $inactive }}" href="{{ route('admin.customers') }}" wire:navigate>
            <span class="material-symbols-outlined"> group </span>
            <span>Customer Management</span>
        </a>
        <a class="{{ $active === 'car-options' ? $activeCls : $inactive }}" href="{{ route('admin.car-options') }}" wire:navigate>
            <span class="material-symbols-outlined"> tune </span>
            <span>Car Options</span>
        </a>
        <a class="{{ $active === 'reports' ? $activeCls : $inactive }}" href="{{ route('admin.reports') }}" >
            <span class="material-symbols-outlined"> assessment </span>
            <span>Reporting</span>
        </a>
        <a class="{{ $active === 'profile' ? $activeCls : $inactive }}" href="{{ route('admin.profile') }}" wire:navigate>
            <span class="material-symbols-outlined"> person </span>
            <span>Profile</span>
        </a>
    </nav>
</aside>

<!-- Mobile drawer and backdrop -->
<div id="admin-mobile-backdrop" class="fixed inset-0 z-30 hidden bg-black/40 lg:hidden" aria-hidden="true"></div>
<aside id="admin-mobile-drawer" class="fixed inset-y-0 left-0 z-40 w-64 transform -translate-x-full transition-transform duration-200 ease-out border-r border-slate-200 bg-white p-4 lg:hidden" aria-label="Admin navigation" aria-hidden="true">
    <div class="flex items-center justify-between mb-2">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-sky-600">menu</span>
            <span class="font-semibold">Menu</span>
        </div>
        <button type="button" class="rounded-md p-2 text-slate-600 hover:bg-slate-100" data-admin-menu-close aria-label="Close menu">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>
    <nav class="flex flex-col space-y-2 mt-2">
        @php
            $linkBase = 'flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium';
            $inactive = $linkBase.' text-slate-700 hover:bg-slate-100';
            $activeCls = $linkBase.' bg-sky-50 text-sky-700';
        @endphp
        <a class="{{ $active === 'dashboard' ? $activeCls : $inactive }}" href="{{ route('dashboard') }}" wire:navigate>
            <span class="material-symbols-outlined"> dashboard </span>
            <span>Dashboard</span>
        </a>
        <a class="{{ $active === 'bookings' ? $activeCls : $inactive }}" href="{{ route('admin.bookings') }}" >
            <span class="material-symbols-outlined"> book_online </span>
            <span>Booking Management</span>
        </a>
        <a class="{{ $active === 'cars' ? $activeCls : $inactive }}" href="{{ route('admin.cars') }}" wire:navigate>
            <span class="material-symbols-outlined"> directions_car </span>
            <span>Car Management</span>
        </a>
        <a class="{{ $active === 'customers' ? $activeCls : $inactive }}" href="{{ route('admin.customers') }}" wire:navigate>
            <span class="material-symbols-outlined"> group </span>
            <span>Customer Management</span>
        </a>
        <a class="{{ $active === 'car-options' ? $activeCls : $inactive }}" href="{{ route('admin.car-options') }}" wire:navigate>
            <span class="material-symbols-outlined"> tune </span>
            <span>Car Options</span>
        </a>
        <a class="{{ $active === 'reports' ? $activeCls : $inactive }}" href="{{ route('admin.reports') }}" wire:navigate>
            <span class="material-symbols-outlined"> assessment </span>
            <span>Reporting</span>
        </a>
        <a class="{{ $active === 'profile' ? $activeCls : $inactive }}" href="{{ route('admin.profile') }}" wire:navigate>
            <span class="material-symbols-outlined"> person </span>
            <span>Profile</span>
        </a>
    </nav>
</aside>

<script>
(function(){
  function openDrawer(){
    var d = document.getElementById('admin-mobile-drawer');
    var b = document.getElementById('admin-mobile-backdrop');
    if(!d||!b) return;
    d.setAttribute('aria-hidden','false');
    b.classList.remove('hidden');
    requestAnimationFrame(function(){ d.classList.remove('-translate-x-full'); });
    var opener = document.querySelector('[data-admin-menu-open]');
    if (opener) opener.setAttribute('aria-expanded','true');
  }
  function closeDrawer(){
    var d = document.getElementById('admin-mobile-drawer');
    var b = document.getElementById('admin-mobile-backdrop');
    if(!d||!b) return;
    d.classList.add('-translate-x-full');
    d.setAttribute('aria-hidden','true');
    var opener = document.querySelector('[data-admin-menu-open]');
    if (opener) opener.setAttribute('aria-expanded','false');
    setTimeout(function(){ b.classList.add('hidden'); }, 180);
  }
  function bind(){
    var openBtn = document.querySelector('[data-admin-menu-open]');
    var closeBtn = document.querySelector('[data-admin-menu-close]');
    var backdrop = document.getElementById('admin-mobile-backdrop');
    if (openBtn && !openBtn.__bound){ openBtn.addEventListener('click', openDrawer); openBtn.__bound = true; }
    if (closeBtn && !closeBtn.__bound){ closeBtn.addEventListener('click', closeDrawer); closeBtn.__bound = true; }
    if (backdrop && !backdrop.__bound){ backdrop.addEventListener('click', closeDrawer); backdrop.__bound = true; }
    document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeDrawer(); });
    window.addEventListener('livewire:navigated', closeDrawer);
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', bind); else bind();
  // Re-bind after Livewire updates
  window.addEventListener('livewire:navigated', bind);
})();
</script>

@if(session('success'))
    <div class="fixed bottom-4 right-4 z-[9999] max-w-md">
        <div class="rounded-lg bg-white shadow-lg border border-green-200 p-4">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 w-5 h-5 text-green-500">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                              clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
                <button class="flex-shrink-0 text-green-600 hover:text-green-800"
                        onclick="this.parentElement.parentElement.remove()">
                    <span class="material-symbols-outlined text-base">close</span>
                </button>
            </div>
        </div>
    </div>
@endif
