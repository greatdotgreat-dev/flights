<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Preview Authorization Email | {{ $booking->booking_reference }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', Roboto, sans-serif; }
        .email-preview-container { 
            max-width: 700px; margin: 40px auto; background: white; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden;
        }
        .preview-header { background: #1a237e; color: white; padding: 20px; text-align: center; }
        .email-body-content { padding: 40px; }
        /* Premium Itinerary Card */
        .itinerary-card { 
            border: 1px solid #e0e0e0; border-radius: 12px; margin: 25px 0; 
            background: #fafafa; position: relative;
        }
        .itinerary-header { 
            background: #f1f3f9; padding: 10px 20px; border-bottom: 1px solid #e0e0e0;
            display: flex; justify-content: space-between; font-weight: bold; color: #1a237e;
        }
        .segment-row { padding: 20px; display: flex; align-items: center; justify-content: space-between; }
        .city-code { font-size: 14px; font-weight: 500; color: #333; margin: 0; }
        .city-name { font-size: 24px; color: #757575; text-transform: uppercase; }
        .flight-icon { color: #1a237e; font-size: 20px; padding: 0 15px; }
        .auth-box { 
            background: #fff9e6; border: 1px dashed #ffc107; padding: 20px; 
            border-radius: 8px; margin-top: 30px; text-align: center;
        }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="alert alert-info d-flex justify-content-between align-items-center shadow-sm">
        <span><strong>Review Mode:</strong> This is how the customer will see the email.</span>
        <div>
            <a href="{{ route('charge.authorize.edit', $booking->id) }}" class="btn btn-sm btn-outline-primary">Back to Editor</a>
        </div>
    </div>

    <div class="email-preview-container">
        <div class="preview-header">
            <h4 class="mb-0">PAYMENT AUTHORIZATION</h4>
            <small>Booking Ref: {{ $booking->booking_reference }}</small>
        </div>

        <div class="email-body-content">
            <!-- This outputs the edited content from CKEditor -->
            <div class="editable-content-area">
                {!! $finalContent !!}
            </div>

            <!-- Fixed Premium Elements (Non-Editable for Consistency) -->
            <div class="itinerary-card">
                <div class="itinerary-header">
                    <span>FLIGHT DETAILS</span>
                    <span>PNR: {{ $booking->gk_pnr }}</span>
                </div>
                @foreach($booking->segments as $segment)
                <div class="segment-row">
                    <div class="text-start">
                        <p class="city-code">{{ $segment->from_airport ?? 'DEPARTURE' }}</p>
                        <p class="city-name">{{ $segment->from_city }}</p>
                    </div>
                    <div class="flex-grow-1 text-center border-bottom mb-3 mx-3">
                        <span class="flight-icon">✈</span>
                    </div>
                    <div class="text-end">
                        <p class="city-code">{{ $segment->to_airport ?? 'ARRIVAL' }}</p>
                        <p class="city-name">{{ $segment->to_city }}</p>
                    </div>
                </div>
                <div class="px-3 pb-3 small text-muted d-flex justify-content-between">
                    <span>Flight: {{ $segment->airline_name }} {{ $segment->flight_number }}</span>
                    <span>Date: {{ \Carbon\Carbon::parse($segment->departure_date)->format('D, d M Y') }}</span>
                </div>
                @endforeach
            </div>

            <div class="auth-box">
                <p class="mb-0 small text-muted">By replying to this email or clicking the consent link, you authorize</p>
                <h5 class="my-2">{{ $booking->currency }} {{ number_format($booking->amount_charged, 2) }}</h5>
                <p class="small mb-0">Total Charge Amount (All Inclusive)</p>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="text-center mt-4">
        <form action="{{ route('charge.authorize.send', $booking->id) }}" method="POST">
            @csrf
            <input type="hidden" name="final_content" value="{{ $finalContent }}">
            <button type="submit" class="btn btn-primary btn-lg px-5 shadow">Confirm & Send to Customer</button>
        </form>
    </div>
</div>
</body>
</html>
    