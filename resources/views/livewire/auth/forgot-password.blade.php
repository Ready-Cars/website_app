 <div class="flex flex-col md:flex-row min-h-screen">
     <!-- Illustration / Brand side -->
     <div class="w-full md:w-1/2 bg-[#0e1133] flex flex-col justify-center items-center p-8 lg:p-12 relative">
         <a href="{{ route('home') }}" class="absolute top-8 left-8 inline-flex items-center" aria-label="{{ config('app.name') }}" wire:navigate>
             <img src="https://readycars.ng/img/logo.png" alt="{{ config('app.name') }} logo" class="h-9 w-auto object-contain" />
         </a>
         <div class="max-w-md text-center">
             <img alt="Password reset illustration" class="w-full h-auto" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBS6i6QbUofp_vAMhhBp0F-ABDnSVWzSQpV6HLuYgeN8cBGGrmmrTbgIUReuOJOSpFgfTd2Y1Np_FMzYah_k9H2gtiniAKhoezOssdCxYWA9SI0Z1jG3hae9pNi60vGqSTrmGPlnRT5QLv2BIl694t_MqzAQO2tCV5gk0RFOccZ2rWCawh6Mqu89ZIM9Q5aO4gIIf_BBqdSg90xhAM-70GawDrpmkaoRFH-oRQjVfSimxpbZGc3hfI14xX7WI1g4L-nED1RvLPx85c0"/>
             <h2 class="text-3xl font-bold text-white mt-8">Reset your password</h2>
             <p class="text-white/80 mt-4">Enter your email address and we’ll send you a link to reset your password.</p>
         </div>
     </div>

     <!-- Form side -->
     <div class="w-full md:w-1/2 flex items-center justify-center p-8 lg:p-16 bg-white">
         <div class="w-full max-w-md space-y-8">
             <div>
                 <h2 class="text-3xl font-bold text-gray-900">Forgot password</h2>
                 <p class="mt-2 text-sm text-gray-600">
                     Remembered it?
                     <a class="font-medium text-[#1173d4] hover:text-[#0f63b9]" href="{{ route('login') }}" wire:navigate>Back to log in</a>
                 </p>
             </div>

             <!-- Session Status -->
             <x-auth-session-status class="text-center" :status="session('status')" />

             <form method="POST" wire:submit="sendPasswordResetLink" class="mt-4 space-y-6">
                 <div class="space-y-4">
                     <div class="relative">
                         <label class="sr-only" for="email">Email address</label>
                         <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                             <span class="material-symbols-outlined text-gray-400">person</span>
                         </div>
                         <input id="email" name="email" type="email" autocomplete="email" wire:model.defer="email" required autofocus
                                class="relative block w-full appearance-none rounded-md border border-gray-300 px-3 py-4 pl-10 text-gray-900 placeholder-gray-500 focus:z-10 focus:border-[#1173d4] focus:outline-none focus:ring-[#1173d4] sm:text-sm"
                                placeholder="Email address">
                         @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                     </div>
                 </div>
                 <div>
                     <button type="submit" class="group relative flex w-full justify-center rounded-md bg-[#1173d4] py-3 px-4 text-sm font-semibold text-white hover:bg-[#0f63b9] focus:outline-none focus:ring-2 focus:ring-[#1173d4] focus:ring-offset-2">Email password reset link</button>
                 </div>
             </form>

             <p class="text-center text-sm text-gray-500">© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
         </div>
     </div>
 </div>
