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
            'item_id' => [
                'required',
                'exists:items,id',
                function ($attribute, $value, $fail) {
                    $user = auth()->user();
                    $visibleItemIds = $user->getVisibleItems()->pluck('id')->toArray();
                    if (! in_array($value, $visibleItemIds)) {
                        $fail('This item is not available in your assigned catalog.');
                    }
                },
            ],
            'quantity' => 'required|integer|min:1|max:999',
        ];
    }
}
