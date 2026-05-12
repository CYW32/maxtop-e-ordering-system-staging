<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
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

        // ARCHITECTURE FIX: Define statuses for the dropdown requested by Blade
        $status = ['pending', 'approved', 'in_transit', 'delivered', 'cancelled'];

        $query = Order::whereIn('status', $status)->where(function ($query) use ($user) {
            $query->where('handler_id', $user->id)->orWhere(function ($sub) use ($user) {
                $sub->whereNull('handler_id')->whereHas('user', function ($u) use ($user) {
                    $u->where('assigned_cs_id', $user->id);
                });
            });
        });

        // Apply Dynamic Search
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->search($term)->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$term}%"));
            });
        }

        // ARCHITECTURE FIX: Apply the Status Filter from the dynamic dropdown
        if ($request->filled('status') && in_array($request->status, $status)) {
            $query->where('status', $request->status);
        }

        // Finalize with Pagination and Query String Persistence
        $myOrders = $query->latest()->paginate(15)->withQueryString();

        return view('cs.orders.index', compact('myOrders', 'status'));
    }

    /**
     * Fulfills Section 5.b: Claiming Queue (Unassigned Orders)
     * Displays orders from customers who have NO assigned CS Staff.
     */
    public function queue(Request $request)
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
                $q->search($term)->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$term}%"));
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
        /**
         * ARCHITECTURE FIX: Eager load relationships.
         * Added 'statusHistory.changer' to support the view's audit trail without N+1 queries [2].
         */
        $order->load(['items.item', 'items.uom', 'user.company', 'handler', 'statusHistory.changer']);

        /**
         * PRAGMATIC FIX: Resolve 'Undefined variable $placeOrderDate' Error.
         * As requested, mapping this to the record's creation timestamp [1].
         */
        $placeOrderDate = $order->created_at;

        $currentUser = auth()->user();

        // 1. DEFINE TARGET ROLES FOR HANDOVER [Backbone 25]
        // Default: Admins and Leaders can transfer authority to any CS role.
        $targetRoles = ['admin', 'cs_leader', 'cs_staff'];

        /**
         * 2. RESTRICTION: CS Staff Escalation Path [Backbone 32.d.2]
         * If the user is strictly CS Staff, they can ONLY hand over to a CS Leader.
         */
        if ($currentUser->hasRole('cs_staff') && !$currentUser->hasAnyRole(['admin', 'cs_leader'])) {
            $targetRoles = ['cs_leader'];
        }

        $staffQuery = User::role($targetRoles)->where('status', 'active');

        /**
         * 3. OWNERSHIP GUARD [Backbone 32.c]
         * Exclude the *current handler* from the list to prevent lateral assignment loops.
         */
        if ($order->handler_id) {
            $staffQuery->where('id', '!=', $order->handler_id);
        }

        $eligibleStaff = $staffQuery->orderBy('name')->get();

        /**
         * 4. PASS DATA TO VIEW
         * Including $placeOrderDate ensures the header template can render without failure [3].
         */
        return view('cs.orders.show', compact('order', 'eligibleStaff', 'placeOrderDate'));
    }

    /**
     * Fulfills Section 5: Handover Protocol (CS A to CS B)
     */
    public function handover(Request $request, Order $order)
    {
        $request->validate([
            'new_handler_id' => 'required|exists:users,id',
        ]);

        // NEW RULE: Prevent handover if order is already shipped or delivered
        if (in_array($order->status, ['in_transit', 'delivered', 'cancelled'])) {
            return redirect()->back()->with('error', 'Orders that are already In Transit or Delivered cannot be transferred.');
        }

        $oldHandlerName = $order->handler->name ?? 'Unassigned';
        $newHandler = User::findOrFail($request->new_handler_id);

        // Security: Only the current handler or a Leader/Admin can initiate handover
        if (
            $order->handler_id !== auth()->id() &&
            !auth()
                ->user()
                ->hasAnyRole(['admin', 'cs_leader'])
        ) {
            abort(403, 'Unauthorized handover attempt.');
        }

        $order->update(['handler_id' => $newHandler->id]);

        activity('order')
            ->performedOn($order)
            ->causedBy(auth()->user())
            ->log("Order handed over from {$oldHandlerName} to {$newHandler->name}");

        return redirect()
            ->back()
            ->with('success', "Order handed over to {$newHandler->name}.");
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
            $customer = $order->user;
            $hq = $customer->parent_id ? $customer->parent : $customer;

            if (is_null($hq->assigned_cs_id)) {
                $hq->update(['assigned_cs_id' => auth()->id()]);
                $hq->branches()->update(['assigned_cs_id' => auth()->id()]);

                activity('user_assignment')
                    ->performedOn($hq)
                    ->causedBy(auth()->user())
                    ->log("Entire HQ Cluster ({$hq->name} and branches) assigned to CS: " . auth()->user()->name);
            }
        });

        activity('order')
            ->performedOn($order)
            ->causedBy(auth()->user())
            ->log('Order claimed by CS Staff');

        return redirect()->back()->with('success', 'Order claimed. You are now the assigned representative for this entire customer hierarchy.');
    }

    /**
     * Fulfills Addendum 5.a: UOM Snapshot during Approval
     */
    public function approve(Order $order)
    {
        $this->authorizeAction($order);

        DB::transaction(function () use ($order) {
            foreach ($order->items as $orderItem) {
                $uom = $orderItem->uom;

                $orderItem->update([
                    'snapshot_name' => $orderItem->item->name,
                    'snapshot_uom_name' => $uom?->uom_name ?? 'Unit',
                    'snapshot_uom_rate' => $uom?->rate_qty ?? 1,
                    'price_at_order' => $uom->price,
                ]);
            }
            $order->update(['status' => 'approved']);
        });

        return redirect()->back()->with('success', 'Order approved and Pure UOM prices snapshotted.');
    }

    /**
     * Fulfills Addendum 4.b: Filtered list for Leaders to approve cancellations.
     */
    public function cancellationRequests(Request $request)
    {
        $query = Order::where('status', 'cancellation_requested')->with(['user.company', 'handler', 'cancellationRequester']);

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->search($term)->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$term}%"));
            });
        }

        $requests = $query->latest()->paginate(15)->withQueryString();

        return view('cs.orders.cancellations', compact('requests'));
    }

    /**
     * Fulfills Addendum 4.a & 4.b: Strict Role-Based Cancellation.
     */
    public function cancel(\App\Http\Requests\CS\CancelOrderRequest $request, Order $order)
    {
        $user = auth()->user();
        $this->authorizeAction($order);

        // NEW SECURITY RULE: Block cancellation for Shipped or Delivered orders
        if (in_array($order->status, ['in_transit', 'delivered'])) {
            return redirect()->back()->with('error', 'Operation Denied: Orders that are already in transit or delivered cannot be cancelled.');
        }

        // LOGIC FIX: Handle Approved Order -> Request Transition
        if ($order->status === 'approved' && $user->hasRole('cs_staff')) {
            $order->update([
                'status' => 'cancellation_requested',
                'cancellation_requested_by' => $user->id,
                'cancellation_request_reason' => $request->cancellation_reason,
            ]);

            return redirect()->route('office.orders.show', $order)->with('success', 'Cancellation request submitted for manager approval.');
        }

        // SECURITY FIX: Explicitly block CS Staff from finalizing cancellations
        if ($order->status === 'cancellation_requested' && $user->hasRole('cs_staff')) {
            abort(403, 'Unauthorized: CS Staff cannot finalize cancellation requests.');
        }

        // Only Admin/Leader reaches here to finalize
        $order->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->cancellation_reason ?? $order->cancellation_request_reason,
            'cancellation_requested_by' => $order->cancellation_requested_by ?? $user->id,
        ]);

        return redirect()->route('office.orders.index')->with('success', 'Order has been permanently cancelled.');
    }

    /**
     * Fulfills Section 4.4 & 6: In Transit Status & Internal Notes
     */
    public function updateStatus(Request $request, Order $order)
    {
        if (
            $order->handler_id !== auth()->id() &&
            !auth()
                ->user()
                ->hasAnyRole(['admin', 'cs_leader'])
        ) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,approved,in_transit,delivered',
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
     */
    public function history(Request $request)
    {
        $user = auth()->user();
        $status = ['draft', 'pending', 'approved', 'in_transit', 'delivered', 'cancelled'];

        $query = Order::where('handler_id', $user->id);

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->search($term)->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$term}%"));
            });
        }

        if ($request->filled('status') && in_array($request->status, $status)) {
            $query->where('status', $request->status);
        }

        $history = $query->latest()->paginate(15)->withQueryString();

        return view('cs.orders.history', compact('history', 'status'));
    }

    /**
     * Fulfills Section 5.d Handover Protocol & Ownership Logic.
     */
    private function authorizeAction(Order $order): void
    {
        $user = auth()->user();

        if ($user->hasAnyRole(['admin', 'cs_leader'])) {
            return;
        }

        if ($order->handler_id !== $user->id) {
            abort(403, 'Read-Only Mode: You are not the assigned handler for this order.');
        }
    }

    /**
     * Fulfills Leadership Oversight Requirement.
     */
    public function allOrders(Request $request)
    {
        if (
            !auth()
                ->user()
                ->hasAnyRole(['admin', 'cs_leader'])
        ) {
            abort(403, 'Unauthorized: Master oversight restricted to Leadership.');
        }

        $status = ['draft', 'pending', 'approved', 'in_transit', 'delivered', 'cancelled', 'cancellation_requested'];

        $query = Order::with(['user.company', 'handler']);

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->search($term)->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$term}%"))->orWhereHas('handler', fn($h) => $h->where('name', 'like', "%{$term}%"));
            });
        }

        if ($request->filled('status') && in_array($request->status, $status)) {
            $query->where('status', $request->status);
        }

        $allOrders = $query->latest()->paginate(15)->withQueryString();

        return view('cs.orders.all', compact('allOrders', 'status'));
    }

    /**
     * STREAM the Order PDF (View in browser tab).
     */
    public function pdf(\App\Models\Order $order)
    {
        $order->load(['user.company', 'items.item', 'items.uom']);

        // Bulletproof Way: Prevents Facade Class Not Found Error
        $pdf = app('dompdf.wrapper')->loadView('cs.orders.pdf', compact('order'));

        // Requested filename change
        $filename = 'Stock_Order_' . $order->order_number . '.pdf';

        // Use stream() to just "view only" in the browser tab
        return $pdf->stream($filename);
    }

    /**
     * Generate and DOWNLOAD the Stock Order PDF.
     */
    public function stockOrder(\App\Models\Order $order)
    {
        $order->load(['user.company', 'items.item', 'items.uom']);

        // Bulletproof Way: Prevents Facade Class Not Found Error
        $pdf = app('dompdf.wrapper')->loadView('cs.orders.stock-order', compact('order'));

        $filename = 'Stock_Order_' . $order->order_number . '.pdf';

        // Use download() to force the file to save to the user's PC
        return $pdf->download($filename);
    }
}
