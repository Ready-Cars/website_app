<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #1173d4;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #1173d4;
            font-size: 28px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .receipt-info div {
            flex: 1;
        }
        .receipt-number {
            text-align: right;
        }
        .customer-info, .booking-details {
            margin-bottom: 30px;
        }
        .section-title {
            background-color: #f8f9fa;
            padding: 10px;
            border-left: 4px solid #1173d4;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .info-row {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
            color: #666;
        }
        .info-value {
            flex: 1;
        }
        .price-breakdown {
            margin-top: 30px;
        }
        .price-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .price-row.total {
            border-top: 2px solid #1173d4;
            border-bottom: 2px solid #1173d4;
            font-weight: bold;
            font-size: 18px;
            margin-top: 10px;
            padding: 15px 0;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            @php
                $logoPath = public_path('img2.png');
                $logoBase64 = '';
                if (file_exists($logoPath)) {
                    $logoData = file_get_contents($logoPath);
                    $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
                }
            @endphp
            @if($logoBase64)
                <img src="{{ $logoBase64 }}" alt="{{ config('app.name') }} Logo" style="max-height: 80px; margin-bottom: 10px;">
            @else
                <div style="height: 80px; margin-bottom: 10px; text-align: center; color: #666;">{{ config('app.name') }}</div>
            @endif
        </div>
        <p>Booking Receipt</p>
    </div>

    <div class="receipt-info">
        <div>
            <strong>Receipt Date:</strong> {{ now()->format('F j, Y') }}<br>
            <strong>Receipt Time:</strong> {{ now()->format('g:i A') }}
        </div>
        <div class="receipt-number">
            <strong>Receipt #:</strong> {{ sprintf('RCT-%05d', $booking->id) }}<br>
            <strong>Booking #:</strong> {{ sprintf('BKG-%05d', $booking->id) }}
        </div>
    </div>

    <div class="customer-info">
        <div class="section-title">Customer Information</div>
        <div class="info-row">
            <div class="info-label">Name:</div>
            <div class="info-value">{{ $booking->user->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Email:</div>
            <div class="info-value">{{ $booking->user->email }}</div>
        </div>
    </div>

    <div class="booking-details">
        <div class="section-title">Booking Details</div>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value">
                <span class="status-badge status-{{ $booking->status }}">
                    {{ ucfirst($booking->status) }}
                </span>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Vehicle:</div>
            <div class="info-value">{{ $booking->car->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Category:</div>
            <div class="info-value">{{ $booking->car->category }} • {{ $booking->car->transmission }} • {{ $booking->car->seats }} seats</div>
        </div>
        <div class="info-row">
            <div class="info-label">Service Type:</div>
            <div class="info-value">{{ $booking->serviceType->name ?? 'Standard' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Pick-up Location:</div>
            <div class="info-value">{{ $booking->pickup_location }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Drop-off Location:</div>
            <div class="info-value">{{ $booking->dropoff_location }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Start Date:</div>
            <div class="info-value">{{ $booking->start_date->format('F j, Y') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">End Date:</div>
            <div class="info-value">{{ $booking->end_date->format('F j, Y') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Duration:</div>
            <div class="info-value">{{ $booking->start_date->diffInDays($booking->end_date) }} day(s)</div>
        </div>
        @if($booking->extras && count($booking->extras) > 0)
        <div class="info-row">
            <div class="info-label">Extras:</div>
            <div class="info-value">
                @php
                    $selectedExtras = array_filter($booking->extras);
                @endphp
                @if(count($selectedExtras) > 0)
                    {{ implode(', ', array_keys($selectedExtras)) }}
                @else
                    None
                @endif
            </div>
        </div>
        @endif
        @if($booking->notes)
        <div class="info-row">
            <div class="info-label">Notes:</div>
            <div class="info-value">{{ $booking->notes }}</div>
        </div>
        @endif
    </div>

    <div class="price-breakdown">
        <div class="section-title">Price Breakdown</div>
        <div class="price-row">
            <span>Daily Rate ({{ $booking->start_date->diffInDays($booking->end_date) }} days @ N{{ number_format($booking->car->daily_price, 2) }})</span>
            <span>N{{ number_format($booking->car->daily_price * $booking->start_date->diffInDays($booking->end_date), 2) }}</span>
        </div>
        @if($booking->extras && count(array_filter($booking->extras)) > 0)
        <div class="price-row">
            <span>Extras</span>
            <span>N{{ number_format($booking->subtotal - ($booking->car->daily_price * $booking->start_date->diffInDays($booking->end_date)), 2) }}</span>
        </div>
        @endif
        <div class="price-row">
            <span>Subtotal</span>
            <span>N{{ number_format($booking->subtotal, 2) }}</span>
        </div>
        @if($booking->taxes > 0)
        <div class="price-row">
            <span>Taxes</span>
            <span>N{{ number_format($booking->taxes, 2) }}</span>
        </div>
        @endif
        <div class="price-row total">
            <span>Total Amount</span>
            <span>N{{ number_format($booking->total, 2) }}</span>
        </div>
    </div>

    <div class="footer">
        <p>Thank you for choosing {{ config('app.name') }}!</p>
        <p>This is an automatically generated receipt. Please keep this for your records.</p>
        <p>For any questions, please contact our customer service.</p>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>
</body>
</html>
