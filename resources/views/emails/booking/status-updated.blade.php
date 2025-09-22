@component('mail::message')
# Booking Status Updated

Hi {{ $booking->user->name }},

Your booking status has changed from **{{ ucfirst($previousStatus) }}** to **{{ ucfirst($newStatus) }}** on {{ $appName }}.

@component('mail::panel')
- Booking ID: #{{ $booking->id }}
- Status: {{ ucfirst($booking->status) }}
- Car: {{ $booking->car->name }}@if(!empty($booking->car->category)) ({{ $booking->car->category }})@endif
- Pick-up: {{ $booking->pickup_location }}
- Drop-off: {{ $booking->dropoff_location }}
- Dates: {{ optional($booking->start_date)->format('M d, Y') }} → {{ optional($booking->end_date)->format('M d, Y') }}
@endcomponent

### Customer
- Name: {{ $booking->user->name }}
- Email: {{ $booking->user->email }}
@if(!empty($booking->user->phone))
- Phone: {{ $booking->user->phone }}
@endif

### Financials
- Subtotal: ₦{{ number_format((float) ($booking->subtotal ?? 0), 2) }}
- Taxes/Fees: ₦{{ number_format((float) ($booking->taxes ?? 0), 2) }}
- Total: ₦{{ number_format((float) $booking->total, 2) }}

@php $extras = (array)($booking->extras ?? []); @endphp
@if(!empty($extras))
### Extras
@foreach($extras as $k => $v)
    @if($v)
- {{ is_string($k) ? ucfirst(str_replace('_',' ', $k)) : (string)$k }}
    @endif
@endforeach
@endif

@if(!empty($booking->notes))
### Notes
{{ $booking->notes }}
@endif

@if(($booking->status ?? '') === 'cancelled' && !empty($booking->cancellation_reason))
### Cancellation reason
{{ $booking->cancellation_reason }}
@endif

View your booking details directly using the button below.

@component('mail::button', ['url' => $bookingUrl])
View Booking
@endcomponent

Thanks,
{{ $appName }}
@endcomponent
