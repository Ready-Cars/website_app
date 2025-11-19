<x-layouts.main-admin title="Booking Management - {{ config('app.name') }}">
    <livewire:admin.bookings />
    <script>
        // Alpine.js component for handling booking modals with frontend data
        document.addEventListener('alpine:init', () => {
            // Create a global store for booking modal functionality
            Alpine.store('bookingModal', {
                showViewModal: false,
                selectedBooking: null,
                bookingsData: @json($bookingsData ?? []),

                init() {
                    // Listen for specific bookings data updates from Livewire
                    window.addEventListener('bookingsDataUpdated', (event) => {
                        this.bookingsData = event.detail[0] || {};
                        window.bookingsData = this.bookingsData;
                    });

                    // Fallback: Listen for general Livewire updates
                    window.addEventListener('livewire:updated', () => {
                        if (window.bookingsData) {
                            this.bookingsData = window.bookingsData;
                        }
                    });
                },

                openViewModal(bookingId) {
                    this.selectedBooking = this.bookingsData[bookingId] || null;
                    if (this.selectedBooking) {
                        this.showViewModal = true;
                    }
                },

                closeViewModal() {
                    this.showViewModal = false;
                    this.selectedBooking = null;
                },

                confirmBooking(bookingId) {
                    // Use Livewire.find to get the component and call the method
                    const wireId = document.querySelector('[wire\\:id]')?.getAttribute('wire:id');
                    if (wireId && window.Livewire) {
                        const component = window.Livewire.find(wireId);
                        if (component) {
                            component.call('confirm', bookingId);
                        }
                    }
                },

                openSettings() {
                    // Use Livewire.find to get the component and call the method
                    const wireId = document.querySelector('[wire\\:id]')?.getAttribute('wire:id');
                    if (wireId && window.Livewire) {
                        const component = window.Livewire.find(wireId);
                        if (component) {
                            component.call('openSettings');
                        }
                    }
                }
            });

            // Make openViewModal globally accessible
            window.openViewModal = function(bookingId) {
                Alpine.store('bookingModal').openViewModal(bookingId);
            };

            Alpine.data('bookingModal', () => ({
                get showViewModal() { return Alpine.store('bookingModal').showViewModal; },
                get selectedBooking() { return Alpine.store('bookingModal').selectedBooking; },
                closeViewModal() { Alpine.store('bookingModal').closeViewModal(); }
            }));
        });
    </script>

    <script>
        // Accessible dropdowns for action menus
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
                // close on outside click
                document.addEventListener('click', function(e){
                    if (menu.classList.contains('hidden')) return;
                    if (!root.contains(e.target)) close();
                });
                // close on escape
                document.addEventListener('keydown', function(e){ if (e.key === 'Escape') close(); });
                // close after any Livewire navigation
                window.addEventListener('livewire:navigated', close);
            }
            function initAll(){ document.querySelectorAll('[data-dropdown]').forEach(initDropdown); }

            // Initial bind
            if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', initAll); } else { initAll(); }
            // Re-init on Livewire SPA navigation
            window.addEventListener('livewire:navigated', initAll);

            // Re-init when Livewire updates the DOM (via MutationObserver)
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

</x-layouts.main-admin>
