<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
use App\Http\Requests\CS\CancelOrderRequest;
use App\Models\Order; // Added: Fulfills Section 5 Handover Context
use App\Models\User; // Added: Required for form handling
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderManagementController extends Controller
{
    /**
     * Fulfills Section 5.a: On-going Orders
     * Displays orders assigned to me or currently handled by me that are in active transit states.
     */
    public function index()
    {
        $user = auth()->user();

        $myOrders = Order::whereIn('status', ['pending', 'approved', 'in_transit'])
            ->where(function ($query) use ($user) {
                $query->where('handler_id', $user->id) // Already claimed
                    ->orWhere(function ($sub) use ($user) {
                        $sub->whereNull('handler_id') // New but assigned customer
                            ->whereHas('user', function ($u) use ($user) {
                                $u->where('assigned_cs_id', $user->id);
                            });
                    });
            })
            ->latest()
            ->paginate(15);

        return view('cs.orders.index', compact('myOrders'));
    }

    /**
     * Fulfills Section 5.b: Claiming Queue (Unassigned Orders)
     * Displays orders from customers who have NO assigned CS Staff.
     */
    public function queue()
    {
        $unassigned = Order::where('status', 'pending')
            ->whereNull('handler_id')
            ->whereHas('user', function ($query) {
                $query->whereNull('assigned_cs_id');
            })
            ->latest()
            ->get();

        return view('cs.orders.queue', compact('unassigned'));
    }

    /**
     * Fulfills Section 5: Handler Visibility & Handover Context
     */
    public function show(Order $order)
    {
        $order->load(['items.item', 'user.details', 'handler']);

        // Corrected: Uses the imported App\Models\User class [2]
        $eligibleStaff = User::role(['admin', 'cs_leader', 'cs_staff'])
            ->where('id', '!=', auth()->id())
            ->where('status', 'active')
            ->get();

        return view('cs.orders.show', compact('order', 'eligibleStaff'));
    }

    /**
     * Fulfills Section 5: Handover Protocol (CS A to CS B) [5]
     */
    public function handover(Request $request, Order $order)
    {
        $request->validate([
            'new_handler_id' => 'required|exists:users,id',
        ]);

        $oldHandlerName = $order->handler->name ?? 'Unassigned';
        $newHandler = User::findOrFail($request->new_handler_id);

        // Security: Only the current handler or a Leader/Admin can initiate handover [5, 6]
        if ($order->handler_id !== auth()->id() && ! auth()->user()->hasAnyRole(['admin', 'cs_leader'])) {
            abort(403, 'Unauthorized handover attempt.');
        }

        $order->update(['handler_id' => $newHandler->id]);

        activity('order')
            ->performedOn($order)
            ->causedBy(auth()->user())
            ->log("Order handed over from {$oldHandlerName} to {$newHandler->name}");

        return redirect()->route('office.orders.index')->with('success', "Order handed over to {$newHandler->name}.");
    }

    /**
     * Fulfills Ownership Logic and Section 5.a Permanent Assignment
     */
    public function claim(Order $order)
    {
        if ($order->handler_id) {
            return redirect()->back()->with('error', 'This order has already been claimed.');
        }

        DB::transaction(function () use ($order) {
            // 1. Claim the specific order
            $order->update(['handler_id' => auth()->id()]);

            // 2. Fulfills Section 5.a: If customer is unassigned, make this CS the permanent representative
            $customer = $order->user;
            if (is_null($customer->assigned_cs_id)) {
                $customer->update(['assigned_cs_id' => auth()->id()]);

                activity('user_assignment')
                    ->performedOn($customer)
                    ->causedBy(auth()->user())
                    ->log('Customer permanently assigned to CS: '.auth()->user()->name);
            }
        });

        activity('order')
            ->performedOn($order)
            ->causedBy(auth()->user())
            ->log('Order claimed by CS Staff');

        return redirect()->back()->with('success', 'Order claimed. You are now the current handler and assigned representative for this customer.');
    }

    /**
     * Fulfills Section 4.3 and 3C: Approval & Snapshot Trigger [7, 8]
     */
    public function approve(Order $order)
    {
        if ($order->handler_id !== auth()->id() && ! auth()->user()->hasAnyRole(['admin', 'cs_leader'])) {
            abort(403, 'You are not the handler for this order.');
        }

        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending orders can be approved.');
        }

        DB::transaction(function () use ($order) {
            foreach ($order->items as $orderItem) {
                $orderItem->update([
                    'snapshot_name' => $orderItem->item->name,
                    'price_at_order' => $orderItem->item->price,
                ]);
            }
            $order->update(['status' => 'approved']);
        });

        activity('order')
            ->performedOn($order)
            ->causedBy(auth()->user())
            ->log('Order approved and item names snapshotted');

        return redirect()->route('office.orders.index')->with('success', 'Order approved. Item names are now locked.');
    }

    /**
     * Fulfills Section 4.6: Order Cancellation with Mandatory Reason [9]
     */
    public function cancel(CancelOrderRequest $request, Order $order)
    {
        if ($order->handler_id !== auth()->id() && ! auth()->user()->hasAnyRole(['admin', 'cs_leader'])) {
            abort(403);
        }

        if ($order->status === 'completed') {
            return redirect()->back()->with('error', 'Completed orders cannot be cancelled.');
        }

        $order->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->cancellation_reason,
        ]);

        activity('order')
            ->performedOn($order)
            ->causedBy(auth()->user())
            ->withProperties(['reason' => $request->cancellation_reason])
            ->log('Order was cancelled');

        return redirect()->route('office.orders.index')->with('success', 'Order has been cancelled.');
    }

    /**
     * Fulfills Section 4.4 & 6: In Transit Status & Internal Notes
     */
    public function updateStatus(Request $request, Order $order)
    {
        // Security: Only handler or CS Leader/Admin [5]
        if ($order->handler_id !== auth()->id() && ! auth()->user()->hasAnyRole(['admin', 'cs_leader'])) {
            abort(403);
        }

        $validated = $request->validate([
            // FIX: Added 'pending' to the allowed list so notes can be saved early [1, 3]
            'status' => 'required|in:pending,approved,in_transit,completed',
            'internal_notes' => 'nullable|string',
            'tracking_number' => 'required_if:status,in_transit|nullable|string',
            'logistics_carrier' => 'required_if:status,in_transit|nullable|string',
        ]);

        $order->update($validated);

        activity('order')
            ->performedOn($order)
            ->causedBy(auth()->user())
            ->log("Order updated (Status: {$request->status})");

        return redirect()->back()->with('success', 'Internal notes updated successfully.');
    }

    /**
     * Fulfills Section 5.c: My Claimed Orders (Master List)
     * Displays all orders ever claimed by this CS, providing a full historical audit trail.
     */
    public function history()
    {
        $user = auth()->user();

        // Fulfills Section 5.c: Visibility for all orders where the user is the 'Current Handler'
        $history = Order::where('handler_id', $user->id)
            ->latest()
            ->paginate(15);

        return view('cs.orders.history', compact('history'));
    }
}
