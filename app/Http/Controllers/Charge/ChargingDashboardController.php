<?php

namespace App\Http\Controllers\Charge;

use App\Http\Controllers\Controller;
use App\Models\ChargeAssignment;
use Illuminate\Http\Request;

class ChargingDashboardController extends Controller
{
    public function index()
    {
        $chargerId = auth()->id();
        
        // If not logged in, redirect to login
        if (!$chargerId) {
            return redirect()->route('login');
        }

        // Get ALL assignments for this charger (pending, accepted, etc.)
        // For the table, let's show assignments that are not rejected
        $assignments = ChargeAssignment::with(['booking', 'agent', 'merchant'])
            ->where('charger_id', $chargerId)
            ->whereIn('status', ['pending', 'accepted']) // Show both pending and accepted
            ->latest()
            ->paginate(10);

        // Count only pending for the notification badge
        $pendingCount = ChargeAssignment::where('charger_id', $chargerId)
            ->where('status', 'pending')
            ->count();

        // Get the latest pending assignment for popup (if any)
        $latestPending = ChargeAssignment::with(['booking', 'agent'])
            ->where('charger_id', $chargerId)
            ->where('status', 'pending')
            ->latest()
            ->first();

        return view('charge.dashboard', compact(
            'assignments',     // For the table
            'pendingCount',    // For the badge
            'latestPending'    // For the popup
        ));
    }
}