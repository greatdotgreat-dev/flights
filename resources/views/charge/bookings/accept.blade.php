@extends('layouts.charging')
s

{{-- For charging team to accept assignments --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-credit-card fa-3x text-primary mb-4"></i>
                    <h3>Accept Charging Assignment?</h3>
                    <p class="lead">Booking #{{ $booking->booking_reference }}</p>
                    <p>{{ $booking->customer_name }} - ${{ number_format($booking->amount_charged, 2) }}</p>
                    
                    <form method="POST" action="{{ route('charge.accept.assignment', $assignment) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-check mr-2"></i>Accept & Start Charging
                        </button>
                    </form>
                    
                    <a href="{{ route('charge.dashboard') }}" class="btn btn-secondary btn-lg mt-2">
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
