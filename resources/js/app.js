// Global Livewire loader overlay
(function(){
  function createLoader(){
    if (document.getElementById('lw-global-loader')) { return document.getElementById('lw-global-loader'); }
    const overlay = document.createElement('div');
    overlay.id = 'lw-global-loader';
    overlay.className = 'loader-overlay';

    const box = document.createElement('div');
    box.className = 'loader-box';

    const spinner = document.createElement('div');
    spinner.className = 'loader-spinner';

    const label = document.createElement('div');
    label.className = 'loader-label';
    label.textContent = 'Loading...';

    box.appendChild(spinner);
    box.appendChild(label);
    overlay.appendChild(box);
    document.body.appendChild(overlay);
    return overlay;
  }

  function init(){
    const overlay = createLoader();
    let active = 0;

    function show(){ overlay.classList.add('show'); }
    function hide(){ overlay.classList.remove('show'); }

    function inc(){ active++; show(); }
    function dec(){ active = Math.max(0, active - 1); if (active === 0) hide(); }

    // Livewire v2: hooks API
    function attachV2Hooks(){
      if (!window.Livewire || !window.Livewire.hook) return false;
      window.Livewire.hook('request', ({ respond, fail }) => {
        inc();
        const done = () => dec();
        respond(done);
        fail(done);
      });
      return true;
    }

    // Livewire v3: DOM events API
    function attachV3Events(){
      let attached = false;
      // v3 dispatches these events on document
      const start = () => { attached = true; inc(); };
      const end = () => dec();
      const error = () => dec();
      document.addEventListener('livewire:request-start', start);
      document.addEventListener('livewire:request-end', end);
      document.addEventListener('livewire:error', error);
      // If none of these ever fire, attached will remain false. That's okay.
      return true;
    }

    // Try to attach immediately
    const v2ok = attachV2Hooks();
    if (!v2ok) {
      // Try again when Livewire boots
      document.addEventListener('livewire:init', attachV2Hooks, { once: true });
      // Also attach v3 event listeners now (they don't require Livewire to exist ahead of time)
      attachV3Events();
    } else {
      // Even if v2 worked, also register v3 events to cover mixed contexts
      attachV3Events();
    }

    // Safety: hide on navigation completed
    window.addEventListener('livewire:navigated', () => { active = 0; hide(); });
  }

  if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', init); }
  else { init(); }
})();
