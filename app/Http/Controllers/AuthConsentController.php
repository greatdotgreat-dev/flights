<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;


class AuthConsentController extends Controller
{
    public function edit($id)
    {
        // Load the booking with relationships
            $booking = Booking::with(['passengers', 'cards', 'segments'])->findOrFail($id);

        
        // Generate the initial HTML content from your email template
        // This passes the $booking object to emails.booking-auth-template
        $emailContent = view('emails.booking-auth-template', compact('booking'))->render();

        // Pass both the object and the rendered HTML to the edit page
        return view('charge.auth.edit', compact('booking', 'emailContent'));
    }

    public function preview(Request $request, $id)
{
    // Load booking with segments for the premium itinerary display
    $booking = Booking::with(['segments', 'cards', 'passengers'])->findOrFail($id);
    
    // Catch the HTML from the CKEditor
    $finalContent = $request->input('email_body');

    return view('charge.auth.preview', compact('booking', 'finalContent'));
}

// send email directly to the customers after using the preview page 
public function send(Request $request, $id)
{
    $booking = Booking::findOrFail($id);
    
    // 1. Generate the Secure Signed Link for the button
    $authLink = URL::temporarySignedRoute(
        'customer.consent.view', 
        now()->addHours(48), 
        ['id' => $booking->id]
    );

    // 2. Get the HTML content from the editor/preview
    $emailBody = $request->input('final_content');

    // 3. Render the full premium email wrapper with the content and link
    $finalHtml = view('emails.customer-final-auth', [
        'booking' => $booking,
        'emailBody' => $emailBody,
        'authLink' => $authLink
    ])->render();

    // 4. Send the HTML Mail
    Mail::html($finalHtml, function ($message) use ($booking) {
        $message->to($booking->customer_email)
                ->subject('Review & Authorize: Your Flight Booking ' . $booking->booking_reference)
                ->from(config('mail.from.address'), 'Travelomile Reservation');
    });

    // 5. Status Tracking
    $booking->update(['status' => 'authemailsent', 'auth_email_sent_at' => now()]);

    return redirect()->route('charge.dashboard')->with('success', 'Premium authorization mail sent.');


    // 5. Update Status
    $booking->update([
        'status' => 'authemailsent', 
        'auth_email_sent_at' => now()
    ]);

    return redirect()->route('charge.dashboard')->with('success', 'Authorization mail sent successfully.');
}
}


