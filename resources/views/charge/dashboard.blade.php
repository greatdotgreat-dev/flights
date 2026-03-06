@extends('layouts.charging')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4>Charging Dashboard</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('info'))
                        <div class="alert alert-info">{{ session('info') }}</div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $pendingCount }}</h3>
                                    <p>Pending Assignments</p>
                                </div>
                                <div class="icon"><i class="fas fa-clock"></i></div>
                            </div>
                        </div>
                    </div>

                    @if($latestPending)
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <strong>New Assignment!</strong> 
                            Booking #{{ $latestPending->booking->booking_reference }} 
                            from {{ $latestPending->agent->name }} 
                            ({{ $latestPending->booking->customer_name }}) 
                            - Amount: ${{ number_format($latestPending->booking->amount_charged, 2) }}
                            <div class="mt-2">
                                <a href="{{ route('charge.assignments.details', $latestPending) }}" class="btn btn-sm btn-primary">View Details</a>
                                <form action="{{ route('charge.assignments.accept', $latestPending) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Accept</button>
                                </form>
                                <form action="{{ route('charge.assignments.reject', $latestPending) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                </form>
                            </div>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif 

                    <h5>All Assignments</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Booking Ref</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Assigned By</th>
                                <th>Assigned At</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
    @forelse($assignments as $assign)  {{-- Changed from $pendingAssignments --}}
    <tr>
        <td>{{ $assign->booking->booking_reference }}</td>
        <td>{{ $assign->booking->customer_name }}</td>
        <td>${{ number_format($assign->booking->amount_charged, 2) }}</td>
        <td>{{ $assign->agent->name }}</td>
        <td>
            @if($assign->status === 'pending')
                <span class="badge badge-warning">Pending</span>
            @elseif($assign->status === 'accepted')
                <span class="badge badge-success">Accepted</span>
            @elseif($assign->status === 'rejected')
                <span class="badge badge-danger">Rejected</span>
            @endif
        </td>
        <td>{{ $assign->assigned_at->format('d M Y H:i') }}</td>
        <td>
            <a href="{{ route('charge.assignments.details', $assign) }}" 
               class="btn btn-sm btn-info">Details</a>
               <a href="{{ route('charge.authorize.edit', $assign->booking->id) }}" 
               class="btn btn-sm btn-primary">Get Auth</a>
            
            @if($assign->status === 'pending')
                <form action="{{ route('charge.assignments.accept', $assign) }}" 
                      method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success">Accept</button>
                </form>
                <form action="{{ route('charge.assignments.reject', $assign) }}" 
                      method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                </form>
            @endif
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="7" class="text-center">No assignments found</td>
    </tr>
    @endforelse
</tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

