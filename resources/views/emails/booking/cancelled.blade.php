@component('mail::message')
# Booking Cancelled

Hi {{ $booking->user->name }},

Your reservation with {{ $appName }} has been cancelled.

@component('mail::panel')
- Car: {{ $booking->car->name }}
- Original dates: {{ optional($booking->start_date)->format('M d, Y') }} → {{ optional($booking->end_date)->format('M d, Y') }}
- Reason: {{ $booking->cancellation_reason ?: 'Not specified' }}
@endcomponent

You can view your trips history at any time.

@component('mail::button', ['url' => $tripsUrl])
View My Trips
@endcomponent

Regards,
{{ $appName }}
@endcomponent
