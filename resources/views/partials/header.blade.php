<header class="relative flex items-center justify-between whitespace-nowrap border-b border-solid border-b-slate-200 px-4 sm:px-6 lg:px-10 py-4" data-mobile-menu-root>
    <div class="flex items-center gap-3 text-slate-900">
        <svg class="h-8 w-8 text-[#1173d4]" fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
            <path d="M4 42.4379C4 42.4379 14.0962 36.0744 24 41.1692C35.0664 46.8624 44 42.2078 44 42.2078L44 7.01134C44 7.01134 35.068 11.6577 24.0031 5.96913C14.0971 0.876274 4 7.27094 4 7.27094L4 42.4379Z" fill="currentColor"></path>
        </svg>
        <a href="{{ route('home') }}" class="text-xl font-bold tracking-tight" wire:navigate>{{ config('app.name') }}</a>
    </div>

    <nav class="hidden lg:flex items-center gap-8">
        <a class="text-slate-700 hover:text-[#1173d4] text-sm font-medium transition-colors" href="{{ route('cars.index') }}" wire:navigate>Car catalog</a>
        @auth
            <a class="text-slate-700 hover:text-[#1173d4] text-sm font-medium transition-colors" href="{{ route('trips.index') }}" wire:navigate>My trips</a>
            <a class="text-slate-700 hover:text-[#1173d4] text-sm font-medium transition-colors" href="{{ route('wallet.index') }}" wire:navigate>Wallet</a>
            @if(auth()->user()->is_admin ?? false)
                <a class="text-slate-700 hover:text-[#1173d4] text-sm font-medium transition-colors" href="{{ route('dashboard') }}" wire:navigate>Dashboard</a>
            @endif
        @endauth
    </nav>

    <div class="flex items-center gap-2">
        <button class="lg:hidden p-2" aria-label="Open menu" aria-controls="mobile-menu" aria-expanded="false" data-mobile-menu-button>
            <span class="material-symbols-outlined text-slate-700">menu</span>
        </button>
        @guest
            <a href="{{ route('register') }}" class="hidden sm:flex min-w-[84px] max-w-[480px] items-center justify-center overflow-hidden rounded-md h-10 px-4 bg-[#1173d4] text-white text-sm font-bold tracking-wide hover:bg-[#0f63b9] transition-colors" wire:navigate>
                <span class="truncate">Register</span>
            </a>
            <a href="{{ route('login') }}" class="hidden sm:flex min-w-[84px] max-w-[480px] items-center justify-center overflow-hidden rounded-md h-10 px-4 bg-slate-200 text-slate-900 text-sm font-bold tracking-wide hover:bg-slate-300 transition-colors" wire:navigate>
                <span class="truncate">Login</span>
            </a>
        @else
            <a href="{{ route('trips.index') }}" class="hidden sm:flex min-w-[84px] max-w-[480px] items-center justify-center overflow-hidden rounded-md h-10 px-4 bg-slate-200 text-slate-900 text-sm font-bold tracking-wide hover:bg-slate-300 transition-colors" wire:navigate>
                <span class="truncate">My trips</span>
            </a>
        @endguest
    </div>

    <!-- Mobile dropdown menu -->
    <div id="mobile-menu" class="lg:hidden absolute left-0 right-0 top-full z-40 bg-white border-b border-slate-200 shadow-sm hidden" data-mobile-menu-panel>
        <div class="px-4 sm:px-6 py-3 flex flex-col gap-2">
            <a class="block py-2 text-slate-800 hover:text-[#1173d4] text-sm font-medium" href="{{ route('cars.index') }}" wire:navigate>Car catalog</a>
            @auth
                <a class="block py-2 text-slate-800 hover:text-[#1173d4] text-sm font-medium" href="{{ route('trips.index') }}" wire:navigate>My trips</a>
                <a class="block py-2 text-slate-800 hover:text-[#1173d4] text-sm font-medium" href="{{ route('wallet.index') }}" wire:navigate>Wallet</a>
                @if(auth()->user()->is_admin ?? false)
                    <a class="block py-2 text-slate-800 hover:text-[#1173d4] text-sm font-medium" href="{{ route('dashboard') }}" wire:navigate>Dashboard</a>
                @endif
            @else
                <a class="block py-2 text-slate-800 hover:text-[#1173d4] text-sm font-medium" href="{{ route('login') }}" wire:navigate>Login</a>
                <a class="block py-2 text-slate-800 hover:text-[#1173d4] text-sm font-medium" href="{{ route('register') }}" wire:navigate>Register</a>
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
