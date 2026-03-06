<?php
namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('agent.dashboard');

        // show total bookings created by this agent on dashboard 
    }
}
