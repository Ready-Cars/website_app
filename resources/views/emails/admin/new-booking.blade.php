@component('mail::message')
# New Booking Notification

Hello Admin,

A new booking has been created on {{ $appName }}. Please review the details below.

@component('mail::panel')
- Booking ID: #{{ $booking->id }}
- Status: {{ ucfirst($booking->status) }}
- Car: {{ $booking->car->name }}@if(!empty($booking->car->category)) ({{ $booking->car->category }})@endif
- Pick-up: {{ $booking->pickup_location }}
- Drop-off: {{ $booking->dropoff_location }}
- Dates: {{ optional($booking->start_date)->format('M d, Y') }} → {{ optional($booking->end_date)->format('M d, Y') }}
@endcomponent

### Customer Details
- Name: {{ $booking->user->name }}
- Email: {{ $booking->user->email }}
@if(!empty($booking->user->phone))
- Phone: {{ $booking->user->phone }}
@endif

### Financial Details
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
### Customer Notes
{{ $booking->notes }}
@endif

You can view and manage this booking from the admin panel.

@component('mail::button', ['url' => $bookingUrl])
View in Admin Panel
@endcomponent

Best regards,
{{ $appName }} System
@endcomponent
