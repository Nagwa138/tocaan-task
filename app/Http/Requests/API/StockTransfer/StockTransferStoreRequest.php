<?php

namespace App\Http\Requests\API\StockTransfer;

use Illuminate\Foundation\Http\FormRequest;

class StockTransferStoreRequest extends FormRequest
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
            'quantity' => [
                'required',
                'numeric',
                'min:1'
            ],
            'notes' => [
                'nullable',
                'string',
                'max:255'
            ],
            'source_warehouse_id' => [
                'required',
                'numeric',
                'exists:warehouses,id'
            ],
            'destination_warehouse_id' => [
                'required',
                'numeric',
                'exists:warehouses,id',
                'different:source_warehouse_id'
            ],
            'inventory_item_id' => [
                'required',
                'numeric',
                'exists:inventory_items,id'
            ]
        ];
    }
}
