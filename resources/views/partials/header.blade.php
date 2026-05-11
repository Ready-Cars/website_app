<style>
    button {
        cursor: pointer;
    }
</style>

@if(!session('is_from_app'))

    <header
        class="sticky top-0 z-50 bg-[#0e1133] flex items-center justify-between whitespace-nowrap px-4 sm:px-6 lg:px-10 py-3 md:py-4"
        data-mobile-menu-root>
        <div class="flex items-center gap-6">
            <a href="{{ route('home') }}" class="inline-flex items-center transition-all duration-200 hover:opacity-95 hover:scale-[1.01] active:scale-[0.99]" aria-label="{{ config('app.name') }}"
                wire:navigate>
                <img src="{{ asset('img.png') }}" alt="{{ config('app.name') }} logo"
                    class="h-7 md:h-10 w-auto object-contain" />
            </a>

            <nav class="hidden xl:flex items-center gap-8 ml-4">
                @php
                    $linkBase = 'inline-flex items-center text-[0.95rem] font-medium tracking-tight transition-all duration-200 hover:-translate-y-px';
                    $activeClasses = 'text-white border-b-2 border-white';
                    $inactiveClasses = 'text-white/70 hover:text-white';
                @endphp
                <a class="{{ request()->routeIs('cars.index') ? "$activeClasses" : "$inactiveClasses" }} {{ $linkBase }}"
                    href="{{ route('cars.index') }}" wire:navigate>Car catalog</a>
                <a class="{{ request()->routeIs('contact.index') ? "$activeClasses" : "$inactiveClasses" }} {{ $linkBase }}"
                    href="{{ route('contact.index') }}" wire:navigate>Contact</a>
                @auth
                    <a class="{{ request()->routeIs('trips.index') ? "$activeClasses" : "$inactiveClasses" }} {{ $linkBase }}"
                        href="{{ route('trips.index') }}" wire:navigate>My trips</a>
                @endauth
            </nav>
        </div>

        <div class="flex items-center gap-4">
            <a href="#"
                class="hidden md:block text-[0.93rem] font-medium text-white/90 hover:text-white px-4 py-2 rounded-full transition-all duration-200 hover:-translate-y-px">
                Why choose {{ config('app.name') }}
            </a>

            <!-- Pill User Menu -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" @click.away="open = false"
                    class="flex items-center gap-2 border border-white/20 rounded-full p-1.5 pl-2.5 md:p-2 md:pl-3 hover:bg-white/5 transition-all duration-200 hover:scale-[1.02] active:scale-95">
                    <span class="material-symbols-outlined text-white text-[22px] md:text-2xl">menu</span>
                    <div class="w-7 h-7 md:w-8 md:h-8 rounded-full bg-slate-700 flex items-center justify-center overflow-hidden">
                        @auth
                            <span class="text-xs font-bold text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
                        @else
                            <span class="material-symbols-outlined text-slate-300 text-xl">account_circle</span>
                        @endauth
                    </div>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open" x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-64 origin-top-right bg-white rounded-xl shadow-xl border border-slate-100 overflow-hidden py-2"
                    style="display: none;">

                    @guest
                        <a href="{{ route('cars.index') }}"
                            class="block px-4 py-3 text-[0.95rem] font-medium text-slate-700 hover:bg-slate-50 transition-all duration-150 hover:pl-5 border-b border-slate-50"
                            wire:navigate>Car catalog</a>
                        <a href="{{ route('login') }}"
                            class="block px-4 py-3 text-[0.95rem] font-medium text-slate-700 hover:bg-slate-50 transition-all duration-150 hover:pl-5" wire:navigate>Log
                            in</a>
                        <a href="{{ route('register') }}" class="block px-4 py-3 text-[0.92rem] text-slate-600 hover:bg-slate-50 transition-all duration-150 hover:pl-5"
                            wire:navigate>Sign up</a>
                        <div class="h-px bg-slate-100 my-1"></div>
                        <a href="#" class="block px-4 py-3 text-[0.92rem] text-slate-600 hover:bg-slate-50 transition-all duration-150 hover:pl-5">Become a host</a>
                    @else
                        <div class="px-4 py-2 mb-1">
                            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-[0.12em]">Account</p>
                        </div>
                        @if(auth()->user()->is_admin ?? false)
                            <a href="{{ route('dashboard') }}"
                                class="block px-4 py-2 text-[0.95rem] text-slate-700 hover:bg-slate-50 transition-all duration-150 hover:pl-5 font-medium" wire:navigate>Admin
                                Dashboard</a>
                        @endif
                        <a href="{{ route('cars.index') }}" class="block px-4 py-2 text-[0.95rem] text-slate-700 hover:bg-slate-50 transition-all duration-150 hover:pl-5"
                            wire:navigate>Car catalog</a>
                        <a class="block px-4 py-2 text-[0.95rem] text-slate-700 hover:bg-slate-50 transition-all duration-150 hover:pl-5" href="{{ route('trips.index') }}"
                            wire:navigate>My trips</a>
                        <a href="{{ route('profile.index') }}" class="block px-4 py-2 text-[0.95rem] text-slate-700 hover:bg-slate-50 transition-all duration-150 hover:pl-5"
                            wire:navigate>Profile</a>
                        <a href="{{ route('wallet.index') }}" class="block px-4 py-2 text-[0.95rem] text-slate-700 hover:bg-slate-50 transition-all duration-150 hover:pl-5"
                            wire:navigate>Wallet</a>
                        <a href="{{ route('notifications.index') }}"
                            class="block px-4 py-2 text-[0.95rem] text-slate-700 hover:bg-slate-50 transition-all duration-150 hover:pl-5" wire:navigate>Notifications</a>

                        <div class="h-px bg-slate-100 my-2"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="block w-full text-left px-4 py-2 text-[0.95rem] text-rose-600 hover:bg-rose-50 transition-all duration-150 hover:pl-5">Log out</button>
                        </form>
                    @endguest
                </div>
            </div>
        </div>
    </header>
    @livewire('pricing-disclaimer')
@endif