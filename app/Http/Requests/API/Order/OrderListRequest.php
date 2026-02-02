<?php

namespace App\Http\Requests\API\Order;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class OrderListRequest extends FormRequest
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
                'in:pending,confirmed,cancelled',
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

    public function getStatus(): mixed
    {
        return $this->input('status');
    }

    public function getPerPage(): mixed
    {
        return $this->input('per_page', 10);
    }
}
