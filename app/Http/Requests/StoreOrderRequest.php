<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_type' => 'required|in:delivery,pickup',
            'payment_method' => 'required|in:card,cash',
            'address_json' => 'nullable|array',
            'items' => 'required|array|min:1',
            'items.*.drink_id' => 'required|exists:drinks,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.customizations' => 'nullable|string',
        ];
    }
}
