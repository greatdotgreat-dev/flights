<?php

// app/Http/Controllers/BookingController.php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\CallType;
use App\Models\ChargeAssignment;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    // AGENT: List own bookings
    public function agentIndex()
    {
        $bookings = Booking::where('user_id', auth()->id())
            ->with(['user', 'passengers', 'cards'])
            ->latest()
            ->paginate(20);

        return view('agent.bookings.index', compact('bookings'));
    }

    /**
     * Display the Charging Team Dashboard.
     */
    public function chargeIndex()
    {

        // Returning to the view you mentioned in your dashboard snippet
        return view('charge.dashboard');
    }

    // AGENT: Create form
    public function agentCreate()
    {
        $callTypes = CallType::where('is_active', true)->get();
        $merchants = Merchant::where('is_active', true)->get();

        return view('agent.bookings.create', compact('callTypes', 'merchants'));
    }

    // AGENT: Store (your full logic from Agent/BookingController.php)[file:61]
    public function agentStore(Request $request)
    {
        $validated = $request->validate([
            // Your full validation rules here (from file:61)
            'calltype' => 'required|string',
            'serviceprovided' => 'required|string',
            // ... all other rules
        ]);

        DB::beginTransaction();
        try {
            // 1. Create Booking (your exact code)
            $booking = Booking::create([
                'user_id' => auth()->id(),
                'agent_custom_id' => auth()->user()->agent_custom_id ?? 'AG'.auth()->id(),
                // ... all fields from validated
                'status' => 'pending',
            ]);

            // 2. Segments, Passengers, Cards, Hotel/Cab/Insurance (your exact code)
            // Copy from file:61 store() method

            DB::commit();

            return redirect()->route('agent.bookings.show', $booking->id)
                ->with('success', 'Booking created! Ref: '.$booking->booking_reference);
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Failed: '.$e->getMessage()])->withInput();
        }
    }

    // AGENT: Show booking
public function agentShow(Booking $booking)
{
    abort_unless($booking->user_id === auth()->id(), 403);

    $booking->load(['passengers', 'segments', 'cards.merchant', 'hotel', 'cab', 'insurance']);

    return view('agent.bookings.show', compact('booking'));
}

public function chargeShow(Booking $booking)
{
    // Charge access rule: either charge can see all,
    // OR only those "assignedtocharging"/assigned to them—apply your real rule here.

    $booking->load(['passengers', 'segments', 'cards.merchant', 'hotel', 'cab', 'insurance']);

    // If you want it EXACTLY same UI:
    return view('agent.bookings.show', compact('booking')); // reuse same blade
    // OR if you want same content but charging layout:
    // return view('charge.bookings.show', compact('booking'));
}



    // AGENT: Charge form
    public function chargeByAgent(Booking $booking)
    {
        if ($booking->user_id !== auth()->id() || $booking->status !== 'pending') {
            abort(403);
        }
        $merchants = Merchant::where('is_active', true)->get();

        return view('agent.charging.charge', compact('booking', 'merchants'));
    }

    // ADMIN/MIS: List all/filter (from AdminBookingsController)
    public function adminIndex(Request $request)
    {
        $agentId = $request->query('agent_id');
        $agent = $agentId ? User::findOrFail($agentId) : null;

        $bookings = Booking::with(['user', 'passengers', 'segments'])
            ->when($agentId, fn ($q) => $q->where('user_id', $agentId))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.bookings.index', compact('bookings', 'agent'));
    }

    // ADMIN/MIS: Show
    public function adminShow(Booking $booking)
    {
        $booking->load(['user', 'passengers', 'segments', 'cards']);

        return view('admin.bookings.show', compact('booking'));
    }

    // ADMIN/MIS: Edit/Update (from AdminBookingsController)
    public function adminEdit(Booking $booking)
    {
        return view('admin.bookings.edit', compact('booking'));
    }

    public function adminUpdate(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,charged,refunded',
            'mis_remarks' => 'nullable|string',
            'amount_charged' => 'required|numeric',
            // ... other fields
        ]);

        $booking->update($validated);

        return redirect()->route('admin.bookings.index', ['agent_id' => $booking->user_id])
            ->with('success', 'Updated!');
    }

    // assign bookings to charging team
    public function assignForCharging(Request $request, Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        if ($booking->status !== 'pending') {
            return back()->with('error', 'This booking is already assigned to the charging team (Status: '.$booking->status.').');
        }

        $request->validate([
            'merchant' => 'required|exists:merchants,id',
        ]);

        $merchant = Merchant::findOrFail($request->merchant);

        // Find a random charge user
        $charger = User::where('role', 'charge')->inRandomOrder()->first();

        if (! $charger) {
            return back()->withErrors(['merchant' => 'No charging team member is available!']);
        }

        // Create assignment record
        $assignment = ChargeAssignment::create([
            'booking_id' => $booking->id,
            'charger_id' => $charger->id,
            'agent_id' => auth()->id(),
            'merchant_id' => $merchant->id,
            'status' => 'pending',
            'assigned_at' => now(),
        ]);

        // Update booking status
        $booking->update([
            'status' => 'assigned_to_charging',
            'merchant_name' => $merchant->name,
        ]);

        // Optionally, we could store assignment id in booking if needed, but not necessary.

        return back()->with('success', 'Booking sent to '.$charger->name.' for charging.');
    }
}
