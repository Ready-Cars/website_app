<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Instructions - Booking #{{ $booking->id }}</title>
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
            background-color: #3b82f6;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f8fafc;
            padding: 30px;
            border: 1px solid #e2e8f0;
        }
        .booking-details {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #3b82f6;
        }
        .payment-info {
            background-color: #fef3c7;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #f59e0b;
        }
        .account-details {
            background-color: #dcfce7;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #22c55e;
        }
        .highlight {
            font-weight: bold;
            font-size: 18px;
            color: #1f2937;
        }
        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #059669;
        }
        .footer {
            background-color: #374151;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 0 0 8px 8px;
            font-size: 14px;
        }
        ul {
            padding-left: 20px;
        }
        li {
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Payment Instructions</h1>
        <p>Booking #{{ $booking->id }} - Manual Payment Required</p>
    </div>

    <div class="content">
        <p>Dear {{ $booking->user->name }},</p>

        <p>Your booking has been confirmed and is ready for payment. Please follow the instructions below to complete your payment manually.</p>

        <div class="booking-details">
            <h3>🚗 Booking Details</h3>
            <ul>
                <li><strong>Booking ID:</strong> #{{ $booking->id }}</li>
                @if($booking->car)
                <li><strong>Car:</strong> {{ $booking->car->name }}</li>
                @endif
                <li><strong>Start Date:</strong> {{ $booking->start_date ? $booking->start_date->format('M d, Y') : 'TBD' }}</li>
                <li><strong>End Date:</strong> {{ $booking->end_date ? $booking->end_date->format('M d, Y') : 'TBD' }}</li>
                @if($booking->pickup_location)
                <li><strong>Pickup Location:</strong> {{ $booking->pickup_location }}</li>
                @endif
                @if($booking->dropoff_location)
                <li><strong>Dropoff Location:</strong> {{ $booking->dropoff_location }}</li>
                @endif
            </ul>
        </div>

        <div class="payment-info">
            <h3>💰 Payment Amount</h3>
            <div class="amount">₦{{ number_format($booking->total, 2) }}</div>
        </div>

        <div class="account-details">
            <h3>🏦 Bank Account Details</h3>
            <p class="highlight">Please transfer the exact amount to the following account:</p>
            <ul>
                <li><strong>Bank Name:</strong> {{ $bankName }}</li>
                <li><strong>Account Number:</strong> <span class="highlight">{{ $accountNumber }}</span></li>
                <li><strong>Amount:</strong> <span class="highlight">₦{{ number_format($booking->total, 2) }}</span></li>
            </ul>
        </div>

        <div style="background-color: #fef2f2; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ef4444;">
            <h3>📋 Important Instructions</h3>
            <ul>
                <li>Please use <strong>Booking #{{ $booking->id }}</strong> as your payment reference/narration</li>
                <li>Transfer the exact amount: <strong>₦{{ number_format($booking->total, 2) }}</strong></li>
                <li>After making the payment, please keep your payment receipt</li>
                <li>Your booking will be processed once payment is confirmed</li>
                <li>If you have any questions, please contact our support team</li>
            </ul>
        </div>

        <p style="margin-top: 30px;">Thank you for choosing our service. We look forward to serving you!</p>

        <p style="color: #6b7280; font-size: 14px; margin-top: 30px;">
            <strong>Note:</strong> This is an automated email. Please do not reply directly to this message.
        </p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html>
