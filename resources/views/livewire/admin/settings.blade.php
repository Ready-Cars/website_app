<div class="p-6">
    <h1 class="text-2xl font-bold text-slate-900 mb-6">Settings</h1>

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

        <!-- Contact Information Section -->
        <div class="pt-8 border-t border-slate-200">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Contact Information</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                    <input type="email" class="form-input w-full max-w-md" wire:model.defer="contactEmail" placeholder="contact@example.com">
                    @error('contactEmail') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Phone Number</label>
                    <input type="text" class="form-input w-full max-w-md" wire:model.defer="contactPhone" placeholder="+1 (555) 123-4567">
                    @error('contactPhone') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Address</label>
                    <textarea class="form-textarea w-full max-w-md" rows="3" wire:model.defer="contactAddress" placeholder="Street address, city, state, ZIP code"></textarea>
                    @error('contactAddress') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                    <textarea class="form-textarea w-full max-w-md" rows="4" wire:model.defer="contactDescription" placeholder="Brief description about your company or services"></textarea>
                    @error('contactDescription') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Manual Payment Settings Section -->
        <div class="pt-8 border-t border-slate-200">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Manual Payment Settings</h2>
            <p class="text-sm text-slate-600 mb-4">Configure bank account details for manual payment instructions sent to customers.</p>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Account Number</label>
                    <input type="text" class="form-input w-full max-w-md" wire:model.defer="manualPaymentAccountNumber" placeholder="Enter account number">
                    @error('manualPaymentAccountNumber') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Bank Name</label>
                    <input type="text" class="form-input w-full max-w-md" wire:model.defer="manualPaymentBankName" placeholder="Enter bank name">
                    @error('manualPaymentBankName') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="pt-6">
            <button wire:click="save" class="inline-flex items-center justify-center gap-2 rounded-md h-10 px-5 bg-[#1173d4] text-white text-sm font-semibold hover:bg-[#0f63b9]">
                <span class="material-symbols-outlined text-base">save</span>
                <span>Save settings</span>
            </button>
        </div>
    </div>
</div>
