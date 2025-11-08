<div>
    @if($showDisclaimer)
        <!-- Full Screen Overlay -->
        <div class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm">
            <!-- Modal -->
            <div class="relative w-full max-w-md mx-4 bg-white rounded-lg shadow-xl border border-slate-200 dark:bg-zinc-800 dark:border-zinc-700">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-slate-200 dark:border-zinc-700">
                    <div class="flex items-center gap-3">
                        <flux:icon name="exclamation-triangle" class="w-6 h-6 text-amber-500" />
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">
                            Important Pricing Information
                        </h2>
                    </div>
                </div>

                <!-- Body -->
                <div class="px-6 py-4 space-y-4">
                    <div class="text-slate-600 dark:text-zinc-300 space-y-3">
                        <p class="font-medium">
                            Please note the following before proceeding:
                        </p>

                        <ul class="space-y-2 text-sm list-disc list-inside">
                            <li>
                                <strong>Indicative Pricing:</strong> All prices displayed on this website are for indication purposes only and do not represent the final booking price.
                            </li>
                            <li>
                                <strong>Price Negotiation:</strong> Our support staff will reach out to you after your booking to discuss and negotiate the final pricing based on your specific requirements.
                            </li>
                            <li>
                                <strong>Final Confirmation:</strong> The actual rental price will be confirmed during our consultation call with you.
                            </li>
                        </ul>

                        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-3 mt-4">
                            <p class="text-amber-800 dark:text-amber-200 text-sm font-medium">
                                By clicking "I Understand", you acknowledge that you have read and accept these pricing terms.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 border-t border-slate-200 dark:border-zinc-700 flex justify-end">
                    <flux:button
                        wire:click="acceptDisclaimer"
                        variant="primary"
                        class="min-w-[120px]"
                    >
                        I Understand
                    </flux:button>
                </div>
            </div>
        </div>
    @endif
</div>
