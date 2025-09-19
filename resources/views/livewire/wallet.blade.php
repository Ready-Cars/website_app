<div>
    <div class="relative flex h-auto min-h-screen w-full flex-col overflow-x-hidden group/design-root">
        <div class="layout-container flex h-full grow flex-col">
            @include('partials.header')

            <main class="flex-1 px-4 sm:px-6 lg:px-24 py-10">
                <div class="mx-auto max-w-3xl">
                    <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight mb-2">My Wallet</h1>
                    <p class="text-slate-600 mb-6">Add funds to your wallet and pay for bookings instantly.</p>

                    @if (session('wallet_success'))
                        <div class="mb-6 rounded-md border border-green-300 bg-green-50 px-4 py-3 text-green-800">
                            {{ session('wallet_success') }}
                        </div>
                    @endif
                    @if (session('wallet_error'))
                        <div class="mb-6 rounded-md border border-red-300 bg-red-50 px-4 py-3 text-red-800">
                            {{ session('wallet_error') }}
                        </div>
                    @endif

                    <div class="rounded-lg bg-white shadow-sm border border-slate-200 p-6 space-y-6">
                        <div class="flex items-center justify-between">
                            <div class="text-slate-600">Current balance</div>
                            <div class="text-3xl font-extrabold text-slate-900">₦{{ number_format($balance, 2) }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Amount to add</label>
                            <form action="{{ route('wallet.paystack.init') }}" method="POST" class="flex gap-3 w-full">
                                @csrf
                                <input type="number" step="0.01" min="1" name="amount" wire:model.defer="amount" class="form-input w-full rounded-md border-gray-300 focus:border-[#1173d4] focus:ring-[#1173d4]" placeholder="e.g., 5000.00">
                                <button type="submit" class="inline-flex items-center justify-center rounded-md h-11 px-5 bg-[#1173d4] text-white text-sm font-bold tracking-wide hover:bg-[#0f63b9] transition-colors">Fund Wallet</button>
                            </form>
                            <div class="mt-2">
                                <button type="button" wire:click="addFunds" class="inline-flex items-center justify-center rounded-md h-9 px-3 border border-slate-300 text-slate-700 text-xs font-medium hover:bg-slate-50">Add dummy funds (dev)</button>
                            </div>
                            @error('amount') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            <div class="mt-3 flex items-center gap-2">
                                <span class="text-xs text-slate-500">Quick add:</span>
                                <button type="button" wire:click="preset(20)" class="rounded-md border border-slate-300 px-3 py-1.5 text-sm hover:bg-slate-50">₦20</button>
                                <button type="button" wire:click="preset(50)" class="rounded-md border border-slate-300 px-3 py-1.5 text-sm hover:bg-slate-50">₦50</button>
                                <button type="button" wire:click="preset(100)" class="rounded-md border border-slate-300 px-3 py-1.5 text-sm hover:bg-slate-50">₦100</button>
                            </div>
                            <div class="text-xs text-slate-500 mt-2">Note: Funding is simulated for demo purposes. No real payment is processed.</div>
                        </div>

                        <!-- History -->
                        <div>
                            <h3 class="text-base font-semibold text-slate-900 mb-3">Recent activity</h3>
                            <div class="overflow-hidden rounded-md border border-slate-200">
                                <table class="min-w-full divide-y divide-slate-200">
                                    <thead class="bg-slate-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Date</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Description</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Amount</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 bg-white">
                                        @forelse(($transactions ?? []) as $tx)
                                            <tr>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-slate-700">{{ optional($tx->created_at)->format('M d, Y H:i') }}</td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-slate-700">{{ $tx->description ?? ucfirst($tx->type) }}</td>
                                                @php $sign = $tx->type === 'debit' ? '-' : '+'; @endphp
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-right {{ $tx->type === 'debit' ? 'text-red-600' : 'text-green-700' }}">{{ $sign }}₦{{ number_format($tx->amount, 2) }}</td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-right text-slate-700">₦{{ number_format($tx->balance_after, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-500">No activity yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-4 flex items-center justify-between">
                                <div class="text-xs text-slate-500">
                                    @if($transactions instanceof \Illuminate\Contracts\Pagination\Paginator)
                                        Showing page {{ $transactions->currentPage() }} of {{ $transactions->lastPage() }}
                                    @endif
                                </div>
                                <div>
                                    @if(method_exists($transactions, 'links'))
                                        {{ $transactions->onEachSide(1)->links() }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
