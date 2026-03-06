@extends('layouts.agent')

@section('content')
<div class="container-fluid pt-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="icon fas fa-check"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="icon fas fa-ban"></i> {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-gradient-primary text-white">
                    <h3 class="card-title">
                        <i class="fas fa-credit-card mr-2"></i>Charge Booking #{{ $booking->booking_reference }}
                    </h3>
                </div>
                <div class="card-body">
                    
                    {{-- Assignment Form --}}
                    <form action="{{ route('agent.bookings.charge.assign', $booking) }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-8">
                                <h5>Booking Summary</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Customer:</strong> {{ $booking->customer_name }}<br>
                                        <strong>Phone:</strong> {{ $booking->customer_phone }}<br>
                                        <strong>Amount:</strong> <span class="text-success">${{ number_format($booking->amount_charged, 2) }}</span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>PNR:</strong> {{ $booking->gk_pnr ?? $booking->airline_pnr }}<br>
                                        <strong>Date:</strong> {{ $booking->booking_date->format('M d, Y') }}<br>
                                        <strong>Status:</strong> 
                                        <span class="badge badge-warning">Pending Charging</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Select Merchant for Charging Team <span class="text-danger">*</span></label>
                                    <select name="merchant" class="form-control" required>
                                        <option value="">-- Choose Merchant --</option>
                                        @foreach($merchants as $merchant)
                                            <option value="{{ $merchant->id }}">
                                                {{ $merchant->name }} ({{ $merchant->code ?? 'N/A' }})
                                                <small class="text-muted">{{ $merchant->currency }}</small>
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">
                                        Selected merchant details will be shared with charging team
                                    </small>
                                </div>
                            </div>
                        </div>

                        {{-- Pending Cards Table --}}
                        <hr>
                        {{-- <h6>Pending Card Charges ({{ $pendingCards->count() }})</h6> --}}
                        <div class="table-responsive mb-4">
                            <table class="table table-sm table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Card Last 4</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Holder</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @foreach($pendingCards as $index => $card)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td><code>**** {{ $card->card_last_four }}</code></td>
                                            <td><span class="badge badge-secondary">{{ $card->card_type }}</span></td>
                                            <td>${{ number_format($card->charge_amount, 2) }}</td>
                                            <td>{{ Str::limit($card->card_holder_name, 20) }}</td>
                                        </tr>
                                    @endforeach --}}
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-12 text-right">
                                <a href="{{ route('agent.bookings.show', $booking) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left mr-2"></i>Back to Details
                                </a>

                                @if($booking->status === 'pending')
                                <form action="{{ route('agent.bookings.charge.assign', $booking) }}" method="POST">
                                    @csrf
                                    <!-- merchant select etc -->
                                    <button type="submit" class="btn btn-success btn-lg">Send to Charging</button>
                                </form>
                                @else
                                    <div class="alert alert-info">This booking has already been sent to charging team.</div>
                                @endif
                                {{-- <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-paper-plane mr-2"></i>Send to Charging Team
                                </button> --}}
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

</div>
@endsection
