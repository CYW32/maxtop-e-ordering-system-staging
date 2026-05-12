<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Fulfills Addendum Section 4: Unified Order Dashboard
     * Updated to support Staff-Specific Scoping and Leadership Overview.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->hasAnyRole(['admin', 'cs_leader', 'cs_staff'])) {

            // Default to 'mine' (My Orders)
            $viewMode = $request->query('view', 'mine');

            // Security: Prevent standard CS Staff from viewing all team orders on the stats dashboard
            if ($viewMode === 'all' && !$user->hasAnyRole(['admin', 'cs_leader'])) {
                $viewMode = 'mine';
            }

            // Initialize scoped query based on view mode
            if ($viewMode === 'all') {
                $baseQuery = Order::query(); // Team Orders (System-wide)
            } else {
                $baseQuery = Order::assignedTo($user); // My Orders
            }

            // Clone the scoped query for each status to ensure accurate real-time counts
            $stats = [
                'All Orders' => (clone $baseQuery)->count(),
                'Pending' => (clone $baseQuery)->where('status', 'pending')->count(),
                'Approved' => (clone $baseQuery)->where('status', 'approved')->count(),
                'In Transit' => (clone $baseQuery)->where('status', 'in_transit')->count(),
                'Delivered' => (clone $baseQuery)->where('status', 'delivered')->count(),
                'Cancelled' => (clone $baseQuery)->where('status', 'cancelled')->count(),
            ];

            return view('cs.orders.dashboard', compact('stats', 'viewMode'));
        }

        // Standard landing page for Customers [Backbone Section 1.b]
        return view('dashboard');
    }
}