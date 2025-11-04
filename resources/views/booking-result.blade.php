<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - Payment Result</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('home') }}" class="flex-shrink-0">
                            <h1 class="text-xl font-bold text-gray-900">{{ config('app.name') }}</h1>
                        </a>
                    </div>
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ route('trips.index') }}" class="text-gray-600 hover:text-gray-900">My Trips</a>
                            <a href="{{ route('wallet.index') }}" class="text-gray-600 hover:text-gray-900">Wallet</a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">Login</a>
                            <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Sign Up</a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <!-- Result Status -->
                <div class="text-center mb-8">
                    @if(session('success') || request('status') === 'success')
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">Payment Successful!</h1>
                        <p class="text-lg text-gray-600">Your booking has been confirmed and is now active.</p>
                    @else
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">Payment Failed</h1>
                        <p class="text-lg text-gray-600">There was an issue processing your payment. Please try again or contact support.</p>
                    @endif
                </div>

                <!-- Message from Controller -->
                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Booking Details (if available) -->
                @php
                    $booking = session('booking');
                @endphp
                @if($booking)
                    <div class="bg-white shadow rounded-lg overflow-hidden mb-8">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Booking Details</h2>
                        </div>
                        <div class="px-6 py-4 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Booking ID</dt>
                                    <dd class="mt-1 text-sm text-gray-900">#{{ $booking['id'] ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            @if(($booking['status'] ?? '') === 'confirmed') bg-green-100 text-green-800
                                            @elseif(($booking['status'] ?? '') === 'pending payment') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($booking['status'] ?? 'Unknown') }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Car</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $booking['car']['name'] ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Total Amount</dt>
                                    <dd class="mt-1 text-sm font-semibold text-gray-900">₦{{ number_format($booking['total'] ?? 0, 2) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Pickup Location</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $booking['pickup_location'] ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Drop-off Location</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $booking['dropoff_location'] ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Start Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        @if($booking['start_date'] ?? null)
                                            {{ \Carbon\Carbon::parse($booking['start_date'])->format('M d, Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">End Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        @if($booking['end_date'] ?? null)
                                            {{ \Carbon\Carbon::parse($booking['end_date'])->format('M d, Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </dd>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @auth
                        <a href="{{ route('trips.index') }}"
                           class="inline-flex justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            View My Trips
                        </a>
                        <a href="{{ route('wallet.index') }}"
                           class="inline-flex justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Check Wallet
                        </a>
                    @endauth
                    <a href="{{ route('home') }}"
                       class="inline-flex justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Back to Home
                    </a>
                </div>

                <!-- Support Contact -->
                @if(session('error'))
                    <div class="mt-8 text-center">
                        <p class="text-sm text-gray-600">
                            If you continue to experience issues, please
                            <a href="{{ route('contact.index') }}" class="text-blue-600 hover:text-blue-500">contact our support team</a>
                            for assistance.
                        </p>
                    </div>
                @endif
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <p class="text-center text-sm text-gray-500">
                    © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </p>
            </div>
        </footer>
    </div>
</body>
</html>
