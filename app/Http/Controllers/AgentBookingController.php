<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\CallType;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AgentBookingController extends Controller
{
    public function create()
    {
        $callTypes = CallType::where('is_active', true)->get();
        $merchants = Merchant::where('is_active', true)->get();

        return view('agent.bookings.create', compact('callTypes', 'merchants'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_date' => 'required|date',
            'call_type' => 'required|string',
            'service_provided' => 'required|string|in:Flight,Hotel,Package',
            'service_type' => 'required|string|in:New Booking,Modification,Cancellation',
            'booking_portal' => 'required|string|in:amadeus,sabre,worldspan,gds,website',

            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:30',
            'billing_phone' => 'required|string|max:30',
            'billing_address' => 'required|string',

            'flight_type' => 'nullable|in:oneway,roundtrip,multicity',
            'gk_pnr' => 'nullable|string|required_without:airline_pnr',
            'airline_pnr' => 'nullable|string|required_without:gk_pnr',

            'adults' => 'required|integer|min:1|max:9',
            'children' => 'required|integer|min:0|max:9',
            'infants' => 'required|integer|min:0|max:9',

            'currency' => 'required|string|max:10',
            'amount_charged' => 'required|numeric|min:0',
            'amount_paid_airline' => 'required|numeric|min:0',
            'total_mco' => 'nullable|numeric',

            'agent_remarks' => 'nullable|string',

            'segments' => 'required_if:service_provided,Flight|array|min:1',
            'segments.*.from_city' => 'required|string|max:100',
            'segments.*.to_city' => 'required|string|max:100',
            'segments.*.departure_date' => 'required|date',
            'segments.*.return_date' => 'nullable|date',
            'segments.*.cabin_class' => 'required|string|max:50',
            'segments.*.airline_name' => 'required|string|max:100',
            'segments.*.flight_number' => 'nullable|string|max:50',
            'segments.*.segment_pnr' => 'nullable|string|max:50',

            'passengers' => 'required|array|min:1',
            'passengers.*.passenger_type' => 'required|string|max:10',
            'passengers.*.title' => 'required|string|max:20',
            'passengers.*.first_name' => 'required|string|max:100',
            'passengers.*.middle_name' => 'nullable|string|max:100',
            'passengers.*.last_name' => 'required|string|max:100',
            'passengers.*.gender' => 'required|string|max:20',
            'passengers.*.dob' => 'required|date',
            'passengers.*.passport_number' => 'nullable|string|max:50',
            'passengers.*.passport_expiry' => 'nullable|date',
            'passengers.*.nationality' => 'nullable|string|max:100',
            'passengers.*.seat_preference' => 'nullable|string|max:100',
            'passengers.*.meal_preference' => 'nullable|string|max:100',
            'passengers.*.special_assistance' => 'nullable|string|max:255',

            'cards' => 'required|array|min:1',
            'cards.*.merchant_id' => 'nullable|exists:merchants,id',
            'cards.*.merchant_name' => 'nullable|string|max:255', // NEW
            'cards.*.card_holder_name' => 'required|string|max:255',
            'cards.*.card_number' => 'required|string|max:25',
            'cards.*.card_type' => 'required|string|max:50',
            'cards.*.expiration_month' => 'required|string|max:2',
            'cards.*.expiration_year' => 'required|string|max:4',
            'cards.*.cvv' => 'required|string|max:4',
            'cards.*.charge_amount' => 'required|numeric|min:0.01',
            'cards.*.billing_email' => 'required|email|max:255',
            'cards.*.billing_phone' => 'required|string|max:30',
            'cards.*.billing_address' => 'required|string',

            'hotel_required' => 'nullable|boolean',
            'cab_required' => 'nullable|boolean',
            'insurance_required' => 'nullable|boolean',
        ]);
        // Custom check: each card must have either merchant_id OR merchant_name
        foreach ($request->input('cards', []) as $i => $card) {
            if (empty($card['merchant_id']) && empty($card['merchant_name'])) {
                throw ValidationException::withMessages([
                    "cards.$i.merchant_name" => 'Please select an existing merchant or enter a new merchant name.',
                ]);
            }

            DB::beginTransaction();

            try {
                $firstSegment = $validated['segments'][0] ?? null;

                $booking = Booking::create([
                    'user_id' => auth()->id(),
                    'agent_custom_id' => auth()->user()->agent_custom_id ?? ('AG'.auth()->id()),
                    'booking_date' => $validated['booking_date'],
                    'call_type' => $validated['call_type'],
                    'service_provided' => $validated['service_provided'],
                    'service_type' => $validated['service_type'],
                    'booking_portal' => $validated['booking_portal'],
                    'email_auth_taken' => $request->boolean('email_auth_taken'),

                    'customer_name' => $validated['customer_name'],
                    'customer_email' => $validated['customer_email'],
                    'customer_phone' => $validated['customer_phone'],
                    'billing_phone' => $validated['billing_phone'],
                    'billing_address' => $validated['billing_address'],

                    'flight_type' => $validated['flight_type'] ?? null,
                    'departure_city' => $firstSegment['from_city'] ?? null,
                    'arrival_city' => $firstSegment['to_city'] ?? null,
                    'departure_date' => $firstSegment['departure_date'] ?? null,
                    'return_date' => $firstSegment['return_date'] ?? null,
                    'airline_name' => $firstSegment['airline_name'] ?? null,
                    'flight_number' => $firstSegment['flight_number'] ?? null,
                    'cabin_class' => $firstSegment['cabin_class'] ?? null,

                    'adults' => $validated['adults'],
                    'children' => $validated['children'],
                    'infants' => $validated['infants'],
                    'gk_pnr' => $validated['gk_pnr'] ?? null,
                    'airline_pnr' => $validated['airline_pnr'] ?? null,

                    'currency' => $validated['currency'],
                    'amount_charged' => $validated['amount_charged'],
                    'amount_paid_airline' => $validated['amount_paid_airline'],
                    'total_mco' => $validated['total_mco'] ?? ($validated['amount_charged'] - $validated['amount_paid_airline']),
                    'status' => 'pending',
                    'agent_remarks' => $validated['agent_remarks'] ?? null,

                    'hotel_required' => $request->boolean('hotel_required'),
                    'cab_required' => $request->boolean('cab_required'),
                    'insurance_required' => $request->boolean('insurance_required'),
                ]);

                foreach ($validated['segments'] as $segment) {
                    $booking->segments()->create($segment);
                }

                foreach ($validated['passengers'] as $index => $passenger) {
                    $booking->passengers()->create($passenger + [
                        'passenger_order' => $index + 1,
                    ]);
                }


                if ($request->boolean('hotel_required') && $request->filled('hotel.hotel_name')) {
                    $booking->hotel()->create($request->hotel);
                }

                if ($request->boolean('cab_required') && $request->filled('cab.cab_type')) {
                    $booking->cab()->create($request->cab);
                }

    if ($request->boolean('insurance_required') && $request->filled('insurance.insurance_type')) {
        $booking->insurance()->create($request->insurance);
    }

    $cardsData = $validated['cards'];
    foreach ($cardsData as $index => &$card) {
        if (empty($card['merchant_id']) && !empty($card['merchant_name'])) {
            $merchant = Merchant::firstOrCreate(
                ['name' => trim($card['merchant_name'])],
                [
                    'currency' => $validated['currency'] ?? 'USD',
                    'is_active' => true,
                ]
            );

            $card['merchant_id'] = $merchant->id;
        }

        unset($card['merchant_name']); // don't send this to booking_cards table
    }
    unset($card); // break reference
    // Now create cards using normalized data
    foreach ($cardsData as $index => $card) {
        $booking->cards()->create($card + [
            'card_order' => $index + 1,
        ]);
    }

    DB::commit();

    return redirect()
        ->route('agent.bookings.show', $booking->id)
        ->with('success', 'Booking created successfully. Ref: '.$booking->booking_reference);

} catch (\Exception $e) {
                DB::rollBack();

                return back()
                    ->withErrors(['error' => $e->getMessage()])
                    ->withInput();
            }

        }
    }
}
