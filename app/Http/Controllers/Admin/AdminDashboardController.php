<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AdminDashboardController extends Controller
{  
    public function index()
{
    $totalBookings  = Booking::count();
    // $totalAgents    = User::where(function ($q) {
        // $q->where('email', 'like', '%@callinggenie.com')
        //   ->orWhere('email', 'like', '%@trafficpirates.com');
    // })->count();
    $totalAgents = User::where('role', 'agent')->count();

    $todaysBookings = Booking::whereDate('created_at', today())->count();
    $latestBookings = Booking::with('user')->latest()->take(10)->get();

    return view('admin.dashboard', compact(
        'totalBookings',
        'totalAgents', // it is showing total users not total agents, we need to filter users with specific role where role is agent

        'todaysBookings',
        'latestBookings'
    ));
}

}

