@component('mail::message')
# Booking Confirmed

Hi {{ $booking->user->name }},

Your reservation has been confirmed with {{ $appName }}.

@component('mail::panel')
- Car: {{ $booking->car->name }}
- Pick-up: {{ $booking->pickup_location }}
- Drop-off: {{ $booking->dropoff_location }}
- Dates: {{ optional($booking->start_date)->format('M d, Y') }} → {{ optional($booking->end_date)->format('M d, Y') }}
- Total: ₦{{ number_format((float) $booking->total, 2) }}
@endcomponent

You can manage your trip from the link below.

@component('mail::button', ['url' => $tripsUrl])
View My Trips
@endcomponent

Thanks,
{{ $appName }}
@endcomponent
