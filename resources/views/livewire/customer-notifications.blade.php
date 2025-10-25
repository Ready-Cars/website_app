<div>
    <div class="relative flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-hidden" style='font-family: "Work Sans", "Noto Sans", sans-serif;'>
        <div class="layout-container flex h-full grow flex-col">
            @include('partials.header')

            <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto pb-24 md:pb-8">
                <div class="flex flex-col gap-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-gray-900 text-3xl font-bold leading-tight">Notifications</h1>
                            <p class="text-gray-600">Stay updated on your booking activity.</p>
                        </div>
                        <button wire:click="markAllAsRead" class="inline-flex items-center justify-center gap-2 rounded-md h-10 px-4 border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50">
                            <span class="material-symbols-outlined text-base">done_all</span>
                            <span>Mark all as read</span>
                        </button>
                    </div>

                    <div class="flex border-b border-gray-200">
                        <button class="px-4 py-3 text-base font-semibold {{ $tab==='unread' ? 'border-b-2 border-[#1173d4] text-[#1173d4]' : 'text-gray-500 hover:text-gray-700' }}" wire:click="switch('unread')">Unread</button>
                        <button class="px-4 py-3 text-base font-semibold {{ $tab==='all' ? 'border-b-2 border-[#1173d4] text-[#1173d4]' : 'text-gray-500 hover:text-gray-700' }}" wire:click="switch('all')">All</button>
                    </div>

                    <div class="flex flex-col divide-y rounded-xl border border-gray-200 bg-white shadow-sm">
                        @forelse($items as $n)
                            @php $data = $n->data ?? []; @endphp
                            <div class="flex items-start gap-4 p-4 {{ $n->read_at ? 'bg-white' : 'bg-blue-50/40' }}">
                                <div class="mt-1">
                                    <span class="material-symbols-outlined text-2xl {{ $n->read_at ? 'text-slate-400' : 'text-[#1173d4]' }}">notifications</span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-slate-900 font-semibold">{{ $data['title'] ?? 'Notification' }}</h3>
                                        <div class="text-xs text-slate-500">{{ optional($n->created_at)->diffForHumans() }}</div>
                                    </div>
                                    <div class="text-slate-700 text-sm mt-1">{{ $data['message'] ?? '' }}</div>
                                    <div class="mt-2 flex items-center gap-2">
                                        @if(!empty($data['url']))
                                            <a href="{{ $data['url'] }}" class="inline-flex items-center justify-center gap-2 rounded-md h-9 px-3 bg-[#1173d4] text-white text-xs font-semibold hover:bg-[#0f63b9]" wire:navigate>
                                                <span class="material-symbols-outlined text-base">visibility</span>
                                                <span>View booking</span>
                                            </a>
                                        @endif
                                        @if(!$n->read_at)
                                            <button wire:click="markAsRead('{{ $n->id }}')" class="inline-flex items-center justify-center gap-2 rounded-md h-9 px-3 border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50">
                                                <span class="material-symbols-outlined text-base">done</span>
                                                <span>Mark as read</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-slate-600">No notifications found.</div>
                        @endforelse
                    </div>

                    <div>
                        {{ $items->links() }}
                    </div>
                </div>
            </main>
            @include('partials.footer')
        </div>
    </div>
</div>
