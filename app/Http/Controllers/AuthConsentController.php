<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AuthConsentController extends Controller
{
    public function edit($id)
    {
        $booking = Booking::with(['passengers', 'cards', 'segments'])->findOrFail($id);
        $emailContent = view('emails.booking-auth-template', compact('booking'))->render();

        return view('charge.auth.edit', compact('booking', 'emailContent'));
    }

    public function preview(Request $request, $id)
    {
        $booking = Booking::with(['segments', 'cards', 'passengers'])->findOrFail($id);
        $finalContent = $request->input('email_body');

        return view('charge.auth.preview', compact('booking', 'finalContent'));
    }

    public function send(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->auth_email_sent_at || $booking->status === 'auth_email_sent') {
            return redirect()->back()->with('error', 'Auth mail has already been sent. Please use resend mail option.');
        }

        $emailBody = $request->input('final_content');

        $finalHtml = view('emails.customer-final-auth', [
            'booking' => $booking,
            'emailBody' => $emailBody,
        ])->render();

        Mail::html($finalHtml, function ($message) use ($booking) {
            $message->to($booking->customer_email)
                ->subject('Booking Acknowledgement Required: ' . $booking->booking_reference)
                ->from(config('mail.from.address'), 'Travelomile Reservation');
        });

        $booking->update([
            'status' => 'auth_email_sent',
            'auth_email_sent_at' => now(),
        ]);

        return redirect()->route('charge.dashboard')->with('success', 'Acknowledgement mail sent successfully.');
    }

    public function resend(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $emailBody = $request->input('final_content');

        if (!$emailBody) {
            $emailBody = view('emails.booking-auth-template', compact('booking'))->render();
        }

        $finalHtml = view('emails.customer-final-auth', [
            'booking' => $booking,
            'emailBody' => $emailBody,
        ])->render();

        Mail::html($finalHtml, function ($message) use ($booking) {
            $message->to($booking->customer_email)
                ->subject('Resent: Booking Acknowledgement Required - ' . $booking->booking_reference)
                ->from(config('mail.from.address'), 'Travelomile Reservation');
        });

        $booking->update([
            'status' => 'auth_email_sent',
            'auth_email_sent_at' => now(),
        ]);

        return redirect()->route('charge.dashboard')->with('success', 'Auth mail resent successfully.');
    }



}
