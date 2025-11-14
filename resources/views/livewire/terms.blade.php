<div>
    <div class="relative flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-hidden">
        <div class="layout-container flex h-full grow flex-col">

            <main class="flex-1 px-4 sm:px-6 lg:px-24 pt-8 pb-12">
                <div class="mx-auto max-w-4xl">

                    <!-- Page Header -->
                    <div class="text-center mb-12">
                        <h1 class="text-4xl font-bold text-slate-900 mb-4">ReadyCars Terms and Conditions</h1>
                        <p class="text-lg text-slate-600">Transportation Services Agreement</p>
                        <p class="text-sm text-slate-500 mt-2">Last Updated: {{ date('F j, Y') }}</p>
                    </div>

                    <!-- Terms Content -->
                    <div class="bg-white rounded-lg shadow-sm border p-8 space-y-8">

                        <!-- Introduction -->
                        <section>
                            <p class="text-slate-700 leading-relaxed mb-6">
                                ReadyCars is a web-based platform for booking transportation services. It acts as an intermediary by transmitting transportation service requests to registered transportation service providers who have been verified by ReadyCars.
                            </p>
                        </section>

                        <!-- About ReadyCars App -->
                        <section>
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">1. About ReadyCars App</h2>
                            <div class="text-slate-700 leading-relaxed space-y-3">
                                <p><strong>1.1</strong> The use of ReadyCars platform requires the installation of the software and the registration of a user account. During the installation of the ReadyCars app, the mobile number of the ReadyCars service user is linked to the respective user account and added to the database.</p>
                                <p><strong>1.2</strong> When using the ReadyCars platform, the user can choose whether they wish to pay in cash or via in-app payment for the transportation service to the driver. Once a payment option has been selected, the user cannot change it.</p>
                                <p><strong>1.3</strong> Any complaints regarding ReadyCars' services can be directed to our support team via email at <a href="mailto:nigeria@readycars.ng" class="text-blue-600 hover:text-blue-800">nigeria@readycars.ng</a>, through the ReadyCars app by using the support button, or by calling our support line at <a href="tel:+2349024166944" class="text-blue-600 hover:text-blue-800">+2349024166944</a> (available on weekdays from 09.00-17.00 W.A.T).</p>
                            </div>
                        </section>

                        <!-- Bookings Terms and Conditions -->
                        <section>
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">2. Bookings Terms and Conditions</h2>
                            <div class="text-slate-700 leading-relaxed space-y-3">
                                <p><strong>2.2</strong> ReadyCars, a subsidiary of ReadyFlow Services Ltd, provides premium car booking and car hiring services to meet corporate and personal needs in Nigeria.</p>
                                <p><strong>2.3</strong> All vehicles provided by ReadyCars come with trained and licensed drivers. Clients are not allowed to drive the vehicles.</p>
                                <p><strong>2.4</strong> Clients are required to provide accurate and complete information when booking a car. ReadyCars will not be liable for any delays or errors resulting from incorrect information provided by the client.</p>
                                <p><strong>2.5</strong> ReadyCars reserves the right to refuse service to any client without providing a reason.</p>
                                <p><strong>2.6</strong> The client is responsible for any damage to the vehicle caused by their negligence, intentional or unintentional actions, and will be required to pay for any damages or repairs necessary.</p>
                                <p><strong>2.7</strong> Clients are required to return the vehicle in the same condition in which it was provided. Any damage or loss to the vehicle or its contents will be charged to the client.</p>
                                <p><strong>2.8</strong> Clients are required to comply with all applicable laws and regulations when using ReadyCars' services, including but not limited to traffic laws and regulations.</p>
                                <p><strong>2.9</strong> ReadyCars reserves the right to cancel any booking without notice in the event of an emergency or force majeure, including but not limited to acts of God, war, or government intervention.</p>
                                <p><strong>2.10</strong> ReadyCars will not be liable for any loss, damage, or injury resulting from the use of its services, including but not limited to accidents, theft, or delay.</p>
                                <p><strong>2.11</strong> ReadyCars reserves the right to modify or update these terms and conditions at any time without prior notice.</p>
                            </div>
                        </section>

                        <!-- Customer Conditions -->
                        <section>
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">3. Customer Account Conditions</h2>
                            <div class="text-slate-700 leading-relaxed space-y-3">
                                <p class="mb-4">By registering an account with ReadyCars, a customer shall accept the following conditions:</p>
                                <p><strong>3.1</strong> ReadyCars shall have the right to add the personal data of the app user to the ReadyCars database and to forward the personal data to transportation service providers in accordance with ReadyCars' Privacy Policy (<a href="https://readycars.com/privacy/" class="text-blue-600 hover:text-blue-800" target="_blank">https://readycars.com/privacy/</a>).</p>
                                <p><strong>3.2</strong> ReadyCars shall have the right to make unilateral amendments to the Terms and Conditions and Privacy Policy and to relinquish the database to third parties. We may notify users of changes to the Terms and Conditions and Privacy Policy.</p>
                                <p><strong>3.3</strong> ReadyCars shall be entitled to transfer the database of personal data to third parties without prior notification of the app users. In case of a transfer of the business or the database, the rights and conditions arising from this license agreement shall be transferred as well.</p>
                                <p><strong>3.4</strong> ReadyCars shall be entitled to forward personal data and bank data to credit card and mobile payment intermediaries.</p>
                                <p><strong>3.5</strong> ReadyCars has the right to send marketing messages and authentication codes through SMS messages.</p>
                                <p><strong>3.6</strong> ReadyCars only encourages the use of two modes of payment: cash payment and in-app payment (card). ReadyCars bears no liability for damages that may occur outside the outlined acceptable payment methods.</p>
                            </div>
                        </section>

                        <!-- Agreement Acknowledgment -->
                        <section class="border-t pt-6">
                            <p class="text-sm text-slate-600">
                                By using ReadyCars services, you acknowledge that you have read, understood, and agree to these Terms and Conditions.
                            </p>
                        </section>

                    </div>
                </div>
            </main>
            @include('partials.footer')
        </div>
    </div>
</div>
