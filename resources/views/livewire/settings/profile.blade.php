<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile')" :subheading="__('View and update your personal information')">
        <div class="my-6 w-full grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left card: Avatar and email info -->
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
                        <div class="mt-4">
                            <flux:text class="mt-2">
                                {{ __('Your email address is unverified.') }}
                                <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                    {{ __('Resend verification email') }}
                                </flux:link>
                            </flux:text>
                            @if (session('status') === 'verification-link-sent')
                                <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                    {{ __('A new verification link has been sent to your email address.') }}
                                </flux:text>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            <!-- Right card: Editable fields -->
            <div class="col-span-1 lg:col-span-2">
                <form wire:submit="updateProfileInformation" class="rounded-xl border border-slate-200 p-6 bg-white shadow-sm space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:input wire:model="name" :label="__('Full name')" type="text" required autofocus autocomplete="name" />
                        <flux:input wire:model="phone" :label="__('Phone number')" type="tel" required autocomplete="tel" placeholder="e.g., +234 801 234 5678" />
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="flex items-center justify-end">
                            <flux:button variant="primary" type="submit" class="w-full">{{ __('Save changes') }}</flux:button>
                        </div>
                        <x-action-message class="me-3" on="profile-updated">
                            {{ __('Saved.') }}
                        </x-action-message>
                    </div>
                </form>
            </div>
        </div>

        <livewire:settings.delete-user-form />
    </x-settings.layout>
</section>
