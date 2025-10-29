<div>
    <div class="relative flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-hidden" style='font-family: "Work Sans", "Noto Sans", sans-serif;'>
        <div class="layout-container flex h-full grow flex-col">

            <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8 w-full max-w-6xl mx-auto pb-24 md:pb-8">
                <div class="flex flex-col gap-8">
                    <div class="flex flex-col gap-2">
                        <h1 class="text-gray-900 text-4xl font-bold leading-tight">Profile</h1>
                        <p class="text-gray-600 text-lg">View and update your personal information.</p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Left: Avatar and email -->
                        <div class="col-span-1">
                            <div class="rounded-xl border border-slate-200 p-6 bg-white shadow-sm">
                                <div class="flex items-center gap-4">
                                    <div class="h-14 w-14 rounded-full bg-[#1173d4] text-white flex items-center justify-center text-xl font-bold">
                                        {{ auth()->user()->initials() }}
                                    </div>
                                    <div>
                                        <div class="text-sm text-slate-500">Email</div>
                                        <div class="text-slate-900 font-medium">{{ $email }}</div>
                                    </div>
                                </div>
                                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                                    <div class="mt-4 text-sm">
                                        <p class="text-slate-600">Your email address is unverified.</p>
                                        <button type="button" class="mt-2 inline-flex items-center gap-2 rounded-md h-9 px-3 border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50" wire:click.prevent="resendVerificationNotification">
                                            <span class="material-symbols-outlined text-base">forward_to_inbox</span>
                                            <span>Resend verification email</span>
                                        </button>
                                        @if (session('status') === 'verification-link-sent')
                                            <p class="mt-2 text-green-700 text-sm">A new verification link has been sent to your email address.</p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Right: Editable form -->
                        <div class="col-span-1 lg:col-span-2">
                            <form wire:submit="updateProfileInformation" class="rounded-xl border border-slate-200 p-6 bg-white shadow-sm space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-slate-700">Full name</label>
                                        <input id="name" type="text" wire:model="name" required autocomplete="name" class="mt-1 block w-full rounded-md border-slate-300 focus:border-[#1173d4] focus:ring-[#1173d4]" />
                                        @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-slate-700">Phone number</label>
                                        <input id="phone" type="tel" wire:model="phone" required autocomplete="tel" placeholder="e.g., +234 801 234 5678" class="mt-1 block w-full rounded-md border-slate-300 focus:border-[#1173d4] focus:ring-[#1173d4]" />
                                        @error('phone') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md h-10 px-5 bg-[#1173d4] text-white text-sm font-semibold hover:bg-[#0f63b9]">
                                        <span class="material-symbols-outlined text-base">save</span>
                                        <span>Save changes</span>
                                    </button>
                                    <x-action-message class="me-3" on="profile-updated">
                                        {{ __('Saved.') }}
                                    </x-action-message>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
