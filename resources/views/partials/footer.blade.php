@if(!session('is_from_app'))
    <footer class="border-t border-slate-200 bg-slate-100/95">
        <div class="mx-auto max-w-7xl px-6 sm:px-8 lg:px-12 py-14 md:py-16">
            <div class="grid grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-4 lg:gap-12">
                <div class="md:col-span-2 lg:col-span-1 lg:pr-8">
                    <a href="{{ route('home') }}" class="inline-flex items-center" wire:navigate>
                        <img src="{{ asset('img2.png') }}" alt="{{ config('app.name') }} logo" class="h-20 w-auto object-contain" />
                    </a>
                    <p class="mt-5 max-w-sm text-[1rem] leading-7 text-slate-600">
                        Reliable car rentals across major cities with transparent pricing and flexible booking.
                    </p>
                </div>

                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-800">Company</h3>
                    <ul class="mt-4 space-y-3">
                        <li><a class="text-base text-slate-600 transition-colors hover:text-[#1173d4]" href="#">About</a></li>
                        <li><a class="text-base text-slate-600 transition-colors hover:text-[#1173d4]" href="#">Careers</a></li>
                        <li><a class="text-base text-slate-600 transition-colors hover:text-[#1173d4]" href="#">Press</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-800">Support</h3>
                    <ul class="mt-4 space-y-3">
                        <li><a class="text-base text-slate-600 transition-colors hover:text-[#1173d4]" href="{{ route('contact.index') }}" wire:navigate>Contact Us</a></li>
                        <li><a class="text-base text-slate-600 transition-colors hover:text-[#1173d4]" href="#">FAQ</a></li>
                        <li><a class="text-base text-slate-600 transition-colors hover:text-[#1173d4]" href="{{ route('terms.index') }}" wire:navigate>Terms & Conditions</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-800">Contact Us</h3>
                    <ul class="mt-4 grid grid-cols-1 gap-y-3.5">
                        <li>
                            <a class="inline-flex items-center gap-2 whitespace-nowrap text-[0.95rem] leading-tight text-slate-600 transition-colors hover:text-[#1173d4]" href="https://wa.me/2349022072949" target="_blank" rel="noopener"><span class="font-semibold text-slate-700">IBADAN:</span><span class="font-normal tabular-nums">09022072949</span></a>
                        </li>
                        <li>
                            <a class="inline-flex items-center gap-2 whitespace-nowrap text-[0.95rem] leading-tight text-slate-600 transition-colors hover:text-[#1173d4]" href="https://wa.me/2349121448260" target="_blank" rel="noopener"><span class="font-semibold text-slate-700">LAGOS:</span><span class="font-normal tabular-nums">09121448260</span></a>
                        </li>
                        <li>
                            <a class="inline-flex items-center gap-2 whitespace-nowrap text-[0.95rem] leading-tight text-slate-600 transition-colors hover:text-[#1173d4]" href="https://wa.me/2347068413686" target="_blank" rel="noopener"><span class="font-semibold text-slate-700">ABEOKUTA:</span><span class="font-normal tabular-nums">07068413686</span></a>
                        </li>
                        <li>
                            <a class="inline-flex items-center gap-2 whitespace-nowrap text-[0.95rem] leading-tight text-slate-600 transition-colors hover:text-[#1173d4]" href="https://wa.me/2348106022024" target="_blank" rel="noopener"><span class="font-semibold text-slate-700">OSOGBO:</span><span class="font-normal tabular-nums">08106022024</span></a>
                        </li>
                        <li>
                            <a class="inline-flex items-center gap-2 whitespace-nowrap text-[0.95rem] leading-tight text-slate-600 transition-colors hover:text-[#1173d4]" href="https://wa.me/2348168098526" target="_blank" rel="noopener"><span class="font-semibold text-slate-700">ENUGU:</span><span class="font-normal tabular-nums">08168098526</span></a>
                        </li>
                        <li>
                            <a class="inline-flex items-center gap-2 whitespace-nowrap text-[0.95rem] leading-tight text-slate-600 transition-colors hover:text-[#1173d4]" href="https://wa.me/2349033437179" target="_blank" rel="noopener"><span class="font-semibold text-slate-700">ILORIN:</span><span class="font-normal tabular-nums">09033437179</span></a>
                        </li>
                        <li>
                            <a class="inline-flex items-center gap-2 whitespace-nowrap text-[0.95rem] leading-tight text-slate-600 transition-colors hover:text-[#1173d4]" href="https://wa.me/2349068012096" target="_blank" rel="noopener"><span class="font-semibold text-slate-700">AKURE:</span><span class="font-normal tabular-nums">09068012096</span></a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mt-12 border-t border-slate-200 pt-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <p class="text-[0.95rem] text-slate-500">© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                <div class="flex items-center gap-3 md:justify-end">
                    <a class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-300 text-slate-500 transition-all hover:border-[#1173d4] hover:text-[#1173d4]" href="https://www.facebook.com/share/1GjQa7Ne9h/" target="_blank" rel="noopener">
                        <span class="sr-only">Facebook</span>
                        <svg aria-hidden="true" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path clip-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" fill-rule="evenodd"></path>
                        </svg>
                    </a>
                    <a class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-300 text-slate-500 transition-all hover:border-[#1173d4] hover:text-[#1173d4]" href="https://x.com/readycarsng?s=09" target="_blank" rel="noopener">
                        <span class="sr-only">Twitter</span>
                        <svg aria-hidden="true" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.71v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"></path>
                        </svg>
                    </a>
                    <a class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-300 text-slate-500 transition-all hover:border-[#1173d4] hover:text-[#1173d4]" href="https://www.instagram.com/readycarsng?igsh=MXdxeDNnYW90ZXRieQ==" target="_blank" rel="noopener">
                        <span class="sr-only">Instagram</span>
                        <svg aria-hidden="true" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path clip-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.024.06 1.378.06 3.808s-.012 2.784-.06 3.808c-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.024.048-1.378.06-3.808.06s-2.784-.013-3.808-.06c-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.048-1.024-.06-1.378-.06-3.808s.012-2.784.06-3.808c.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 016.08 2.525c.636-.247 1.363-.416 2.427-.465C9.53 2.013 9.884 2 12.315 2zm-1.125 1.625h2.25c2.32 0 2.613.01 3.53.056.89.043 1.447.203 1.84.368.447.182.792.402 1.127.737.335.335.555.68.737 1.127.165.393.325.95.368 1.84.046.917.055 1.21.055 3.53s-.01 2.613-.056 3.53c-.043.89-.203 1.447-.368 1.84a3.32 3.32 0 01-.737 1.127 3.32 3.32 0 01-1.127.737c-.393.165-.95.325-1.84.368-.917.046-1.21.055-3.53.055s-2.613-.01-3.53-.056c-.89-.043-1.447-.203-1.84-.368a3.32 3.32 0 01-1.127-.737 3.32 3.32 0 01-.737-1.127c-.165-.393-.325-.95-.368-1.84-.046-.917-.055-1.21-.055-3.53s.01-2.613.056-3.53c.043-.89.203-1.447.368-1.84.182-.447.402-.792.737-1.127.335-.335.68-.555 1.127-.737.393-.165.95-.325 1.84-.368.917-.046 1.21-.055 3.53-.055z" fill-rule="evenodd"></path>
                            <path d="M12 8.25a3.75 3.75 0 100 7.5 3.75 3.75 0 000-7.5zM8.25 12a3.75 3.75 0 117.5 0 3.75 3.75 0 01-7.5 0z"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>
@endif
