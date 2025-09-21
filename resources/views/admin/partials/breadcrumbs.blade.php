@php
    // Optional static items passed by server as a fallback. When JavaScript is
    // available, we will replace this with a dynamic, visited-links-based trail.
    $items = $items ?? [];
@endphp

<nav class="mb-3 text-sm" aria-label="Breadcrumb">
    <ol id="admin-breadcrumbs" class="flex flex-wrap items-center gap-1 text-slate-500">
        @foreach($items as $index => $it)
            @php
                $isLast = $index === count($items) - 1;
                $label = (string)($it['label'] ?? '');
                $url = $it['url'] ?? null;
            @endphp
            <li class="flex items-center">
                @if(!$isLast && !empty($url))
                    <a href="{{ $url }}" class="hover:text-slate-700" wire:navigate>{{ $label }}</a>
                @else
                    <span class="text-slate-700">{{ $label }}</span>
                @endif
            </li>
            @if(!$isLast)
                <li class="mx-1 text-slate-400" aria-hidden="true">/</li>
            @endif
        @endforeach
    </ol>
</nav>

<script>
(function(){
  // Build a dynamic breadcrumb trail based on recently visited admin pages (max 3)
  // Works across Livewire SPA navigation via the 'livewire:navigated' event.
  var STORAGE_KEY = 'admin_breadcrumbs_trail_v1';

  function pathToLabel(path){
    // Normalize trailing slash
    if (path.length > 1 && path.endsWith('/')) path = path.slice(0, -1);
    var map = {
      '/dashboard': 'Dashboard',
      '/admin/bookings': 'Bookings',
      '/admin/cars': 'Cars',
      '/admin/customers': 'Customers',
      '/admin/car-options': 'Car Options',
      '/admin/reports': 'Reports',
      '/admin/profile': 'Profile'
    };
    if (map[path]) return map[path];
    // Fallback: use document.title without the app name suffix if present
    try {
      var t = document.title || '';
      // Common titles are like: "Admin Dashboard - AppName" or "Car Management - AppName"
      if (t.includes(' - ')) t = t.split(' - ')[0];
      return t || path;
    } catch(e){ return path; }
  }

  function isAdminPath(path){
    return path === '/dashboard' || path.startsWith('/admin');
  }

  function readTrail(){
    try { return JSON.parse(sessionStorage.getItem(STORAGE_KEY) || '[]'); } catch(e){ return []; }
  }
  function writeTrail(arr){
    try { sessionStorage.setItem(STORAGE_KEY, JSON.stringify(arr)); } catch(e){}
  }

  function pushCurrent(){
    var loc = window.location;
    var path = loc.pathname || '/';
    if (!isAdminPath(path)) return;
    var url = loc.origin + path + (loc.search || '');
    var label = pathToLabel(path);

    var trail = readTrail();
    // Remove any existing entry for the same path
    trail = trail.filter(function(it){ return (it && it.path) ? it.path !== path : true; });
    // Push current at the end
    trail.push({ path: path, url: url, label: label });
    // Keep only last 3
    if (trail.length > 3) trail = trail.slice(trail.length - 3);

    writeTrail(trail);
  }

  function render(){
    var el = document.getElementById('admin-breadcrumbs');
    if (!el) return;
    var trail = readTrail();
    if (!trail.length) return; // keep server-rendered fallback

    var parts = [];
    for (var i = 0; i < trail.length; i++){
      var it = trail[i];
      var isLast = (i === trail.length - 1);
      if (!it) continue;
      if (isLast){
        parts.push('<li class="flex items-center"><span class="text-slate-700">' + escapeHtml(it.label) + '</span></li>');
      } else {
        parts.push('<li class="flex items-center"><a class="hover:text-slate-700" href="' + encodeURI(it.url) + '" wire:navigate>' + escapeHtml(it.label) + '</a></li>');
        parts.push('<li class="mx-1 text-slate-400" aria-hidden="true">/</li>');
      }
    }
    el.innerHTML = parts.join('');
  }

  function escapeHtml(s){
    return String(s || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function update(){
    pushCurrent();
    render();
  }

  // Initialize
  if (document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', update);
  } else {
    update();
  }
  // Re-run after Livewire SPA navigations
  window.addEventListener('livewire:navigated', update);
})();
</script>
