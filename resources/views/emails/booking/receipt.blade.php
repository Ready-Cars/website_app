<x-mail::message>
# Booking Receipt - {{ $appName }}

Dear {{ $booking->user->name }},

Thank you for your booking with {{ $appName }}! We're pleased to provide you with your booking receipt.

## Booking Details

**Booking Reference:** BKG-{{ sprintf('%05d', $booking->id) }}
**Vehicle:** {{ $booking->car->name }}
**Status:** {{ ucfirst($booking->status) }}
**Pick-up Date:** {{ $booking->start_date->format('F j, Y') }}
**Drop-off Date:** {{ $booking->end_date->format('F j, Y') }}
**Total Amount:** ₦{{ number_format($booking->total, 2) }}

## Receipt Information

Your detailed receipt has been attached to this email as a PDF document. The receipt contains:

- Complete booking details
- Itemized price breakdown
- Customer information
- Terms and conditions

Please keep this receipt for your records.

@if($booking->status === 'confirmed')
## Next Steps

Your booking has been confirmed! We'll contact you closer to your pick-up date with additional details about vehicle collection.
@elseif($booking->status === 'pending')
## Next Steps

Your booking request is currently being reviewed. We'll contact you shortly to confirm the final pricing and complete your reservation.
@endif

<x-mail::button :url="$bookingUrl">
View Booking Details
</x-mail::button>

If you have any questions about your booking or need to make changes, please don't hesitate to contact our customer service team.

Thank you for choosing {{ $appName }}!

Best regards,
The {{ $appName }} Team
</x-mail::message>
