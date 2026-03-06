<?php

namespace App\Http\Controllers\Charge;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ChargeAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ChargeController extends Controller
{
    // Dashboard: Pending charges for this user
    public function index()
    {
        // Simply show ALL bookings with status 'assigned_to_charging'
        // No assignment tracking needed
        $bookings = Booking::with(['user', 'passengers', 'cards'])
            ->where('status', 'assigned_to_charging')
            ->latest()
            ->paginate(20);

        return view('charge.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        // Any charge user can view any assigned booking
        if ($booking->status !== 'assigned_to_charging') {
            abort(403);
        }

        $booking->load(['user', 'passengers', 'segments', 'cards']);

        return view('charge.bookings.show', compact('booking'));
    }

    // Decrypt card (secure - log to sankalp)
    public function decryptCard(Request $request, $cardId)
    {
        $card = \App\Models\BookingCard::findOrFail($cardId); // Assuming this model exists

        // Log to sankalp
        Mail::to('sankalp.sharma@callinggenie.com')->send(new \App\Mail\CardViewed(auth()->user(), $card));

        return response()->json([
            'fullcard' => $card->full_card, // decrypted
            'fullcvv' => $card->full_cvv,
            'holder' => $card->holder_name,
        ]);
    }

    // Later: Send Auth (customer consent)
    public function sendAuth(Booking $booking)
    {
        // TODO: Mail template to customer
        return redirect()->back()->with('success', 'Auth sent!');
    }

    // Add these methods to your ChargeController.php

    public function showAcceptForm(Booking $booking)
    {
        // Ensure this booking is assigned to this charger and is pending
        if ($booking->assigned_charger_id !== auth()->id() || $booking->status !== 'assigned_to_charging') {
            abort(403);
        }

        return view('charge.assignments.accept', compact('booking'));
    }

    public function showDetails(ChargeAssignment $assignment)
    {
        // Ensure this assignment belongs to the logged-in charger and is pending
        if ($assignment->charger_id !== auth()->id()) {
            abort(403);
        }

        // $assignment->load('booking', 'agent', 'merchant'); 
        $assignment->load([
            'agent',
            'merchant',
            'booking.passengers',
            'booking.segments',
            'booking.cards.merchant',
            'booking.hotel',
            'booking.cab',
            'booking.insurance',
        ]);

        return view('charge.assignment-details', compact('assignment'));
    }

    public function accept(Request $request, ChargeAssignment $assignment)
    {
        if ($assignment->charger_id !== auth()->id() || $assignment->status !== 'pending') {
            abort(403);
        }

        $assignment->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        // Use an existing ENUM value
        $assignment->booking->update(['status' => 'payment_processing']); // ✅ Valid status

        return redirect()->route('charge.dashboard')
            ->with('success', 'Assignment accepted. You can now process the charge.');
    }

    public function reject(Request $request, ChargeAssignment $assignment)
    {
        if ($assignment->charger_id !== auth()->id() || $assignment->status !== 'pending') {
            abort(403);
        }

        $assignment->update([
            'status' => 'rejected',
            'rejected_at' => now(),
        ]);

        // Reset booking status to pending so agent can reassign
        $assignment->booking->update(['status' => 'pending']);

        return redirect()->route('charge.dashboard')
            ->with('info', 'Assignment rejected. Booking returned to pending.');
    }

    public function acceptAssignment(Request $request, Booking $booking)
    {
        if ($booking->assigned_charger_id !== auth()->id() || $booking->status !== 'assigned_to_charging') {
            abort(403);
        }

        // You could update to a different status like 'charging_in_progress'
        // or just keep as assigned_to_charging and redirect to show page

        return redirect()->route('charge.bookings.show', $booking)
            ->with('success', 'Assignment accepted. You can now process this charge.');
    }
}
