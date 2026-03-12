<?php

namespace App\Http\Controllers\Charge;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ChargeBookingStatusController extends Controller
{
    public function update(Request $request, $id)
    {
        $allowedStatuses = [
            'payment_processing',
            'confirmed',
            'ticketed',
            'failed',
            'cancelled',
            'hold',
            'refund',
            'charging_in_progress',
        ];

        $request->validate([
            'status' => ['required', Rule::in($allowedStatuses)],
        ]);

        $booking = Booking::findOrFail($id);

        if (!in_array($booking->status, ['auth_email_sent', 'payment_processing', 'charging_in_progress', 'confirmed', 'ticketed', 'failed', 'cancelled', 'hold', 'refund'])) {
            return redirect()->back()->with('error', 'This booking is not ready for charge status update.');
        }

        $booking->update([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Booking status updated successfully to ' . str_replace('_', ' ', $request->status) . '.');
    }
}
