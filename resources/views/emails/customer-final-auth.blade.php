<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        /* Embed all your premium CSS here to ensure it travels with the email */
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .email-container { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .header { background: #1a237e; color: #ffffff; padding: 30px; text-align: center; }
        .content { padding: 30px; line-height: 1.6; color: #333; }
        .btn-box { text-align: center; margin: 30px 0; }
        .approve-btn { background-color: #1a237e; color: #ffffff !important; padding: 15px 35px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block; font-size: 18px; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div @class(['email-container'])>
        <div @class(['header'])>
            <h1 style="margin:0; font-size:24px;">PAYMENT AUTHORIZATION</h1>
            <p style="margin:5px 0 0; opacity:0.8;">Booking Reference: {{ $booking->booking_reference }}</p>
        </div>

        <div @class(['content'])>
            <!-- This renders the custom HTML edited by the charger -->
            {!! $emailBody !!}

<p style="font-size:14px; line-height:24px; color:#cbd5e1;">
    Kindly reply to this email with your acknowledgement to confirm that you authorize
    the above booking and payment charges.
</p>

<p style="font-size:14px; line-height:24px; color:#ffffff; font-weight:700;">
    You may reply with:
    "I acknowledge and authorize this booking and the related payment charges."
</p>

        </div>

        <div @class(['footer'])>
            &copy; 2026 Travelomile. All Rights Reserved.<br>
            If you did not request this booking, please contact us immediately.
        </div>
    </div>
</body>
</html>
