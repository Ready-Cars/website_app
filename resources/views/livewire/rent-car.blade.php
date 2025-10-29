<div>
    <div class="relative flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-hidden">
        <div class="layout-container flex h-full grow flex-col">
            @include('partials.header')

            <main class="flex-1 px-4 sm:px-6 lg:px-24 py-10">
                <div class="mx-auto max-w-6xl">
                    <div class="mb-8">
                        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">Rent {{ $car->name }}</h1>
                        <p class="text-slate-600">Plan your trip and confirm your reservation below.</p>
                    </div>

                    @if (session('rent_success') && !$successOpen)
                        <div class="mb-6 rounded-md border border-green-300 bg-green-50 px-4 py-3 text-green-800">
                            {{ session('rent_success') }}
                        </div>
                    @endif

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                        <div class="lg:col-span-2 space-y-6">
                            <!-- Car gallery slider -->
                            @php
                                $gallery = array_values(array_filter(array_merge([
                                    $car->image_url ?? null,
                                ], (array)($car->images ?? []))));
                                if (empty($gallery)) {
                                    $gallery = ['https://via.placeholder.com/1280x720?text=No+Image'];
                                }
                            @endphp
                            <div class="rounded-lg overflow-hidden bg-white shadow-sm border border-slate-200">
                                <div class="relative">
                                    <img id="car-main-image" src="{{ $gallery[0] }}" alt="{{ $car->name }}" class="w-full aspect-video object-cover" />
                                    @if(count($gallery) > 1)
                                        <button type="button" class="absolute left-2 top-1/2 -translate-y-1/2 inline-flex items-center justify-center rounded-full bg-black/40 text-white hover:bg-black/60 w-9 h-9" data-gal-prev aria-label="Previous image">
                                            <span class="material-symbols-outlined">chevron_left</span>
                                        </button>
                                        <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 inline-flex items-center justify-center rounded-full bg-black/40 text-white hover:bg-black/60 w-9 h-9" data-gal-next aria-label="Next image">
                                            <span class="material-symbols-outlined">chevron_right</span>
                                        </button>
                                    @endif
                                </div>

                                @if(count($gallery) > 1)
                                <div class="p-3 border-t border-slate-200">
                                    <div class="flex gap-2 overflow-x-auto" id="car-thumbs" aria-label="Image thumbnails">
                                        @foreach($gallery as $i => $src)
                                            <button type="button" class="shrink-0 rounded-md overflow-hidden border {{ $i === 0 ? 'ring-2 ring-[#1173d4] border-[#1173d4]/20' : 'border-slate-200' }}" data-gal-thumb data-index="{{ $i }}" aria-label="Show image {{ $i + 1 }}">
                                                <img src="{{ $src }}" alt="{{ $car->name }} thumbnail {{ $i + 1 }}" class="w-20 h-14 object-cover" />
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                                <div class="p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h2 class="text-xl font-semibold text-slate-900">{{ $car->name }}</h2>
                                            <p class="text-slate-600 text-sm">{{ $car->category }} • {{ $car->transmission }} • {{ $car->seats }} seats • {{ $car->fuel_type }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-lg font-bold text-slate-900">From ₦{{ number_format($car->daily_price, 0) }}<span class="text-sm font-normal text-slate-500">/day</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Rental form -->
                            <div class="rounded-lg bg-white shadow-sm border border-slate-200 p-5 space-y-5">
                                <h3 class="text-lg font-semibold text-slate-900">Trip details</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Pick-up location</label>
                                        <input type="text" data-field="pickupLocation" class="form-input w-full rounded-md border-slate-300 focus:border-[#1173d4] focus:ring-[#1173d4]" placeholder="City or address" wire:model.defer="pickupLocation">
                                        @error('pickupLocation') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Drop-off location</label>
                                        <input type="text" data-field="dropoffLocation" class="form-input w-full rounded-md border-slate-300 focus:border-[#1173d4] focus:ring-[#1173d4]" placeholder="City or address" wire:model.defer="dropoffLocation">
                                        @error('dropoffLocation') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Start date</label>
                                        <input type="date" data-field="startDate" class="form-input w-full rounded-md border-slate-300 focus:border-[#1173d4] focus:ring-[#1173d4]" wire:model.live="startDate" min="{{ \Carbon\Carbon::today()->toDateString() }}">
                                        @error('startDate') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">End date</label>
                                        <input type="date" data-field="endDate" class="form-input w-full rounded-md border-slate-300 focus:border-[#1173d4] focus:ring-[#1173d4]" wire:model.live="endDate" min="{{ \Carbon\Carbon::tomorrow()->toDateString() }}">
                                        @error('endDate') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Service type</label>
                                    <select data-field="serviceTypeId" class="form-select w-full rounded-md border-slate-300 focus:border-[#1173d4] focus:ring-[#1173d4]" wire:model.defer="serviceTypeId">
                                        <option value="0" >Select a service type</option>
                                        @foreach($serviceTypeOptions as $st)
                                            <option value="{{ $st['id'] }}">{{ $st['name'] }}</option>
{{--                                            <option value="{{ $st['id'] }}">{{ $st['name'] }} — {{ ucfirst($st['pricing_type']) }}</option>--}}
                                        @endforeach
                                    </select>
                                    @error('serviceTypeId') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                    @php
                                        $sel = null;
                                        foreach ($serviceTypeOptions as $opt) { if ((int)($opt['id'] ?? 0) === (int)($serviceTypeId ?? 0)) { $sel = $opt; break; } }
                                    @endphp
{{--                                    @if($sel && ($sel['pricing_type'] ?? '') === 'negotiable')--}}
{{--                                        <div class="mt-2 rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-amber-800 text-sm">--}}
{{--                                            This service type is Negotiable. No payment will be deducted now. A Support Agent will set the price and confirm your booking.--}}
{{--                                        </div>--}}
{{--                                    @endif--}}
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Extras</label>
                                    @if(!empty($availableExtras))
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        @foreach($availableExtras as $ex)
                                            @php $name = $ex['name']; $key = $ex['key']; @endphp
                                            <label class="flex items-center gap-2 text-sm text-slate-700">
                                                <input type="checkbox" class="rounded border-slate-300 text-[#1173d4] focus:ring-[#1173d4]" wire:model.live="extras.{{ $key }}">
                                                {{ $name }} (+₦{{ number_format($ex['price_per_day'], 2) }}/day)
                                            </label>
                                        @endforeach
                                    </div>
                                    @else
                                        <p class="text-sm text-slate-500">No extra options available at this time.</p>
                                    @endif
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                                    <textarea rows="3" data-field="notes" class="form-textarea w-full rounded-md border-slate-300 focus:border-[#1173d4] focus:ring-[#1173d4]" placeholder="Any special requests?" wire:model.defer="notes"></textarea>
                                    @error('notes') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <!-- Desktop-only total + confirm at bottom of form -->
                                <div class="hidden lg:flex items-center justify-between pt-3 border-t border-slate-200">
                                    <div class="text-base">
                                        <span class="text-slate-600">Total</span>
                                        <span class="ml-2 text-xl font-extrabold text-slate-900">₦{{ number_format($this->total, 2) }}</span>
                                    </div>
                                    <button wire:click="openConfirm" class="inline-flex items-center justify-center rounded-md h-11 px-5 bg-[#1173d4] text-white text-sm font-bold tracking-wide hover:bg-[#0f63b9] transition-colors">
                                        Confirm reservation
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Summary card -->
                        <aside class="space-y-6">
                            <div class="rounded-lg bg-white shadow-sm border border-slate-200 p-5">
                                <h3 class="text-lg font-semibold text-slate-900 mb-4">Price summary</h3>
                                <ul class="space-y-2 text-sm text-slate-700">
                                    @php
                                        $sel = null;
                                        foreach ($serviceTypeOptions as $opt) { if ((int)($opt['id'] ?? 0) === (int)($serviceTypeId ?? 0)) { $sel = $opt; break; } }
                                    @endphp
                                    @if($sel)
                                        <li class="flex justify-between"><span>Service type</span> <span>{{ $sel['name'] }} ({{ ucfirst($sel['pricing_type']) }})</span></li>
                                    @endif
                                    <li class="flex justify-between"><span>Days</span> <span>{{ $this->days }}</span></li>
                                    <li class="flex justify-between"><span>Daily rate</span> <span>₦{{ number_format($car->daily_price, 2) }}</span></li>
                                    <li class="flex justify-between"><span>Extras</span> <span>₦{{ number_format($this->extrasCost, 2) }}</span></li>
                                    <li class="flex justify-between"><span>Subtotal</span> <span>₦{{ number_format($this->subtotal, 2) }}</span></li>
                                    <li class="flex justify-between"><span>Taxes ({{ number_format($this->taxRate * 100, 2) }}%)</span> <span>₦{{ number_format($this->taxes, 2) }}</span></li>
                                </ul>
                                <div class="mt-3 border-t pt-3 flex justify-between items-center">
                                    <span class="text-base font-semibold text-slate-900">Total</span>
                                    <span class="text-xl font-extrabold text-slate-900">₦{{ number_format($this->total, 2) }}</span>
                                </div>
                                <div class="mt-5">
                                    <button wire:click="openConfirm" class="w-full inline-flex items-center justify-center rounded-md h-11 px-4 bg-[#1173d4] text-white text-sm font-bold tracking-wide hover:bg-[#0f63b9] transition-colors">
                                        Confirm reservation
                                    </button>
                                </div>
                                <p class="mt-2 text-[13px] text-slate-500">By confirming, you agree to our rental terms and privacy policy.</p>
                            </div>
                            <div class="rounded-lg bg-white shadow-sm border border-slate-200 p-5 text-sm text-slate-600">
                                <p><strong>Free cancellation</strong> up to 24 hours before pick-up.</p>
                                <p class="mt-2">Client must present a valid means of identification at pick-up.</p>
                            </div>
                        </aside>
                    </div>
                </div>
            </main>

          @include('partials.footer')
        </div>
    </div>

    <!-- Confirm Reservation Modal -->
    @if($confirmOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="relative z-10 w-full max-w-md rounded-lg bg-white shadow-xl border border-slate-200">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Confirm reservation</h3>
                <button class="p-1 text-slate-500 hover:text-slate-700" wire:click="closeConfirm">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="px-5 py-4 text-sm text-slate-700 space-y-2">
                <p>Please review your trip details before confirming:</p>
                <ul class="space-y-1">
                    <li class="flex justify-between"><span class="text-slate-500">Car</span><span class="font-medium text-slate-900">{{ $car->name }}</span></li>
                    <li class="flex justify-between"><span class="text-slate-500">Pick-up</span><span class="font-medium text-slate-900">{{ $pickupLocation ?: '—' }}</span></li>
                    <li class="flex justify-between"><span class="text-slate-500">Drop-off</span><span class="font-medium text-slate-900">{{ $dropoffLocation ?: '—' }}</span></li>
                    <li class="flex justify-between"><span class="text-slate-500">Dates</span><span class="font-medium text-slate-900">{{ $startDate }} → {{ $endDate }}</span></li>
                    @php
                        $sel = null;
                        foreach ($serviceTypeOptions as $opt) { if ((int)($opt['id'] ?? 0) === (int)($serviceTypeId ?? 0)) { $sel = $opt; break; } }
                    @endphp
                    @if($sel)
                        <li class="flex justify-between"><span class="text-slate-500">Service type</span><span class="font-medium text-slate-900">{{ $sel['name'] }} ({{ ucfirst($sel['pricing_type']) }})</span></li>
                    @endif
                    <li class="flex justify-between"><span class="text-slate-500">Total</span><span class="font-extrabold text-slate-900">₦{{ number_format($this->total, 2) }}</span></li>
                </ul>
            </div>
            <div class="px-5 py-4 border-t border-slate-200 flex items-center justify-end gap-3">
                <button wire:click="cancelConfirm" class="inline-flex items-center justify-center rounded-md h-10 px-4 border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50">Cancel</button>
                <button wire:click="confirmRent" class="inline-flex items-center justify-center rounded-md h-10 px-5 bg-[#1173d4] text-white text-sm font-bold hover:bg-[#0f63b9]">Confirm</button>
            </div>
        </div>
    </div>
    @endif

    <!-- Toast Container -->
    <div id="toast" class="fixed top-4 right-4 z-[60] hidden">
        <div id="toast-inner" class="rounded-md border px-4 py-3 text-sm shadow-md"></div>
    </div>

    <!-- Success Modal -->
    @if($successOpen)
    <div class="fixed inset-0 z-[70] flex items-center justify-center">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="relative z-10 w-full max-w-md rounded-lg bg-white shadow-xl border border-slate-200">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Success</h3>
                <button class="p-1 text-slate-500 hover:text-slate-700" wire:click="$set('successOpen', false)">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="px-5 py-5 text-slate-700">
                <p class="text-sm">{{ $successMessage }}</p>
            </div>
            <div class="px-5 py-4 border-t border-slate-200 flex items-center justify-end">
                <button class="inline-flex items-center justify-center rounded-md h-10 px-4 bg-[#1173d4] text-white text-sm font-semibold hover:bg-[#0f63b9]" wire:click="$set('successOpen', false)">OK</button>
            </div>
        </div>
    </div>
    @endif
</div>
<script>
    // Listen for Livewire dispatched event and smoothly scroll to the first invalid field
    (function() {
        const scrollHandler = function (event) {
            try {
                const field = event.detail?.field;
                if (!field) return;
                const el = document.querySelector('[data-field="' + field + '"]');
                if (el) {
                    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    // slight delay to ensure scroll completes before focusing
                    setTimeout(() => {
                        try { el.focus({ preventScroll: true }); } catch (e) {}
                    }, 250);
                }
            } catch (e) {
                // no-op
            }
        };
        window.addEventListener('scroll-to-field', scrollHandler);

        // Simple toast helper
        const toastEl = document.getElementById('toast');
        const toastInner = document.getElementById('toast-inner');
        let toastTimer = null;
        function showToast(message, type = 'info') {
            if (!toastEl || !toastInner) return;
            const styles = {
                info: 'bg-white border-slate-200 text-slate-800',
                success: 'bg-green-50 border-green-300 text-green-800',
                error: 'bg-red-50 border-red-300 text-red-800',
            };
            toastInner.className = 'rounded-md border px-4 py-3 text-sm shadow-md ' + (styles[type] || styles.info);
            toastInner.textContent = message;
            toastEl.classList.remove('hidden');
            clearTimeout(toastTimer);
            toastTimer = setTimeout(() => toastEl.classList.add('hidden'), 3200);
        }

        // Success/cancel notifications
        window.addEventListener('rent-confirmed', () => showToast('Reservation confirmed!', 'success'));
        window.addEventListener('reservation-cancelled', () => showToast('Reservation cancelled', 'info'));
    })();

    // Lightweight gallery slider logic
    (function(){
        function init(){
            const main = document.getElementById('car-main-image');
            const thumbs = document.getElementById('car-thumbs');
            const prev = document.querySelector('[data-gal-prev]');
            const next = document.querySelector('[data-gal-next]');
            if (!main) return;
            const imgs = thumbs ? Array.from(thumbs.querySelectorAll('[data-gal-thumb] img')).map(img => img.getAttribute('src')) : [main.getAttribute('src')];
            let idx = 0;
            function setIndex(i){
                if (!imgs.length) return;
                idx = (i + imgs.length) % imgs.length;
                main.src = imgs[idx];
                // update rings on thumbs
                if (thumbs){
                    thumbs.querySelectorAll('[data-gal-thumb]').forEach((btn, i) => {
                        if (i === idx){ btn.classList.add('ring-2','ring-[#1173d4]'); }
                        else { btn.classList.remove('ring-2','ring-[#1173d4]'); }
                    });
                }
            }
            if (thumbs){
                thumbs.querySelectorAll('[data-gal-thumb]').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        const i = parseInt(btn.getAttribute('data-index') || '0', 10) || 0;
                        setIndex(i);
                    });
                });
            }
            if (prev) prev.addEventListener('click', () => setIndex(idx - 1));
            if (next) next.addEventListener('click', () => setIndex(idx + 1));
            // keyboard support
            document.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft' && prev) { setIndex(idx - 1); }
                if (e.key === 'ArrowRight' && next) { setIndex(idx + 1); }
            });
        }
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
        window.addEventListener('livewire:navigated', init);
    })();
</script>
