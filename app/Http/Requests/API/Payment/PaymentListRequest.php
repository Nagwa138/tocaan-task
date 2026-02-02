<?php

namespace App\Http\Requests\API\Payment;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PaymentListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'status' => [
                'sometimes',
                'string',
                'in:pending,successful,failed',
            ],
            'order_id' => [
                'sometimes',
                'numeric',
                'exists:orders,id',
            ],
            'per_page' => [
                'sometimes',
                'numeric',
                'min:1'
            ],
        ];
    }

    protected function prepareForValidation()
    {
        return $this->merge($this->route()->parameters);
    }

    public function getFilters(): array
    {
        return $this->safe()->except(['per_page', 'page']);
    }

    public function getPerPage(): mixed
    {
        return $this->input('per_page', 10);
    }

    public function messages(): array
    {
        return [
            'order_id.exists' => 'The selected order does not exist.',
        ];
    }
}
