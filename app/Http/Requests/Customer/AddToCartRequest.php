<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('customer');
    }

    public function rules(): array
    {
        return [
            'item_id' => ['required', 'exists:items,id'],
            // ARCHITECTURE FIX: Mandatory UOM ID requirement [Addendum 5.a]
            'uom_id' => ['required', 'exists:uoms,id'],
            'quantity' => 'required|integer|min:1|max:999',
        ];
    }
}
