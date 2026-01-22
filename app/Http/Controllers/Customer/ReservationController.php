<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\AddToCartRequest;
use App\Models\Item;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * View the current draft contents.
     */
    public function index()
    {
        $draft = auth()->user()->currentDraft();
        $items = $draft ? $draft->items()->with('item')->get() : collect();

        return view('customer.reservation.index', compact('draft', 'items'));
    }

    /**
     * Submit the reservation, moving it from Draft to Pending status. [3]
     */
    public function submit(Request $request)
    {
        $user = auth()->user();
        $draft = $user->currentDraft();

        // Validate draft is not empty [3]
        if (! $draft || $draft->items()->count() === 0) {
            return redirect()->back()->with('error', 'Your reservation is empty.');
        }

        // Generate Order Number and update status to Pending Review [3]
        $draft->update([
            'status' => 'pending',
            'order_number' => 'ORD-'.strtoupper(uniqid()),
            'created_at' => now(), // Reset timestamp to the actual submission time
        ]);

        // Manual Log for Submission Activity [4]
        activity('order')
            ->performedOn($draft)
            ->causedBy($user)
            ->log('Customer submitted reservation for review');

        return redirect()->route('dashboard')->with('success', 'Order submitted for CS review.');
    }

    /**
     * Add an item to the single allowed draft.
     */
    public function store(AddToCartRequest $request)
    {
        $user = auth()->user();
        $draft = $user->getOrCreateDraft();
        $item = Item::findOrFail($request->item_id);

        // Check if item already exists in draft
        $orderItem = $draft->items()->where('item_id', $item->id)->first();

        if ($orderItem) {
            $orderItem->increment('quantity', $request->quantity);
        } else {
            $draft->items()->create([
                'item_id' => $item->id,
                'snapshot_name' => $item->name, // Initial snapshot
                'quantity' => $request->quantity,
                'price_at_order' => $item->price,
            ]);
        }

        return redirect()->back()->with('success', 'Item added to your reservation draft.');
    }

    /**
     * Update quantity or remove item from draft/pending order.
     * Fulfills Section 4.1 & 4.2
     */
    public function update(Request $request, OrderItem $orderItem)
    {
        // Security: Allow editing if status is draft OR pending
        if ($orderItem->order->user_id !== auth()->id() ||
            ! in_array($orderItem->order->status, ['draft', 'pending'])) {
            abort(403, 'This order is locked and cannot be edited.');
        }

        $request->validate(['quantity' => 'required|integer|min:1|max:999']);
        $orderItem->update(['quantity' => $request->quantity]);

        return redirect()->back()->with('success', 'Reservation updated.');
    }

    public function destroy(OrderItem $orderItem)
    {
        // Security: Allow removal if status is draft OR pending
        if ($orderItem->order->user_id !== auth()->id() ||
            ! in_array($orderItem->order->status, ['draft', 'pending'])) {
            abort(403, 'This order is locked.');
        }

        $orderItem->delete();

        return redirect()->back()->with('success', 'Item removed.');
    }
}
