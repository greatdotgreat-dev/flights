@extends('layouts.admin')

@section('title', 'Edit Booking #' . $booking->id)

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <h2><i class="bi bi-pencil-square"></i> Edit Booking #{{ $booking->id }}</h2>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.bookings.update', $booking->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="charged" {{ $booking->status == 'charged' ? 'selected' : '' }}>Charged</option>
                            <option value="refunded" {{ $booking->status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Amount Charged</label>
                        <input type="number" step="0.01" name="amount_charged" class="form-control" 
                               value="{{ $booking->amount_charged }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Amount Paid to Airline</label>
                        <input type="number" step="0.01" name="amount_paid_airline" class="form-control" 
                               value="{{ $booking->amount_paid_airline }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Total MCO</label>
                        <input type="number" step="0.01" name="total_mco" class="form-control" 
                               value="{{ $booking->total_mco }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">MIS Remarks</label>
                    <textarea name="mis_remarks" class="form-control" rows="4">{{ $booking->mis_remarks }}</textarea>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update Booking
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
