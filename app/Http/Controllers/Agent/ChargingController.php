<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChargingController extends Controller
{
    public function index()
{
    $bookings = Booking::where('status', 'assigned_to_charging')
        ->get()
        ->filter(function($booking) {
            // Decode the charging_remarks to check assignment
            $assignment = json_decode($booking->charging_remarks, true);
            return $assignment && ($assignment['charger_id'] ?? null) == auth()->id();
        });
    
    // Manual pagination
    $perPage = 20;
    $page = request()->get('page', 1);
    $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
        $bookings->forPage($page, $perPage),
        $bookings->count(),
        $perPage,
        $page,
        ['path' => request()->url()]
    );
    
    return view('charge.bookings.index', compact('paginated'));
}

public function show(Booking $booking)
{
    $assignment = json_decode($booking->charging_remarks, true);
    
    if ($booking->status !== 'assigned_to_charging' || 
        !$assignment || 
        ($assignment['charger_id'] ?? null) != auth()->id()) {
        abort(403);
    }
    
    $booking->load(['user', 'passengers', 'segments', 'cards']);
    return view('charge.bookings.show', compact('booking'));
}
    public function chargebyagent(Booking $booking)
    {
        // Only allow charging for agent-owned bookings with pending cards
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $pendingCards = $booking->cards->where('payment_status', 'pending');
        if ($pendingCards->isEmpty()) {
            return back()->with('error', 'No pending payments to charge.');
        }

        $merchants = Merchant::where('is_active', true)->get();

        return view('agent.charging.charge', compact('booking', 'pendingCards', 'merchants'));
    }

    // In your assignForCharging method
    public function assignForCharging(Request $request, Booking $booking)
    {
        if ($booking->user_id !== auth()->id() || $booking->status !== 'pending') {
            abort(403);
        }

        $request->validate([
            'merchant' => 'required|exists:merchants,id',
        ]);

        $merchant = \App\Models\Merchant::findOrFail($request->merchant);

        $charger = \App\Models\User::where('role', 'charge')
            ->inRandomOrder()
            ->first();

        if (! $charger) {
            return back()->withErrors(['merchant' => 'No charging team member is available!']);
        }

        // Store assignment in charging_remarks (JSON format)
        $assignmentData = [
            'charger_id' => $charger->id,
            'charger_name' => $charger->name,
            'assigned_at' => now()->toDateTimeString(),
            'merchant_id' => $merchant->id,
            'merchant_name' => $merchant->name,
            'status' => 'pending',
        ];

        $booking->update([
            'status' => 'assigned_to_charging',
            'charging_remarks' => json_encode($assignmentData), // Store in existing field
            'merchant_name' => $merchant->name,
        ]);

        return back()->with('success', 'Booking sent to '.$charger->name.' | Merchant: '.$merchant->name);

           // Store in cache (expires in 24 hours)
    \Cache::put('booking_assign_' . $booking->id, [
        'charger_id' => $charger->id,
        'assigned_at' => now(),
        'merchant_name' => $merchant->name,
    ], now()->addHours(24));
    
    $booking->update([
        'status' => 'assigned_to_charging',
        'merchant_name' => $merchant->name,
    ]);
    
    return back()->with('success', 'Booking sent to ' . $charger->name);
    }

}
