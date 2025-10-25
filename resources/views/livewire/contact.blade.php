<div>
    <div class="relative flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-hidden">
        <div class="layout-container flex h-full grow flex-col">
            @include('partials.header')

            <main class="flex-1 px-4 sm:px-6 lg:px-24 pt-8 pb-12">
                <div class="mx-auto max-w-4xl">

                    <!-- Page Header -->
                    <div class="text-center mb-12">
                        <h1 class="text-4xl font-bold text-slate-900 mb-4">Contact Us</h1>
                        <p class="text-lg text-slate-600">Get in touch with our team</p>
                    </div>

                    <!-- Contact Information -->
                    <div class="bg-white rounded-lg shadow-sm border p-8">
                        @if(!empty($contactInfo['description']))
                            <div class="mb-8 text-center">
                                <p class="text-slate-700 leading-relaxed text-lg">{{ $contactInfo['description'] }}</p>
                            </div>
                        @endif

                        <div class="grid md:grid-cols-2 gap-8">
                            <!-- Contact Details -->
                            <div class="space-y-6">
                                <div class="text-center md:text-left">
                                    <h2 class="text-2xl font-semibold text-slate-900 mb-6">Get in Touch</h2>
                                </div>

                                @if(!empty($contactInfo['email']))
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <span class="material-symbols-outlined text-[#1173d4] text-2xl">mail</span>
                                        </div>
                                        <div>
                                            <h3 class="font-medium text-slate-900">Email</h3>
                                            <a href="mailto:{{ $contactInfo['email'] }}" class="text-[#1173d4] hover:underline">
                                                {{ $contactInfo['email'] }}
                                            </a>
                                        </div>
                                    </div>
                                @endif

                                @if(!empty($contactInfo['phone']))
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <span class="material-symbols-outlined text-[#1173d4] text-2xl">phone</span>
                                        </div>
                                        <div>
                                            <h3 class="font-medium text-slate-900">Phone</h3>
                                            <a href="tel:{{ $contactInfo['phone'] }}" class="text-[#1173d4] hover:underline">
                                                {{ $contactInfo['phone'] }}
                                            </a>
                                        </div>
                                    </div>
                                @endif

                                @if(!empty($contactInfo['address']))
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <span class="material-symbols-outlined text-[#1173d4] text-2xl">location_on</span>
                                        </div>
                                        <div>
                                            <h3 class="font-medium text-slate-900">Address</h3>
                                            <p class="text-slate-700 whitespace-pre-line">{{ $contactInfo['address'] }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Contact Hours or Additional Info -->
                            <div class="bg-slate-50 rounded-lg p-6">
                                <h3 class="text-xl font-semibold text-slate-900 mb-4">Business Hours</h3>
                                <div class="space-y-2 text-slate-700">
                                    <div class="flex justify-between">
                                        <span>Monday - Friday</span>
                                        <span>9:00 AM - 6:00 PM</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Saturday</span>
                                        <span>9:00 AM - 4:00 PM</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Sunday</span>
                                        <span>Closed</span>
                                    </div>
                                </div>

                                <div class="mt-6 pt-6 border-t border-slate-200">
                                    <h4 class="font-medium text-slate-900 mb-2">Emergency Contact</h4>
                                    <p class="text-sm text-slate-600">
                                        For urgent matters outside business hours, please call our emergency line.
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if(empty($contactInfo['email']) && empty($contactInfo['phone']) && empty($contactInfo['address']) && empty($contactInfo['description']))
                            <div class="text-center py-8">
                                <p class="text-slate-600">Contact information is being updated. Please check back later.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
