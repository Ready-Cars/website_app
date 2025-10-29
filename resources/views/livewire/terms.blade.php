<div>
    <div class="relative flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-hidden">
        <div class="layout-container flex h-full grow flex-col">

            <main class="flex-1 px-4 sm:px-6 lg:px-24 pt-8 pb-12">
                <div class="mx-auto max-w-4xl">

                    <!-- Page Header -->
                    <div class="text-center mb-12">
                        <h1 class="text-4xl font-bold text-slate-900 mb-4">Terms and Conditions</h1>
                        <p class="text-lg text-slate-600">Car Rental Services Agreement</p>
                        <p class="text-sm text-slate-500 mt-2">Last Updated: {{ date('F j, Y') }}</p>
                    </div>

                    <!-- Terms Content -->
                    <div class="bg-white rounded-lg shadow-sm border p-8 space-y-8">

                        <!-- Introduction -->
                        <section>
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">1. Agreement Overview</h2>
                            <p class="text-slate-700 leading-relaxed mb-4">
                                These Terms and Conditions ("Agreement") govern your use of {{ config('app.name') }}'s car rental services. By renting a vehicle from us, you agree to be bound by these terms.
                            </p>
                        </section>

                        <!-- Eligibility -->
                        <section>
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">2. Rental Eligibility</h2>
                            <div class="text-slate-700 leading-relaxed space-y-3">
                                <p><strong>Age Requirements:</strong> You must be at least 21 years old to rent a vehicle. Drivers under 25 may be subject to additional fees.</p>
                                <p><strong>License:</strong> You must possess a valid driver's license that has been held for at least one year.</p>
                                <p><strong>Credit Requirements:</strong> A valid credit card in the renter's name is required for security deposit.</p>
                            </div>
                        </section>

                        <!-- Rental Terms -->
                        <section>
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">3. Rental Terms</h2>
                            <div class="text-slate-700 leading-relaxed space-y-3">
                                <p><strong>Booking and Payment:</strong> Full payment is required at the time of booking. Cancellations made 24 hours before rental start time are eligible for full refund.</p>
                                <p><strong>Security Deposit:</strong> A security deposit will be held on your credit card during the rental period and released upon satisfactory return of the vehicle.</p>
                                <p><strong>Fuel Policy:</strong> Vehicles are provided with a full tank of fuel and must be returned with a full tank, or refueling charges will apply.</p>
                            </div>
                        </section>

                        <!-- Vehicle Use -->
                        <section>
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">4. Vehicle Use Restrictions</h2>
                            <div class="text-slate-700 leading-relaxed">
                                <p class="mb-3">The rental vehicle may not be used for:</p>
                                <ul class="list-disc list-inside space-y-2 ml-4">
                                    <li>Commercial purposes, ride-sharing, or taxi services</li>
                                    <li>Racing, contests, or driving instruction</li>
                                    <li>Transportation of hazardous materials</li>
                                    <li>Off-road driving or on unpaved surfaces</li>
                                    <li>Towing other vehicles or trailers</li>
                                    <li>Any illegal activities</li>
                                </ul>
                            </div>
                        </section>

                        <!-- Insurance and Liability -->
                        <section>
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">5. Insurance and Liability</h2>
                            <div class="text-slate-700 leading-relaxed space-y-3">
                                <p><strong>Required Coverage:</strong> You must maintain valid auto insurance that covers rental vehicles. Proof of insurance is required.</p>
                                <p><strong>Additional Coverage:</strong> Optional collision damage waiver and supplemental liability insurance are available for additional fees.</p>
                                <p><strong>Renter Responsibility:</strong> You are responsible for all damages, theft, and traffic violations during the rental period.</p>
                            </div>
                        </section>

                        <!-- Damages and Fees -->
                        <section>
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">6. Damages and Additional Fees</h2>
                            <div class="text-slate-700 leading-relaxed space-y-3">
                                <p><strong>Late Return:</strong> Late return fees apply for vehicles returned after the agreed time.</p>
                                <p><strong>Cleaning Fees:</strong> Additional charges apply if the vehicle is returned excessively dirty or with odors.</p>
                                <p><strong>Smoking:</strong> A minimum $250 cleaning fee applies for smoking in rental vehicles.</p>
                                <p><strong>Traffic Violations:</strong> All parking tickets, tolls, and traffic violations are the renter's responsibility.</p>
                            </div>
                        </section>

                        <!-- Breakdown and Emergency -->
                        <section>
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">7. Breakdown and Emergency Procedures</h2>
                            <div class="text-slate-700 leading-relaxed space-y-3">
                                <p><strong>24/7 Support:</strong> Contact our emergency hotline immediately in case of breakdown, accident, or theft.</p>
                                <p><strong>Unauthorized Repairs:</strong> Do not authorize repairs without prior approval. Emergency repairs under $75 may be reimbursed with receipts.</p>
                                <p><strong>Accidents:</strong> Report all accidents to police and our office immediately, regardless of severity.</p>
                            </div>
                        </section>

                        <!-- Privacy and Data -->
                        <section>
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">8. Privacy and Data Protection</h2>
                            <div class="text-slate-700 leading-relaxed space-y-3">
                                <p><strong>Data Collection:</strong> We collect personal information necessary for rental services and legal compliance.</p>
                                <p><strong>GPS Tracking:</strong> Vehicles may be equipped with GPS tracking for theft prevention and recovery.</p>
                                <p><strong>Information Sharing:</strong> Personal information may be shared with authorities, insurance companies, and collection agencies as required.</p>
                            </div>
                        </section>

                        <!-- Termination -->
                        <section>
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">9. Rental Termination</h2>
                            <div class="text-slate-700 leading-relaxed space-y-3">
                                <p>We reserve the right to terminate the rental agreement and repossess the vehicle if:</p>
                                <ul class="list-disc list-inside space-y-2 ml-4">
                                    <li>Terms of this agreement are violated</li>
                                    <li>The vehicle is abandoned or used illegally</li>
                                    <li>False information was provided during booking</li>
                                    <li>Payment is declined or insufficient</li>
                                </ul>
                            </div>
                        </section>

                        <!-- Contact Information -->
                        <section>
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">10. Contact Information</h2>
                            <div class="text-slate-700 leading-relaxed space-y-3">
                                <p><strong>Customer Service:</strong> Available 24/7 for assistance and emergencies</p>
                                <p><strong>Emergency Hotline:</strong> Call immediately in case of accidents or breakdowns</p>
                                <p class="text-sm text-slate-600 mt-6">
                                    By proceeding with your rental, you acknowledge that you have read, understood, and agree to these Terms and Conditions.
                                </p>
                            </div>
                        </section>

                    </div>
                </div>
            </main>
            @include('partials.footer')
        </div>
    </div>
</div>
