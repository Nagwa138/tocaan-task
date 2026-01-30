<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockTransferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "source_warehouse" => $this->sourceWarehouse,
            "destination_warehouse" => $this->destinationWarehouse,
            "inventory_item" => $this->inventoryItem,
            "user" => $this->user,
            "quantity" => $this->quantity,
            "status" => $this->status,
            "notes" => $this->notes,
            "created_at" => $this->created_at
        ];
    }
}
