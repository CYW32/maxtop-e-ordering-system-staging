<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Later, we can add logic here.
        // Example: If user is Admin, fetch system stats.
        // Example: If user is Customer, fetch their order history.

        return view('dashboard');
    }
}
