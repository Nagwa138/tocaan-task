<?php

namespace App\Http\Requests\API\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderUpdateStatusRequest extends FormRequest
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
            'order' => 'required|exists:orders,id',
        ];
    }

    protected function prepareForValidation()
    {
        return $this->merge($this->route()->parameters);
    }

    public function messages(): array
    {
        return [
            'order.required' => 'Order you are trying to delete seems invalid.',
        ];
    }

    public function getId(): object|string|null
    {
        return $this->route()->parameter('order');
    }
}
