<?php

namespace App\Http\Requests\API\Order;

use App\Architecture\Repositories\Interfaces\IOrderRepository;
use Illuminate\Foundation\Http\FormRequest;

class OrderUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Check if user can process payment for this order
        if (!$this->getId()) return true;
        else return $this->user()->can('update', app(IOrderRepository::class)->first(['id' => $this->getId()]));
    }


    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'order' => 'required|exists:orders,id',
            'items' => 'required|array|min:1',
            'items.*.product' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'status' => 'sometimes|in:pending,confirmed,cancelled'
        ];
    }

    protected function prepareForValidation()
    {
        return $this->merge($this->route()->parameters);
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

    public function getId(): object|string|null
    {
        return $this->route()->parameter('order');
    }
}
