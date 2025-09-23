<div class="flex flex-col md:flex-row min-h-screen">
    <!-- Illustration / Brand side -->
    <div class="w-full md:w-1/2 bg-[#0e1133] flex flex-col justify-center items-center p-8 lg:p-12 relative">
        <a href="{{ route('home') }}" class="absolute top-8 left-8 inline-flex items-center" aria-label="{{ config('app.name') }}" wire:navigate>
            <img src="https://readycars.ng/img/logo.png" alt="{{ config('app.name') }} logo" class="h-9 w-auto object-contain" />
        </a>
        <div class="max-w-md text-center">
            <img alt="Car rental illustration" class="w-full h-auto" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBS6i6QbUofp_vAMhhBp0F-ABDnSVWzSQpV6HLuYgeN8cBGGrmmrTbgIUReuOJOSpFgfTd2Y1Np_FMzYah_k9H2gtiniAKhoezOssdCxYWA9SI0Z1jG3hae9pNi60vGqSTrmGPlnRT5QLv2BIl694t_MqzAQO2tCV5gk0RFOccZ2rWCawh6Mqu89ZIM9Q5aO4gIIf_BBqdSg90xhAM-70GawDrpmkaoRFH-oRQjVfSimxpbZGc3hfI14xX7WI1g4L-nED1RvLPx85c0"/>
            <h2 class="text-3xl font-bold text-white mt-8">Drive with Confidence</h2>
            <p class="text-white/80 mt-4">Create your account to book cars, manage trips and access member benefits.</p>
        </div>
    </div>

    <!-- Form side -->
    <div class="w-full md:w-1/2 flex items-center justify-center p-8 lg:p-16 bg-white">
        <div class="w-full max-w-md space-y-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Create an account</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Already have an account?
                    <a class="font-medium text-[#1173d4] hover:text-[#0f63b9]" href="{{ route('login') }}" wire:navigate>Log in</a>
                </p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="text-center" :status="session('status')" />

            <form method="POST" wire:submit="register" class="mt-4 space-y-6">
                <div class="rounded-md shadow-sm -space-y-px">
                    <div class="relative">
                        <label class="sr-only" for="name">Full name</label>
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="material-symbols-outlined text-gray-400">person</span>
                        </div>
                        <input id="name" name="name" type="text" autocomplete="name" wire:model.defer="name" required
                               class="relative block w-full appearance-none rounded-t-md border border-gray-300 px-3 py-4 pl-10 text-gray-900 placeholder-gray-500 focus:z-10 focus:border-[#1173d4] focus:outline-none focus:ring-[#1173d4] sm:text-sm"
                               placeholder="Full name">
                        @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="relative">
                        <label class="sr-only" for="email">Email address</label>
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="material-symbols-outlined text-gray-400">mail</span>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" wire:model.defer="email" required
                               class="relative block w-full appearance-none border border-gray-300 px-3 py-4 pl-10 text-gray-900 placeholder-gray-500 focus:z-10 focus:border-[#1173d4] focus:outline-none focus:ring-[#1173d4] sm:text-sm"
                               placeholder="Email address">
                        @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="relative">
                        <label class="sr-only" for="phone">Phone number</label>
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="material-symbols-outlined text-gray-400">call</span>
                        </div>
                        <input id="phone" name="phone" type="tel" autocomplete="tel" wire:model.defer="phone" required
                               class="relative block w-full appearance-none border border-gray-300 px-3 py-4 pl-10 text-gray-900 placeholder-gray-500 focus:z-10 focus:border-[#1173d4] focus:outline-none focus:ring-[#1173d4] sm:text-sm"
                               placeholder="Phone number (e.g., +234 801 234 5678)">
                        @error('phone') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="relative">
                        <label class="sr-only" for="password">Password</label>
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="material-symbols-outlined text-gray-400">lock</span>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="new-password" wire:model.defer="password" required
                               class="relative block w-full appearance-none border border-gray-300 px-3 py-4 pl-10 text-gray-900 placeholder-gray-500 focus:z-10 focus:border-[#1173d4] focus:outline-none focus:ring-[#1173d4] sm:text-sm"
                               placeholder="Password">
                        @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="relative">
                        <label class="sr-only" for="password_confirmation">Confirm password</label>
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="material-symbols-outlined text-gray-400">lock</span>
                        </div>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" wire:model.defer="password_confirmation" required
                               class="relative block w-full appearance-none rounded-b-md border border-gray-300 px-3 py-4 pl-10 text-gray-900 placeholder-gray-500 focus:z-10 focus:border-[#1173d4] focus:outline-none focus:ring-[#1173d4] sm:text-sm"
                               placeholder="Confirm password">
                        @error('password_confirmation') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <button type="submit" class="group relative flex w-full justify-center rounded-md bg-[#1173d4] py-3 px-4 text-sm font-semibold text-white hover:bg-[#0f63b9] focus:outline-none focus:ring-2 focus:ring-[#1173d4] focus:ring-offset-2">Create account</button>
                </div>
            </form>

            <p class="text-center text-sm text-gray-500">© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</div>
