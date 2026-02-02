<?php

namespace App\Http\Requests\API\Order;

use App\Architecture\Repositories\Interfaces\IOrderRepository;
use Illuminate\Foundation\Http\FormRequest;

class OrderDeleteRequest extends FormRequest
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
