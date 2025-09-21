@php
    $name = auth()->user()->name ?? 'Admin';
    $initials = auth()->user()?->initials() ?? 'AD';
@endphp
<div class="relative" data-user-menu>
    <button type="button"
            class="h-10 w-10 overflow-hidden rounded-full bg-slate-200 flex items-center justify-center text-slate-700 font-bold ring-1 ring-transparent hover:ring-slate-300 focus:outline-none focus:ring-sky-500"
            aria-haspopup="true" aria-expanded="false" aria-controls="admin-user-dropdown" data-user-menu-button
            title="{{ $name }}">
        {{ $initials }}
    </button>
    <div id="admin-user-dropdown"
         class="absolute right-0 mt-2 w-48 origin-top-right rounded-md border border-slate-200 bg-white shadow-lg z-30 hidden"
         role="menu" aria-label="User menu" data-user-menu-dropdown>
        <div class="py-1 text-sm flex flex-col items-stretch">
            <a href="{{ route('admin.profile') }}" class="px-3 py-2 hover:bg-slate-50 flex items-center gap-2" wire:navigate role="menuitem">
                <span class="material-symbols-outlined text-base">person</span>
                <span>Profile</span>
            </a>
            <form method="POST" action="{{ route('logout') }}" x-ignore>
                @csrf
                <button type="submit" class="px-3 py-2 text-red-600 hover:bg-red-50 w-full text-left flex items-center gap-2" role="menuitem">
                    <span class="material-symbols-outlined text-base">logout</span>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
(function(){
  function initUserMenu(root){
    if (!root || root.__userMenuInited) return; root.__userMenuInited = true;
    var btn = root.querySelector('[data-user-menu-button]');
    var dd = root.querySelector('[data-user-menu-dropdown]');
    if (!btn || !dd) return;
    function open(){ dd.classList.remove('hidden'); btn.setAttribute('aria-expanded','true'); }
    function close(){ dd.classList.add('hidden'); btn.setAttribute('aria-expanded','false'); }
    function toggle(){ (btn.getAttribute('aria-expanded') === 'true') ? close() : open(); }
    btn.addEventListener('click', function(e){ e.stopPropagation(); toggle(); });
    document.addEventListener('click', function(e){ if (!root.contains(e.target)) close(); });
    document.addEventListener('keydown', function(e){ if (e.key === 'Escape') close(); });
    window.addEventListener('livewire:navigated', close);
  }
  function initAll(){ document.querySelectorAll('[data-user-menu]').forEach(initUserMenu); }
  if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', initAll); } else { initAll(); }
  window.addEventListener('livewire:navigated', initAll);
})();
</script>
