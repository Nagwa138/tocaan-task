<?php

namespace App\Http\Requests\API\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
{
    public function authorize(): true
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'items' => 'required|array|min:1',
            'items.*.product' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Order items are required',
            'items.*.product.required' => 'Product name is required',
            'items.*.quantity.min' => 'Quantity must be at least 1',
            'items.*.price.min' => 'Price cannot be negative',
        ];
    }
}
