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
    public function index(Request $request)
    {
        $user = auth()->user();

        // ARCHITECTURE FIX: Define statuses for the dropdown requested by Blade [3]
        $status = ['pending', 'approved', 'in_transit'];

        $query = Order::whereIn('status', $status)
            ->where(function ($query) use ($user) {
                $query->where('handler_id', $user->id)
                    ->orWhere(function ($sub) use ($user) {
                        $sub->whereNull('handler_id')
                            ->whereHas('user', function ($u) use ($user) {
                                $u->where('assigned_cs_id', $user->id);
                            });
                    });
            });

        // Apply Dynamic Search [1, 5]
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->search($term)
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$term}%"));
            });
        }

        // ARCHITECTURE FIX: Apply the Status Filter from the dynamic dropdown
        if ($request->filled('status') && in_array($request->status, $status)) {
            $query->where('status', $request->status);
        }

        // Finalize with Pagination and Query String Persistence
        $myOrders = $query->latest()
            ->paginate(15)
            ->withQueryString();

        // Pass $status to resolve the ErrorException in the Blade view [3]
        return view('cs.orders.index', compact('myOrders', 'status'));
    }

    /**
     * Fulfills Section 5.b: Claiming Queue (Unassigned Orders)
     * Displays orders from customers who have NO assigned CS Staff.
     */
    public function queue(Request $request) // Inject Request
    {
        $query = Order::where('status', 'pending')
            ->whereNull('handler_id')
            ->whereHas('user', function ($q) {
                $q->whereNull('assigned_cs_id');
            });

        // ARCHITECTURE FIX: Enable search by Order # or Customer Name
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->search($term) // Hits order_number [12]
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$term}%"));
            });
        }

        $unassigned = $query->latest()->paginate(15)->withQueryString();

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
     * Fulfills Ownership Logic and Section 5.a Permanent Cluster Assignment
     */
    public function claim(Order $order)
    {
        if ($order->handler_id) {
            return redirect()->back()->with('error', 'This order has already been claimed.');
        }

        DB::transaction(function () use ($order) {
            // 1. Claim the specific order
            $order->update(['handler_id' => auth()->id()]);

            // 2. Fulfills Section 5.a Cluster Logic:
            // If the HQ is unassigned, assign the entire hierarchy to this CS
            $customer = $order->user;

            // Find the "Root HQ" (The user themselves if they have no parent)
            $hq = $customer->parent_id ? $customer->parent : $customer;

            if (is_null($hq->assigned_cs_id)) {
                // Assign the HQ
                $hq->update(['assigned_cs_id' => auth()->id()]);

                // Assign all associated branches
                $hq->branches()->update(['assigned_cs_id' => auth()->id()]);

                activity('user_assignment')
                    ->performedOn($hq)
                    ->causedBy(auth()->user())
                    ->log("Entire HQ Cluster ({$hq->name} and branches) assigned to CS: ".auth()->user()->name);
            }
        });

        activity('order')
            ->performedOn($order)
            ->causedBy(auth()->user())
            ->log('Order claimed by CS Staff');

        return redirect()->back()->with('success', 'Order claimed. You are now the assigned representative for this entire customer hierarchy.');
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
    public function history(Request $request) // Added Request injection
    {
        $user = auth()->user();

        // Define the statuses available for filtering
        $status = ['draft', 'pending', 'approved', 'in_transit', 'completed', 'cancelled'];

        // 1. Initialize query scoped to the current handler [Section 5.c]
        $query = \App\Models\Order::where('handler_id', $user->id);

        // 2. Apply Dynamic Search (Order Number or Customer Name)
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->search($term) // Uses Searchable trait [4]
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$term}%"));
            });
        }

        // 3. ARCHITECTURE FIX: Apply the Status Filter from the dropdown
        if ($request->filled('status') && in_array($request->status, $status)) {
            $query->where('status', $request->status);
        }

        // 4. Finalize with Pagination and Query String Persistence
        $history = $query->latest()
            ->paginate(15)
            ->withQueryString(); // Prevents losing filters when clicking "Page 2" [5]

        return view('cs.orders.history', compact('history', 'status'));
    }
}
