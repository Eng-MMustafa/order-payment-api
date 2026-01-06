<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipping_address' => 'nullable|string',
            'billing_address' => 'nullable|string',
            'status' => 'nullable|in:pending,confirmed,cancelled',
            'notes' => 'nullable|string',
        ];
    }
}

