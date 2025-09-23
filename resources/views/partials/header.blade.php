<header class="sticky top-0 z-50 bg-[#0e1133] flex items-center justify-between whitespace-nowrap border-b border-solid border-transparent px-4 sm:px-6 lg:px-10 py-4" data-mobile-menu-root>
    <div class="flex items-center gap-3">
        <a href="{{ route('home') }}" class="inline-flex items-center" aria-label="{{ config('app.name') }}" wire:navigate>
            <img src="https://readycars.ng/img/logo.png" alt="{{ config('app.name') }} logo" class="h-8 md:h-9 w-auto object-contain" />
        </a>
    </div>

    <nav class="hidden lg:flex items-center gap-8">
        <a class="text-white/90 hover:text-white text-sm font-medium transition-colors" href="{{ route('cars.index') }}" wire:navigate>Car catalog</a>
        @auth
            <a class="text-white/90 hover:text-white text-sm font-medium transition-colors" href="{{ route('trips.index') }}" wire:navigate>My trips</a>
            <a class="text-white/90 hover:text-white text-sm font-medium transition-colors" href="{{ route('wallet.index') }}" wire:navigate>Wallet</a>
            @if(auth()->user()->is_admin ?? false)
                <a class="text-white/90 hover:text-white text-sm font-medium transition-colors" href="{{ route('dashboard') }}" wire:navigate>Dashboard</a>
                <a class="text-white/90 hover:text-white text-sm font-medium transition-colors" href="{{ route('admin.profile') }}" wire:navigate>Profile</a>
            @else
                <a class="text-white/90 hover:text-white text-sm font-medium transition-colors" href="{{ route('profile.index') }}" wire:navigate>Profile</a>
            @endif
        @endauth
    </nav>

    <div class="flex items-center gap-2">
        <button class="lg:hidden p-2" aria-label="Open menu" aria-controls="mobile-menu" aria-expanded="false" data-mobile-menu-button>
            <span class="material-symbols-outlined text-white">menu</span>
        </button>
        @guest
            <a href="{{ route('register') }}" class="hidden sm:flex min-w-[84px] max-w-[480px] items-center justify-center overflow-hidden rounded-md h-10 px-4 bg-[#1173d4] text-white text-sm font-bold tracking-wide hover:bg-[#0f63b9] transition-colors" wire:navigate>
                <span class="truncate">Register</span>
            </a>
            <a href="{{ route('login') }}" class="hidden sm:flex min-w-[84px] max-w-[480px] items-center justify-center overflow-hidden rounded-md h-10 px-4 bg-slate-200 text-slate-900 text-sm font-bold tracking-wide hover:bg-slate-300 transition-colors" wire:navigate>
                <span class="truncate">Login</span>
            </a>
        @else
            <a href="{{ route('notifications.index') }}" class="hidden sm:flex items-center gap-2 rounded-md h-10 px-3 border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50" wire:navigate>
                <span class="material-symbols-outlined text-base">notifications</span>
                <span>Notifications</span>
            </a>
            <a href="{{ route('trips.index') }}" class="hidden sm:flex min-w-[84px] max-w-[480px] items-center justify-center overflow-hidden rounded-md h-10 px-4 bg-slate-200 text-slate-900 text-sm font-bold tracking-wide hover:bg-slate-300 transition-colors" wire:navigate>
                <span class="truncate">My trips</span>
            </a>
            @auth
                @if(auth()->user()->is_admin ?? false)
                    <a href="{{ route('admin.profile') }}" class="hidden sm:flex items-center gap-2 rounded-md h-10 px-3 border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50" wire:navigate>
                        <span class="material-symbols-outlined text-base">account_circle</span>
                        <span>Profile</span>
                    </a>
                @else
                    <a href="{{ route('profile.index') }}" class="hidden sm:flex items-center gap-2 rounded-md h-10 px-3 border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50" wire:navigate>
                        <span class="material-symbols-outlined text-base">account_circle</span>
                        <span>Profile</span>
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="hidden sm:flex">
                    @csrf
                    <button type="submit" class="min-w-[84px] max-w-[240px] items-center justify-center overflow-hidden rounded-md h-10 px-4 bg-rose-100 text-rose-700 text-sm font-bold tracking-wide hover:bg-rose-200 transition-colors">
                        Logout
                    </button>
                </form>
            @endauth
        @endguest
    </div>

    <!-- Mobile dropdown menu -->
    <div id="mobile-menu" class="lg:hidden absolute left-0 right-0 top-full z-40 bg-[#0e1133] border-b border-transparent shadow-sm hidden" data-mobile-menu-panel>
        <div class="px-4 sm:px-6 py-3 flex flex-col gap-2">
            <a class="block py-2 text-white/90 hover:text-white text-sm font-medium" href="{{ route('cars.index') }}" wire:navigate>Car catalog</a>
            @auth
                <a class="block py-2 text-white/90 hover:text-white text-sm font-medium" href="{{ route('trips.index') }}" wire:navigate>My trips</a>
                <a class="block py-2 text-white/90 hover:text-white text-sm font-medium" href="{{ route('wallet.index') }}" wire:navigate>Wallet</a>
                <a class="block py-2 text-white/90 hover:text-white text-sm font-medium" href="{{ route('notifications.index') }}" wire:navigate>Notifications</a>
                @if(auth()->user()->is_admin ?? false)
                    <a class="block py-2 text-white/90 hover:text-white text-sm font-medium" href="{{ route('dashboard') }}" wire:navigate>Dashboard</a>
                    <a class="block py-2 text-white/90 hover:text-white text-sm font-medium" href="{{ route('admin.profile') }}" wire:navigate>Profile</a>
                @else
                    <a class="block py-2 text-white/90 hover:text-white text-sm font-medium" href="{{ route('profile.index') }}" wire:navigate>Profile</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left py-2 text-rose-300 hover:text-rose-200 text-sm font-medium">Logout</button>
                </form>
            @else
                <a class="block py-2 text-white/90 hover:text-white text-sm font-medium" href="{{ route('login') }}" wire:navigate>Login</a>
                <a class="block py-2 text-white/90 hover:text-white text-sm font-medium" href="{{ route('register') }}" wire:navigate>Register</a>
            @endauth
        </div>
    </div>
</header>
<script>
// Mobile menu toggler (isolated per header instance)
(function(){
    function initHeader(root){
        if (!root || root.__mobileMenuInited) return; // avoid duplicate binding
        const btn = root.querySelector('[data-mobile-menu-button]');
        const panel = root.querySelector('[data-mobile-menu-panel]');
        if (!btn || !panel) return;
        root.__mobileMenuInited = true;

        function open(){ panel.classList.remove('hidden'); btn.setAttribute('aria-expanded','true'); }
        function close(){ panel.classList.add('hidden'); btn.setAttribute('aria-expanded','false'); }
        function toggle(){ const isOpen = btn.getAttribute('aria-expanded') === 'true'; isOpen ? close() : open(); }

        btn.addEventListener('click', (e)=>{ e.stopPropagation(); toggle(); });
        // Click outside closes
        document.addEventListener('click', (e)=>{
            if (panel.classList.contains('hidden')) return;
            if (!root.contains(e.target)) close();
        });
        // Escape closes
        document.addEventListener('keydown', (e)=>{ if (e.key === 'Escape') close(); });
        // Close after Livewire SPA navigation
        window.addEventListener('livewire:navigated', close);
    }
    function initAll(){ document.querySelectorAll('[data-mobile-menu-root]').forEach(initHeader); }
    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', initAll); } else { initAll(); }
    window.addEventListener('livewire:navigated', initAll);
})();
</script>
