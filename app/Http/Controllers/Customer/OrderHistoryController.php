<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderHistoryController extends Controller
{
    /**
     * Display the order history for the logged-in customer.
     * Includes filtering for Search, Status, Date Range, and Pagination control.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Base Query: Fetch orders belonging strictly to the logged-in customer
        $query = Order::where('user_id', $user->id);

        // 1. Keyword Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // 2. Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 3. Date Range Filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // 4. Per Page Logic (Defaults to 10)
        $allowedPerPage = [10, 25, 100];
        $perPage = (int) $request->input('per_page', 10);
        
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        // Fetch Paginated Orders
        $orders = $query->latest()
            ->paginate($perPage)
            ->withQueryString(); 

        return view('customer.orders.index', compact('orders'));
    }

    /**
     * Display the specific order details.
     */
    public function show(Order $order)
    {
        return view('customer.orders.show', compact('order'));
    }
}