<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderManagementController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $status = ['pending', 'approved', 'in_transit', 'delivered', 'cancelled'];

        $query = Order::whereIn('status', $status)->where(function ($query) use ($user) {
            $query->where('handler_id', $user->id)->orWhere(function ($sub) use ($user) {
                $sub->whereNull('handler_id')->whereHas('user', function ($u) use ($user) {
                    $u->where('assigned_cs_id', $user->id);
                });
            });
        });

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->search($term)->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$term}%"));
            });
        }

        if ($request->filled('status') && in_array($request->status, $status)) {
            $query->where('status', $request->status);
        }

        $myOrders = $query->latest()->paginate(15)->withQueryString();

        return view('cs.orders.index', compact('myOrders', 'status'));
    }

    public function queue(Request $request)
    {
        $query = Order::where('status', 'pending')
            ->whereNull('handler_id')
            ->whereHas('user', function ($q) {
                $q->whereNull('assigned_cs_id');
            });

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->search($term)->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$term}%"));
            });
        }

        $unassigned = $query->latest()->paginate(15)->withQueryString();

        return view('cs.orders.queue', compact('unassigned'));
    }

    public function show(Order $order)
    {
        $order->load(['items.item', 'items.uom', 'user.company', 'handler', 'statusHistory.changer']);
        $placeOrderDate = $order->created_at;
        $currentUser = auth()->user();

        $targetRoles = ['admin', 'cs_leader', 'cs_staff'];

        if ($currentUser->hasRole('cs_staff') && !$currentUser->hasAnyRole(['admin', 'cs_leader'])) {
            $targetRoles = ['cs_leader'];
        }

        $staffQuery = User::role($targetRoles)->where('status', 'active');

        if ($order->handler_id) {
            $staffQuery->where('id', '!=', $order->handler_id);
        }

        $eligibleStaff = $staffQuery->orderBy('name')->get();

        return view('cs.orders.show', compact('order', 'eligibleStaff', 'placeOrderDate'));
    }

    public function handover(Request $request, Order $order)
    {
        $request->validate([
            'new_handler_id' => 'required|exists:users,id',
            'handover_reason' => 'required|string|max:500',
        ]);

        if (in_array($order->status, ['in_transit', 'delivered', 'cancelled'])) {
            return redirect()->back()->with('error', 'Orders that are already In Transit or Delivered cannot be transferred.');
        }

        if ($order->handler_id == $request->new_handler_id) {
            return redirect()->back()->with('error', 'Action Denied: You cannot transfer this order to the staff member who is already currently handling it.');
        }

        $oldHandlerName = $order->handler->name ?? 'Unassigned';
        $newHandler = User::findOrFail($request->new_handler_id);

        if (
            $order->handler_id !== auth()->id() &&
            !auth()
                ->user()
                ->hasAnyRole(['admin', 'cs_leader'])
        ) {
            abort(403, 'Unauthorized handover attempt.');
        }

        $order->status_change_reason = "Transferred to {$newHandler->name}. Reason: " . $request->handover_reason;

        $order->update(['handler_id' => $newHandler->id]);

        activity('order')
            ->performedOn($order)
            ->causedBy(auth()->user())
            ->log("Order handed over from {$oldHandlerName} to {$newHandler->name}. Reason: {$request->handover_reason}");

        return redirect()
            ->back()
            ->with('success', "Order handed over to {$newHandler->name}.");
    }

    public function claim(Order $order)
    {
        if ($order->handler_id) {
            return redirect()->back()->with('error', 'This order has already been claimed.');
        }

        DB::transaction(function () use ($order) {
            $order->update(['handler_id' => auth()->id()]);
            $customer = $order->user;

            if (is_null($customer->assigned_cs_id)) {
                $customer->update(['assigned_cs_id' => auth()->id()]);
            }

            if ($customer->company) {
                $company = $customer->company;
                $hq = $company->parent_id ? $company->parent : $company;
                $clusterCompanyIds = array_merge([$hq->id], $hq->branches()->pluck('id')->toArray());

                User::whereIn('company_id', $clusterCompanyIds)
                    ->whereNull('assigned_cs_id')
                    ->update(['assigned_cs_id' => auth()->id()]);

                activity('user_assignment')
                    ->performedOn($hq)
                    ->causedBy(auth()->user())
                    ->log("Entire HQ Cluster ({$hq->company_name} and branches) users assigned to CS: " . auth()->user()->name);
            } else {
                activity('user_assignment')
                    ->performedOn($customer)
                    ->causedBy(auth()->user())
                    ->log("Customer ({$customer->name}) assigned to CS: " . auth()->user()->name);
            }
        });

        activity('order')
            ->performedOn($order)
            ->causedBy(auth()->user())
            ->log('Order claimed by CS Staff');

        return redirect()
            ->route('dashboard')
            ->with('success', "Order ({$order->order_number}) has been claimed.");
    }

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

    // 🚀 这里就是之前重复了的 cancel 方法，现在只有一个了！
    public function cancel(\App\Http\Requests\CS\CancelOrderRequest $request, Order $order)
    {
        $user = auth()->user();
        $this->authorizeAction($order);

        if (in_array($order->status, ['in_transit', 'delivered'])) {
            return redirect()->back()->with('error', 'Operation Denied: Orders that are already in transit or delivered cannot be cancelled.');
        }

        // 1. CS Staff 请求取消
        if ($order->status === 'approved' && $user->hasRole('cs_staff')) {
            $order->status_change_reason = $request->cancellation_reason;

            $order->update([
                'status' => 'cancellation_requested',
                'cancellation_requested_by' => $user->id,
                'cancellation_request_reason' => $request->cancellation_reason,
            ]);

            return redirect()->route('office.orders.show', $order)->with('success', 'Cancellation request submitted for manager approval.');
        }

        if ($order->status === 'cancellation_requested' && $user->hasRole('cs_staff')) {
            abort(403, 'Unauthorized: CS Staff cannot finalize cancellation requests.');
        }

        // 智能拼接原因：如果没写原因就不显示 Note 前缀
        $managerNote = $request->filled('cancellation_reason') ? ' Note: ' . $request->cancellation_reason : '';

        // 2. 经理驳回 (Reject)
        if ($request->input('action') === 'reject') {
            $order->status_change_reason = 'Cancellation Denied.' . $managerNote;

            $order->update([
                'status' => 'approved',
                'cancellation_requested_by' => null,
                'cancellation_request_reason' => null,
            ]);

            return redirect()->route('office.orders.show', $order)->with('success', 'Request rejected. Order reverted back to Approved status.');
        }

        // 3. 经理批准 (Approve)
        $order->status_change_reason = 'Cancellation Approved.' . $managerNote;

        $order->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->cancellation_reason ?? $order->cancellation_request_reason,
            'cancellation_requested_by' => $order->cancellation_requested_by ?? $user->id,
        ]);

        return redirect()->route('office.orders.index')->with('success', 'Order has been permanently cancelled.');
    }

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

        $updateData = ['status' => $request->status];

        if ($request->has('tracking_number')) {
            $updateData['tracking_number'] = $request->tracking_number;
        }
        if ($request->has('logistics_carrier')) {
            $updateData['logistics_carrier'] = $request->logistics_carrier;
        }

        if ($request->filled('internal_notes')) {
            if (!in_array($order->status, ['pending', 'approved'])) {
                return redirect()->back()->with('error', 'Cannot add remarks once the order has passed the approved stage.');
            }

            $rawNotes = $order->internal_notes;
            $currentNotes = [];

            if (!empty($rawNotes)) {
                $decoded = json_decode($rawNotes, true);
                if (is_string($decoded)) {
                    $decoded = json_decode($decoded, true);
                }

                if (is_array($decoded)) {
                    $currentNotes = $decoded;
                } else {
                    $currentNotes = [
                        [
                            'user' => 'Legacy Record',
                            'role' => 'System',
                            'note' => $rawNotes,
                            'time' => $order->updated_at->format('d M Y | H:i'),
                        ],
                    ];
                }
            }

            $cleaned = [];
            foreach ($currentNotes as $note) {
                if (($note['user'] === 'Legacy Record' || $note['user'] === 'Previous Record') && is_string($note['note'])) {
                    $trimNote = trim($note['note']);
                    if (str_starts_with($trimNote, '[') && str_ends_with($trimNote, ']')) {
                        $parsedNested = json_decode($trimNote, true);
                        if (is_array($parsedNested)) {
                            $cleaned = array_merge($cleaned, $parsedNested);
                            continue;
                        }
                    }
                }
                $cleaned[] = $note;
            }
            $currentNotes = $cleaned;

            $currentNotes[] = [
                'user' => auth()->user()->name,
                'role' => auth()->user()->roles->first()->name ?? 'Staff',
                'note' => $request->internal_notes,
                'time' => now()->format('d M Y | H:i'),
            ];

            $updateData['internal_notes'] = json_encode($currentNotes);
        }

        $order->update($updateData);

        activity('order')
            ->performedOn($order)
            ->causedBy(auth()->user())
            ->log("Order updated (Status: {$request->status})");

        return redirect()->back()->with('success', 'Order update processed successfully.');
    }

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

    public function pdf(\App\Models\Order $order)
    {
        $order->load(['user.company', 'items.item', 'items.uom']);
        $pdf = app('dompdf.wrapper')->loadView('cs.orders.pdf', compact('order'));
        $filename = 'Stock_Order_' . $order->order_number . '.pdf';
        return $pdf->stream($filename);
    }

    public function stockOrder(\App\Models\Order $order)
    {
        $order->load(['user.company', 'items.item', 'items.uom']);
        $pdf = app('dompdf.wrapper')->loadView('cs.orders.stock-order', compact('order'));
        $filename = 'Stock_Order_' . $order->order_number . '.pdf';
        return $pdf->download($filename);
    }
}
