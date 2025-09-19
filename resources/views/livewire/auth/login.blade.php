<div class="flex flex-col md:flex-row min-h-screen">
    <!-- Illustration / Brand side -->
    <div class="w-full md:w-1/2 bg-gray-100 flex flex-col justify-center items-center p-8 lg:p-12 relative">
        <a href="{{ route('home') }}" class="absolute top-8 left-8 flex items-center gap-3 text-gray-800" wire:navigate>
            <svg class="h-8 w-8 text-[#1173d4]" fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 42.4379C4 42.4379 14.0962 36.0744 24 41.1692C35.0664 46.8624 44 42.2078 44 42.2078L44 7.01134C44 7.01134 35.068 11.6577 24.0031 5.96913C14.0971 0.876274 4 7.27094 4 7.27094L4 42.4379Z" fill="currentColor"></path>
            </svg>
            <h1 class="text-2xl font-bold">{{ config('app.name') }}</h1>
        </a>
        <div class="max-w-md text-center">
            <img alt="Car rental illustration" class="w-full h-auto" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBS6i6QbUofp_vAMhhBp0F-ABDnSVWzSQpV6HLuYgeN8cBGGrmmrTbgIUReuOJOSpFgfTd2Y1Np_FMzYah_k9H2gtiniAKhoezOssdCxYWA9SI0Z1jG3hae9pNi60vGqSTrmGPlnRT5QLv2BIl694t_MqzAQO2tCV5gk0RFOccZ2rWCawh6Mqu89ZIM9Q5aO4gIIf_BBqdSg90xhAM-70GawDrpmkaoRFH-oRQjVfSimxpbZGc3hfI14xX7WI1g4L-nED1RvLPx85c0"/>
            <h2 class="text-3xl font-bold text-gray-800 mt-8">Your Journey, Your Car</h2>
            <p class="text-gray-600 mt-4">Access your account to manage bookings, view rental history, and enjoy exclusive member benefits.</p>
        </div>
    </div>

    <!-- Form side -->
    <div class="w-full md:w-1/2 flex items-center justify-center p-8 lg:p-16 bg-white">
        <div class="w-full max-w-md space-y-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Sign in to your account</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Or
                    @if (Route::has('register'))
                        <a class="font-medium text-[#1173d4] hover:text-[#0f63b9]" href="{{ route('register') }}" wire:navigate>create an account</a>
                    @endif
                </p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="text-center" :status="session('status')" />

            <form method="POST" wire:submit="login" class="mt-4 space-y-6">
                <div class="rounded-md shadow-sm -space-y-px">
                    <div class="relative">
                        <label class="sr-only" for="email">Email</label>
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="material-symbols-outlined text-gray-400">person</span>
                        </div>
                        <input autocomplete="email" id="email" name="email" type="email"
                               wire:model.defer="email"
                               class="relative block w-full appearance-none rounded-t-md border border-gray-300 px-3 py-4 pl-10 text-gray-900 placeholder-gray-500 focus:z-10 focus:border-[#1173d4] focus:outline-none focus:ring-[#1173d4] sm:text-sm"
                               placeholder="Email address" required />
                        @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="relative">
                        <label class="sr-only" for="password">Password</label>
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="material-symbols-outlined text-gray-400">lock</span>
                        </div>
                        <input autocomplete="current-password" id="password" name="password" type="password"
                               wire:model.defer="password"
                               class="relative block w-full appearance-none rounded-b-md border border-gray-300 px-3 py-4 pl-10 text-gray-900 placeholder-gray-500 focus:z-10 focus:border-[#1173d4] focus:outline-none focus:ring-[#1173d4] sm:text-sm"
                               placeholder="Password" required />
                        @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input wire:model="remember" class="h-4 w-4 rounded border-gray-300 text-[#1173d4] focus:ring-[#1173d4]" type="checkbox"/>
                        <span class="ml-2 block text-sm text-gray-900">Remember me</span>
                    </label>
                    <div class="text-sm">
                        @if (Route::has('password.request'))
                            <a class="font-medium text-[#1173d4] hover:text-[#0f63b9]" href="{{ route('password.request') }}" wire:navigate>Forgot your password?</a>
                        @endif
                    </div>
                </div>
                <div>
                    <button type="submit" class="group relative flex w-full justify-center rounded-md bg-[#1173d4] py-3 px-4 text-sm font-semibold text-white hover:bg-[#0f63b9] focus:outline-none focus:ring-2 focus:ring-[#1173d4] focus:ring-offset-2">Log in</button>
                </div>
            </form>

            <p class="text-center text-sm text-gray-500">© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</div>
