<?php

namespace App\Http\Requests\CS;

use Illuminate\Foundation\Http\FormRequest;

class CancelOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Fulfills Section 5: Only handler or elevated roles can initiate/approve cancellation
        $order = $this->route('order');

        return $order && ($order->handler_id === auth()->id() || auth()->user()->hasAnyRole(['admin', 'cs_leader']));
    }

    /**
     * ARCHITECTURE FIX: Contextual validation for Cancellation.
     * If the order is already in 'cancellation_requested' status, the reason
     * is optional as we fall back to the requester's note.
     */
    public function rules(): array
    {
        $order = $this->route('order');
        $isFinalizing = $order && $order->status === 'cancellation_requested';

        return [
            'cancellation_reason' => $isFinalizing
                ? 'nullable|string|max:1000'
                : 'required|string|min:5|max:1000',
        ];
    }
}
