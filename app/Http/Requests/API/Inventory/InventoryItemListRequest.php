<?php

namespace App\Http\Requests\API\Inventory;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class InventoryItemListRequest extends FormRequest
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
            'search' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'min_price' => [
                'sometimes',
                'numeric',
                'min:0',
            ],
            'max_price' => [
                'sometimes',
                'numeric',
                'min:1',
                'gt:min_price',
            ],
            'per_page' => [
                'sometimes',
                'numeric',
                'min:1'
            ],
            'id' => [
                'sometimes',
                'integer',
                'exists:warehouses,id',
            ]
        ];
    }

    protected function prepareForValidation()
    {
        return $this->merge($this->route()->parameters);
    }

    public function getFilters(): array
    {
        return $this->safe()->except('per_page');
    }

    public function getPerPage(): int
    {
        return $this->input('per_page', 10);
    }
}
