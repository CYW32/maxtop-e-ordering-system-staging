<?php

namespace App\Http\Requests\CS;

use Illuminate\Foundation\Http\FormRequest;

class CancelOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only handlers, leaders, or admins can cancel
        return true;
    }

    public function rules(): array
    {
        return [
            'cancellation_reason' => 'required|string|min:5|max:1000',
        ];
    }
}
