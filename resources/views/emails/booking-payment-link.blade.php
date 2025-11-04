<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Required - Booking #{{ $booking->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .booking-details {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .payment-button {
            text-align: center;
            margin: 30px 0;
        }
        .payment-button a {
            background: #28a745;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            font-weight: bold;
        }
        .payment-button a:hover {
            background: #218838;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Payment Required</h1>
        <p>Your booking has been confirmed by our admin and requires payment to complete</p>
    </div>

    <div class="booking-details">
        <h2>Booking Details</h2>
        <p><strong>Booking ID:</strong> #{{ $booking->id }}</p>
        <p><strong>Car:</strong> {{ $booking->car->name ?? 'N/A' }}</p>
        <p><strong>Pickup Location:</strong> {{ $booking->pickup_location }}</p>
        <p><strong>Drop-off Location:</strong> {{ $booking->dropoff_location }}</p>
        <p><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($booking->start_date)->format('M d, Y') }}</p>
        <p><strong>End Date:</strong> {{ \Carbon\Carbon::parse($booking->end_date)->format('M d, Y') }}</p>
        <p><strong>Customer:</strong> {{ $booking->user->name ?? 'N/A' }}</p>

        <hr style="margin: 20px 0;">

        <p><strong>Amount Due:</strong> <span class="amount">₦{{ number_format($booking->total, 2) }}</span></p>
    </div>

    <div class="payment-button">
        <a href="{{ $paymentUrl }}" target="_blank">Pay Now via Paystack</a>
    </div>

    <p>Dear {{ $booking->user->name ?? 'Customer' }},</p>

    <p>Your booking request has been reviewed and confirmed by our admin team. However, we noticed that your wallet balance was insufficient to cover the booking amount.</p>

    <p>To complete your booking, please click the "Pay Now" button above to securely pay the remaining balance via Paystack. Once payment is confirmed, your booking will be automatically activated.</p>

    <p><strong>Important:</strong> This payment link is secure and will redirect you to Paystack's payment gateway. Do not share this link with anyone else.</p>

    <div class="footer">
        <p>If you have any questions or concerns, please don't hesitate to contact our support team.</p>
        <p>Thank you for choosing our car rental service!</p>
        <p><em>This is an automated email, please do not reply directly to this message.</em></p>
    </div>
</body>
</html>
