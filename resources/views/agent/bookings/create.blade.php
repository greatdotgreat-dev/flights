@extends('layouts.agent')

@section('content')
<div class="container-fluid pt-4">
    <div class="row mb-3">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Create New Booking</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('agent.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('agent.bookings.index') }}">Bookings</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h5><i class="icon fas fa-ban"></i> Validation Errors!</h5>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('agent.bookings.store') }}" method="POST" id="bookingForm">
        @csrf

        <!-- Section 1: Call & Service Information -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title"><i class="fas fa-phone-alt mr-2"></i>Call & Service Information</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="booking_date">Booking Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="booking_date" name="booking_date"
                                value="{{ old('booking_date', date('Y-m-d')) }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="call_type">Call Type <span class="text-danger">*</span></label>
                            <select class="form-control" id="call_type" name="call_type" required>
                                <option value="">-- Select Call Type --</option>
                                @foreach($callTypes as $type)
                                <option value="{{ $type->type_name }}" {{ old('call_type')==$type->type_name ?
                                    'selected' : '' }}>
                                    {{ $type->type_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="service_provided">Service Provided <span class="text-danger">*</span></label>
                            <select class="form-control" id="service_provided" name="service_provided" required>
                                <option value="">-- Select Service --</option>
                                <option value="Flight" {{ old('service_provided')=='Flight' ? 'selected' : '' }}>Flight
                                </option>
                                <option value="Hotel" {{ old('service_provided')=='Hotel' ? 'selected' : '' }}>Hotel
                                </option>
                                <option value="Package" {{ old('service_provided')=='Package' ? 'selected' : '' }}>
                                    Package</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="service_type">Service Type <span class="text-danger">*</span></label>
                            <select class="form-control" id="service_type" name="service_type" required>
                                <option value="">-- Select Type --</option>
                                <option value="New Booking" {{ old('service_type')=='New Booking' ? 'selected' : '' }}>
                                    New Booking</option>
                                <option value="Modification" {{ old('service_type')=='Modification' ? 'selected' : ''
                                    }}>Modification</option>
                                <option value="Cancellation" {{ old('service_type')=='Cancellation' ? 'selected' : ''
                                    }}>Cancellation</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="booking_portal">Booking Portal <span class="text-danger">*</span></label>
                            <select class="form-control" id="booking_portal" name="booking_portal" required>
                                <option value="">-- Select Portal --</option>
                                <option value="amadeus" {{ old('booking_portal')=='amadeus' ? 'selected' : '' }}>Amadeus
                                </option>
                                <option value="sabre" {{ old('booking_portal')=='sabre' ? 'selected' : '' }}>Sabre
                                </option>
                                <option value="worldspan" {{ old('booking_portal')=='worldspan' ? 'selected' : '' }}>
                                    Worldspan</option>
                                <option value="gds" {{ old('booking_portal')=='gds' ? 'selected' : '' }}>GDS</option>
                                <option value="website" {{ old('booking_portal')=='website' ? 'selected' : '' }}>Website
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="email_auth_taken"
                                    name="email_auth_taken" value="1" {{ old('email_auth_taken') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="email_auth_taken">Email Authorization
                                    Taken</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Customer Information -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <h3 class="card-title"><i class="fas fa-user mr-2"></i>Customer Information</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="customer_name">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name"
                                value="{{ old('customer_name') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="customer_email">Customer Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="customer_email" name="customer_email"
                                value="{{ old('customer_email') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="customer_phone">Customer Phone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="customer_phone" name="customer_phone"
                                value="{{ old('customer_phone') }}" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="billing_phone">Billing Phone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="billing_phone" name="billing_phone"
                                value="{{ old('billing_phone') }}" required>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="billing_address">Billing Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="billing_address" name="billing_address" rows="2"
                                required>{{ old('billing_address') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Flight Details -->
       
         @include('agent.bookings.partials.flight-detail')

        <!-- Section 4: Passenger Details -->
        <div class="card">
            <div class="card-header bg-warning">
                <h3 class="card-title"><i class="fas fa-users mr-2"></i>Passenger Details</h3>
                <div class="card-tools">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-light" id="adultsLabel">Adults: <span
                                id="adultsCount">1</span></button>
                        <button type="button" class="btn btn-outline-dark"
                            onclick="changePassengerCount('adults', -1)">-</button>
                        <button type="button" class="btn btn-outline-dark"
                            onclick="changePassengerCount('adults', 1)">+</button>

                        <button type="button" class="btn btn-light ml-2" id="childrenLabel">Children: <span
                                id="childrenCount">0</span></button>
                        <button type="button" class="btn btn-outline-dark"
                            onclick="changePassengerCount('children', -1)">-</button>
                        <button type="button" class="btn btn-outline-dark"
                            onclick="changePassengerCount('children', 1)">+</button>

                        <button type="button" class="btn btn-light ml-2" id="infantsLabel">Infants: <span
                                id="infantsCount">0</span></button>
                        <button type="button" class="btn btn-outline-dark"
                            onclick="changePassengerCount('infants', -1)">-</button>
                        <button type="button" class="btn btn-outline-dark"
                            onclick="changePassengerCount('infants', 1)">+</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <input type="hidden" name="adults" id="adults" value="1">
                <input type="hidden" name="children" id="children" value="0">
                <input type="hidden" name="infants" id="infants" value="0">

                <div id="passengersContainer">
                    <!-- Passengers will be dynamically added here -->
                </div>

                <div class="alert alert-info mt-3" id="totalPassengersAlert">
                    <i class="fas fa-info-circle"></i> Total Passengers: <strong><span
                            id="totalPassengers">1</span></strong> (Maximum 9)
                </div>
            </div>
        </div>

        <!-- Section 5: Optional Services -->
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h3 class="card-title"><i class="fas fa-concierge-bell mr-2"></i>Optional Services</h3>
            </div>
            <div class="card-body">
                <!-- Hotel Service -->
                <div class="custom-control custom-switch mb-3">
                    <input type="checkbox" class="custom-control-input" id="hotel_required" name="hotel_required"
                        value="1" {{ old('hotel_required') ? 'checked' : '' }} onchange="toggleService('hotel')">
                    <label class="custom-control-label" for="hotel_required"><strong>Hotel Required</strong></label>
                </div>
                <div id="hotelSection" style="display: none;" class="border p-3 mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hotel_name">Hotel Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="hotel[hotel_name]"
                                    value="{{ old('hotel.hotel_name') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hotel_location">Hotel Location <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="hotel[hotel_location]"
                                    value="{{ old('hotel.hotel_location') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="check_in_date">Check-in Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="hotel[check_in_date]"
                                    value="{{ old('hotel.check_in_date') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="check_out_date">Check-out Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="hotel[check_out_date]"
                                    value="{{ old('hotel.check_out_date') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="room_type">Room Type</label>
                                <input type="text" class="form-control" name="hotel[room_type]"
                                    value="{{ old('hotel.room_type') }}" placeholder="e.g., Deluxe">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="number_of_rooms">Number of Rooms</label>
                                <input type="number" class="form-control" name="hotel[number_of_rooms]"
                                    value="{{ old('hotel.number_of_rooms', 1) }}" min="1">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hotel_cost">Hotel Cost</label>
                                <input type="number" step="0.01" class="form-control" name="hotel[hotel_cost]"
                                    value="{{ old('hotel.hotel_cost', 0) }}" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hotel_remarks">Hotel Remarks</label>
                                <textarea class="form-control" name="hotel[hotel_remarks]"
                                    rows="2">{{ old('hotel.hotel_remarks') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cab Service -->
                <div class="custom-control custom-switch mb-3">
                    <input type="checkbox" class="custom-control-input" id="cab_required" name="cab_required" value="1"
                        {{ old('cab_required') ? 'checked' : '' }} onchange="toggleService('cab')">
                    <label class="custom-control-label" for="cab_required"><strong>Cab Required</strong></label>
                </div>
                <div id="cabSection" style="display: none;" class="border p-3 mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cab_type">Cab Type <span class="text-danger">*</span></label>
                                <select class="form-control" name="cab[cab_type]">
                                    <option value="pickup" {{ old('cab.cab_type')=='pickup' ? 'selected' : '' }}>Pickup
                                    </option>
                                    <option value="drop" {{ old('cab.cab_type')=='drop' ? 'selected' : '' }}>Drop
                                    </option>
                                    <option value="roundtrip" {{ old('cab.cab_type')=='roundtrip' ? 'selected' : '' }}>
                                        Round Trip</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="pickup_location">Pickup Location</label>
                                <input type="text" class="form-control" name="cab[pickup_location]"
                                    value="{{ old('cab.pickup_location') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="drop_location">Drop Location</label>
                                <input type="text" class="form-control" name="cab[drop_location]"
                                    value="{{ old('cab.drop_location') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="pickup_datetime">Pickup Date & Time</label>
                                <input type="datetime-local" class="form-control" name="cab[pickup_datetime]"
                                    value="{{ old('cab.pickup_datetime') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cab_cost">Cab Cost</label>
                                <input type="number" step="0.01" class="form-control" name="cab[cab_cost]"
                                    value="{{ old('cab.cab_cost', 0) }}" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cab_remarks">Cab Remarks</label>
                                <textarea class="form-control" name="cab[cab_remarks]"
                                    rows="2">{{ old('cab.cab_remarks') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Insurance Service -->
                <div class="custom-control custom-switch mb-3">
                    <input type="checkbox" class="custom-control-input" id="insurance_required"
                        name="insurance_required" value="1" {{ old('insurance_required') ? 'checked' : '' }}
                        onchange="toggleService('insurance')">
                    <label class="custom-control-label" for="insurance_required"><strong>Insurance
                            Required</strong></label>
                </div>
                <div id="insuranceSection" style="display: none;" class="border p-3 mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="insurance_type">Insurance Type</label>
                                <input type="text" class="form-control" name="insurance[insurance_type]"
                                    value="{{ old('insurance.insurance_type') }}" placeholder="e.g., Trip Cancellation">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="insurance_provider">Insurance Provider</label>
                                <input type="text" class="form-control" name="insurance[insurance_provider]"
                                    value="{{ old('insurance.insurance_provider') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="coverage_amount">Coverage Amount</label>
                                <input type="number" step="0.01" class="form-control" name="insurance[coverage_amount]"
                                    value="{{ old('insurance.coverage_amount', 0) }}" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="insurance_cost">Insurance Cost</label>
                                <input type="number" step="0.01" class="form-control" name="insurance[insurance_cost]"
                                    value="{{ old('insurance.insurance_cost', 0) }}" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="policy_number">Policy Number</label>
                                <input type="text" class="form-control" name="insurance[policy_number]"
                                    value="{{ old('insurance.policy_number') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="insurance_remarks">Insurance Remarks</label>
                                <textarea class="form-control" name="insurance[insurance_remarks]"
                                    rows="2">{{ old('insurance.insurance_remarks') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 6: Payment Details -->
        <div class="card mt-4">
            <div class="card-header bg-success text-white">
                <h3 class="card-title"><i class="fas fa-credit-card mr-2"></i>Payment Details</h3>
            </div>
            <div class="card-body">

                @error('cards')
                <div class="alert alert-danger">{{ $message }}</div>
                @enderror

                {{-- ROW 1: Currency | Amount Charged | Amount Paid to Airline | Total MCO --}}
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Currency <span class="text-danger">*</span></label>
                            <select name="currency" class="form-control" required>
                                <option value="USD" {{ old('currency','USD')=='USD' ?'selected':'' }}>USD</option>
                                <option value="EUR" {{ old('currency')=='EUR' ?'selected':'' }}>EUR</option>
                                <option value="GBP" {{ old('currency')=='GBP' ?'selected':'' }}>GBP</option>
                                <option value="AUD" {{ old('currency')=='AUD' ?'selected':'' }}>AUD</option>
                                <option value="CAD" {{ old('currency')=='CAD' ?'selected':'' }}>CAD</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Amount Charged (Customer Pays) <span class="text-danger">*</span></label>
                            <input type="number" id="amount_charged" name="amount_charged" class="form-control"
                                step="0.01" min="0" value="{{ old('amount_charged') }}" required placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Amount Paid to Airline <span class="text-danger">*</span></label>
                            <input type="number" id="amount_paid_airline" name="amount_paid_airline"
                                class="form-control" step="0.01" min="0" value="{{ old('amount_paid_airline') }}"
                                required placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Total MCO (Profit) <span class="text-danger">*</span></label>
                            <input type="number" id="total_mco" name="total_mco" class="form-control" step="0.01"
                                value="{{ old('total_mco') }}" placeholder="Enter MCO manually">
                            {{-- Live MCO hint message --}}
                            <small id="mco_hint" class="text-muted" style="display:none;">
                                💡 Suggested MCO:
                                <strong id="mco_suggested" class="text-success">$0.00</strong>
                                (Amount Charged − Amount Paid to Airline)
                            </small>
                        </div>
                    </div>
                </div>

                <hr>

                {{-- Payment Type Toggle --}}
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="font-weight-bold">Payment Type <span class="text-danger">*</span></label>
                        <div class="mt-1">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="payment_type" id="payment_full"
                                    value="full" checked>
                                <label class="form-check-label" for="payment_full">
                                    <i class="fas fa-credit-card mr-1"></i> Full Payment (Single Merchant)
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="payment_type" id="payment_split"
                                    value="split">
                                <label class="form-check-label" for="payment_split">
                                    <i class="fas fa-code-branch mr-1"></i> Split Payment (Multiple Merchants)
                                </label>
                            </div>
                        </div>
                        <div id="split_payment_note" class="alert alert-info mt-2 py-2" style="display:none;">
                            <i class="fas fa-info-circle mr-1"></i>
                            <strong>Split Payment:</strong> Distribute the total across multiple merchants.
                            All card charge amounts must add up to <strong>Amount Charged</strong>.
                        </div>
                    </div>
                </div>

                {{-- Cards Container --}}
                <div id="cards_container">
                    <div class="card-item border rounded mb-3" id="card_block_0">
                        <div class="bg-light px-3 py-2 d-flex justify-content-between align-items-center rounded-top">
                            <strong><i class="fas fa-credit-card mr-2 text-success"></i>Card 1</strong>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-card-btn"
                                style="display:none;" onclick="removeCard(0)">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </div>
                        <div class="p-3">
                            @include('agent.bookings.partials.card_row', ['index' => 0, 'merchants' => $merchants])
                        </div>
                    </div>
                </div>

                {{-- Add Card Button (split only) --}}
                <div id="add_card_btn_wrapper" style="display:none;" class="mb-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addCard()">
                        <i class="fas fa-plus mr-1"></i> Add Another Card / Merchant
                    </button>
                </div>

                {{-- Split Payment Total Validation Bar --}}
                <div id="card_total_bar" class="p-2 border rounded bg-light mt-2" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Total Charged on All Cards:</span>
                        <strong id="card_total_display" class="text-danger">$0.00</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <span class="text-muted">Amount Required (Amount Charged):</span>
                        <strong id="required_total_display">$0.00</strong>
                    </div>
                    <div id="card_match_msg" class="mt-2"></div>
                </div>

            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- SECTION 7: AGENT REMARKS + SUBMIT --}}
        {{-- ============================================================ --}}
        <div class="card mt-4">
            <div class="card-header bg-dark text-white">
                <h3 class="card-title">
                    <i class="fas fa-sticky-note mr-2"></i>Agent Remarks & Submit
                </h3>
            </div>
            <div class="card-body">

                <div class="form-group">
                    <label for="agent_remarks">Agent Remarks / Notes</label>
                    <textarea name="agent_remarks" id="agent_remarks" class="form-control" rows="3"
                        placeholder="Any internal notes about this booking...">{{ old('agent_remarks') }}</textarea>
                </div>

                <hr>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('agent.bookings.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-arrow-left mr-2"></i>Cancel / Back
                    </a>
                    <button type="submit" class="btn btn-success btn-lg px-5">
                        <i class="fas fa-check-circle mr-2"></i>Create Booking
                    </button>
                    {{-- Hidden fallback to remind agent --}}
                    @error('segments')
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Please select a <strong>Flight Type</strong> and fill at least one flight segment.
                    </div>  
                    @enderror
                </div>

            </div>
        </div>

    </form>
    @push('styles')
    <style>
        .card-header {
            font-weight: 600;
        }

        .passenger-card {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .passenger-card-header {
            background-color: #007bff;
            color: white;
            padding: 8px 12px;
            margin: -15px -15px 15px -15px;
            border-radius: 4px 4px 0 0;
            font-weight: 600;
        }

        .remove-card-btn {
            float: right;
            margin-top: -3px;
        }

        .card-number-input {
            font-family: monospace;
            font-size: 1.1em;
            letter-spacing: 2px;
        }
    </style>
    @endpush

    <script>
        // Inject merchant options for JS to use when adding cards
        window.merchantOptions = `
@foreach($merchants as $m)
    <option value="{{ $m->id }}">{{ $m->name }}{{ $m->code ? ' ('.$m->code.')' : '' }} — {{ $m->currency }}</option>
@endforeach
`;
    </script>

    <script src="{{ asset('js/agent/booking-form.js') }}"></script>

    @endsection]