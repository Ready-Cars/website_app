<div>
    <div class="relative flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-hidden" style='font-family: "Work Sans", "Noto Sans", sans-serif;'>
        <div class="layout-container flex h-full grow flex-col">
            @include('partials.header')

            <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8 w-full max-w-6xl mx-auto pb-24 md:pb-8">
                <div class="flex flex-col gap-8">
                    <div class="flex flex-col gap-2">
                        <h1 class="text-gray-900 text-4xl font-bold leading-tight">My Trips</h1>
                        <p class="text-gray-600 text-lg">Manage your upcoming and past car rentals.</p>
                    </div>
                    <div class="flex flex-col gap-6">
                        <div class="flex border-b border-gray-200">
                            <button class="px-4 py-3 text-base font-semibold {{ $tab==='upcoming' ? 'border-b-2 border-[#1173d4] text-[#1173d4]' : 'text-gray-500 hover:text-gray-700' }}" wire:click="switchTab('upcoming')">Upcoming</button>
                            <button class="px-4 py-3 text-base font-semibold {{ $tab==='past' ? 'border-b-2 border-[#1173d4] text-[#1173d4]' : 'text-gray-500 hover:text-gray-700' }}" wire:click="switchTab('past')">Past</button>
                        </div>
                        <div class="grid grid-cols-1 gap-6">
                            @forelse($trips as $trip)
                                <div class="flex flex-col sm:flex-row items-start gap-6 p-6 bg-white rounded-xl shadow-md border border-gray-200">
                                    <img alt="Car image" class="aspect-video sm:aspect-square w-full sm:w-40 h-auto rounded-lg object-cover" src="{{ $trip->car->image_url }}"/>
                                    <div class="flex-1 flex flex-col gap-2">
                                        <div class="flex justify-between items-start">
                                            <h3 class="text-gray-900 text-xl font-bold">{{ $trip->car->name }}</h3>
                                            @php
                                                $badge = 'bg-green-100 text-green-800';
                                                if ($trip->status === 'cancelled') { $badge = 'bg-red-100 text-red-800'; }
                                                elseif ($trip->status === 'pending') { $badge = 'bg-yellow-100 text-yellow-800'; }
                                            @endphp
                                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $badge }}">{{ ucfirst($trip->status) }}</span>
                                        </div>
                                        <p class="text-gray-600 text-base">{{ $trip->pickup_location }} → {{ $trip->dropoff_location }}</p>
                                        <div class="flex items-center gap-4 text-gray-500 text-sm mt-2">
                                            <div class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-lg"> calendar_today </span>
                                                <span>{{ \Carbon\Carbon::parse($trip->start_date)->format('M d') }} - {{ \Carbon\Carbon::parse($trip->end_date)->format('M d') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-full sm:w-auto flex flex-col sm:items-end gap-2 mt-4 sm:mt-0">
                                        <button wire:click="view({{ $trip->id }})" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-md h-10 px-6 bg-[#1173d4] text-white text-sm font-semibold hover:bg-[#0f63b9] transition-colors">
                                            <span class="material-symbols-outlined text-lg"> visibility </span>
                                            <span>View Booking</span>
                                        </button>
                                        @if($trip->status !== 'cancelled')
                                            <button wire:click="view({{ $trip->id }})" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-md h-10 px-4 border border-red-200 text-red-700 bg-red-50 text-sm font-semibold hover:bg-red-100 transition-colors" aria-label="Cancel booking">
                                                <span class="material-symbols-outlined text-base"> delete </span>
                                                <span>Cancel booking</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-600">No trips found.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <!-- Toast Container -->
        <div id="toast" class="fixed top-4 right-4 z-[60] hidden">
            <div id="toast-inner" class="rounded-md border px-4 py-3 text-sm shadow-md"></div>
        </div>
    </div>

    <!-- View Booking Modal -->
    @if($viewOpen && $selected)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50" wire:click="closeView"></div>
            <div class="relative z-10 w-full max-w-2xl rounded-lg bg-white shadow-xl border border-slate-200">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-900">Booking details</h3>
                    <button class="p-1 text-slate-500 hover:text-slate-700" wire:click="closeView">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <div class="px-5 py-4 text-sm text-slate-700">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="rounded-md border border-slate-200 overflow-hidden">
                            <div class="w-full bg-center bg-no-repeat aspect-video bg-cover" style="background-image: url('{{ $selected->car->image_url }}');"></div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between"><span class="text-slate-500">Car</span><span class="font-medium text-slate-900">{{ $selected->car->name }}</span></div>
                            <div class="flex justify-between"><span class="text-slate-500">Status</span>
                                @php
                                    $badge = 'bg-green-100 text-green-800';
                                    if ($selected->status === 'cancelled') { $badge = 'bg-red-100 text-red-800'; }
                                    elseif ($selected->status === 'pending') { $badge = 'bg-yellow-100 text-yellow-800'; }
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $badge }}">{{ ucfirst($selected->status) }}</span>
                            </div>
                            <div class="flex justify-between"><span class="text-slate-500">Pick-up</span><span class="font-medium text-slate-900">{{ $selected->pickup_location }}</span></div>
                            <div class="flex justify-between"><span class="text-slate-500">Drop-off</span><span class="font-medium text-slate-900">{{ $selected->dropoff_location }}</span></div>
                            <div class="flex justify-between"><span class="text-slate-500">Dates</span><span class="font-medium text-slate-900">{{ \Carbon\Carbon::parse($selected->start_date)->format('M d, Y') }} → {{ \Carbon\Carbon::parse($selected->end_date)->format('M d, Y') }}</span></div>
                            <div class="flex justify-between"><span class="text-slate-500">Total</span><span class="font-extrabold text-slate-900">₦{{ number_format($selected->total, 2) }}</span></div>
                            @if($selected->status === 'cancelled' && $selected->cancellation_reason)
                                <div class="pt-2">
                                    <div class="text-slate-500">Cancellation reason</div>
                                    <div class="mt-1 rounded-md border border-slate-200 bg-slate-50 p-2 text-slate-700">{{ $selected->cancellation_reason }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    @php $extras = (array)($selected->extras ?? []); @endphp
                    @if(!empty($extras))
                        <div class="mt-4">
                            <div class="text-slate-500 mb-1">Extras</div>
                            <ul class="list-disc pl-5 text-slate-700">
                                @foreach($extras as $key => $val)
                                    @if($val)
                                        <li class="capitalize">{{ str_replace('_',' ', $key) }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                <div class="px-5 py-4 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="text-sm text-slate-600">Need to change dates or locations? Modify your booking.</div>
                    <div class="flex items-center gap-2">
                        @if($selected->status !== 'cancelled')
                        <a href="{{ route('rent.show', [$selected->car, 'booking' => $selected->id]) }}" class="inline-flex items-center justify-center gap-2 rounded-md h-10 px-4 border border-[#1173d4]/30 text-[#0f63b9] bg-white text-sm font-semibold hover:bg-[#1173d4]/5" wire:navigate>
                            <span class="material-symbols-outlined text-base">edit</span>
                            <span>Modify</span>
                        </a>
                        <button wire:click="openCancel" class="inline-flex items-center justify-center gap-2 rounded-md h-10 px-4 bg-[#ef4444] text-white text-sm font-semibold hover:bg-[#dc2626]">
                            <span class="material-symbols-outlined text-base">delete</span>
                            <span>Cancel</span>
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Cancel Booking Modal -->
    @if($cancelOpen && $selected)
        <div class="fixed inset-0 z-[60] flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50" wire:click="$set('cancelOpen', false)"></div>
            <div class="relative z-10 w-full max-w-md rounded-lg bg-white shadow-xl border border-slate-200">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#ef4444]">warning</span>
                        <h3 class="text-lg font-semibold text-slate-900">Cancel booking</h3>
                    </div>
                    <button class="p-1 text-slate-500 hover:text-slate-700" wire:click="$set('cancelOpen', false)">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <div class="px-5 py-4 text-sm text-slate-700 space-y-3">
                    <p class="text-slate-600">This action will cancel your reservation. Please tell us why you want to cancel:</p>
                    <textarea wire:model.defer="cancelReason" rows="4" class="form-textarea w-full rounded-md border-gray-300 focus:border-[#1173d4] focus:ring-[#1173d4]" placeholder="e.g., Change of plans, incorrect dates, found a better option, etc."></textarea>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Provide a brief cancellation reason (3–500 characters).</span>
                        <span class="text-slate-500">{{ strlen($cancelReason) }} / 500</span>
                    </div>
                    @error('cancelReason') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="px-5 py-4 border-t border-slate-200 flex items-center justify-end gap-3">
                    <button class="inline-flex items-center justify-center rounded-md h-10 px-4 border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50" wire:click="$set('cancelOpen', false)">
                        <span class="material-symbols-outlined text-base mr-1">arrow_back</span>
                        <span>Back</span>
                    </button>
                    <button wire:click="cancelConfirm" wire:loading.attr="disabled" class="inline-flex items-center justify-center gap-2 rounded-md h-10 px-5 bg-[#ef4444] text-white text-sm font-bold hover:bg-[#dc2626] disabled:opacity-75">
                        <svg wire:loading class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                        <span>Confirm cancellation</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
<script>
    (function(){
        const toastEl = document.getElementById('toast');
        const toastInner = document.getElementById('toast-inner');
        let timer = null;
        function showToast(message, type = 'success'){
            const styles = {
                success: 'bg-green-50 border-green-300 text-green-800',
                info: 'bg-white border-slate-200 text-slate-800',
                error: 'bg-red-50 border-red-300 text-red-800',
            };
            toastInner.className = 'rounded-md border px-4 py-3 text-sm shadow-md ' + (styles[type] || styles.info);
            toastInner.textContent = message;
            toastEl.classList.remove('hidden');
            clearTimeout(timer);
            timer = setTimeout(()=>toastEl.classList.add('hidden'), 3000);
        }
        // Listen for generic rent-confirmed events and show the provided message (fallback to a default)
        window.addEventListener('rent-confirmed', (e) => {
            const msg = (e && e.detail && e.detail.message) ? e.detail.message : 'Booking confirmed successfully';
            showToast(msg, 'success');
        });
    })();
    @if (session('rent_success'))
    // On first load after redirect, dispatch an event to show success toast with server-provided message
    function __dispatchBookingSuccess(){
        window.dispatchEvent(new CustomEvent('rent-confirmed', { detail: { message: @json(session('rent_success')) } }));
    }
    window.addEventListener('load', __dispatchBookingSuccess);
    window.addEventListener('livewire:navigated', __dispatchBookingSuccess);
    @endif
</script>
