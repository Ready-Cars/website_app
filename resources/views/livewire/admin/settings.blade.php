<div class="p-6">
    <h1 class="text-2xl font-bold text-slate-900 mb-4">Booking & Cancellation Settings</h1>

    @if (session('success'))
        <div class="mb-4 rounded-md border border-green-300 bg-green-50 p-3 text-green-800">{{ session('success') }}</div>
    @endif

    <div class="space-y-6 max-w-xl">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-slate-900 font-medium">Refund on cancellation</div>
                <div class="text-slate-600 text-sm">If enabled, when a booking is cancelled, the customer's total is credited back to their wallet.</div>
            </div>
            <label class="inline-flex items-center cursor-pointer">
                <input type="checkbox" class="sr-only peer" wire:model.defer="refundOnCancellation">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute relative after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#1173d4]"></div>
            </label>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Cancellation cutoff (hours before pickup)</label>
            <input type="number" min="0" max="720" class="form-input w-40" wire:model.defer="cancellationCutoffHours">
            <p class="text-xs text-slate-500 mt-1">Set to 0 to allow cancellation anytime before pickup date.</p>
            @error('cancellationCutoffHours') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="pt-2">
            <button wire:click="save" class="inline-flex items-center justify-center gap-2 rounded-md h-10 px-5 bg-[#1173d4] text-white text-sm font-semibold hover:bg-[#0f63b9]">
                <span class="material-symbols-outlined text-base">save</span>
                <span>Save settings</span>
            </button>
        </div>
    </div>
</div>
