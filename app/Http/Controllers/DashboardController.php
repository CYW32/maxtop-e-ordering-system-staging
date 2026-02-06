<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Fulfills Addendum Section 4: Unified Order Dashboard
     * Updated to support Staff-Specific Scoping per user request.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasAnyRole(['admin', 'cs_leader', 'cs_staff'])) {

            // ARCHITECTURE FIX: Initialize scoped query based on current staff [Section 5.a]
            $baseQuery = Order::assignedTo($user);

            // Clone the scoped query for each status to ensure accurate real-time counts
            $stats = [
                'All Orders' => (clone $baseQuery)->count(),
                'Pending' => (clone $baseQuery)->where('status', 'pending')->count(),
                'Approved' => (clone $baseQuery)->where('status', 'approved')->count(),
                'In Transit' => (clone $baseQuery)->where('status', 'in_transit')->count(),
                'Delivered' => (clone $baseQuery)->where('status', 'completed')->count(),
                'Cancelled' => (clone $baseQuery)->where('status', 'cancelled')->count(),
            ];

            return view('cs.orders.dashboard', compact('stats'));
        }

        // Standard landing page for Customers [Backbone Section 1.b]
        return view('dashboard');
    }
}
