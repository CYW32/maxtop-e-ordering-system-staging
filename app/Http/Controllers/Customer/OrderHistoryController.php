<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;

class OrderHistoryController extends Controller
{
    public function index()
    {
        // Fulfills Direct Ordering: Tied strictly to auth()->id() [1, 2]
        $orders = auth()->user()->orders()
            ->where('status', '!=', 'draft') // Drafts are managed in ReservationController
            ->latest()
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function show(\App\Models\Order $order)
    {
        // Security: Ensure owner [1]
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load('items.item');

        return view('customer.orders.show', compact('order'));
    }
}
