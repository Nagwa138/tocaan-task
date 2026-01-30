<?php

namespace App\Architecture\DTO;

class StockTransferDTO extends DataTransferObject
{
    public int $source_warehouse_id;
    public int $destination_warehouse_id;
    public int $inventory_item_id;
    public int $user_id;
    public ?int $quantity;

    static public function fromRequest(array $request): self
    {
        return new self(
            [
                'source_warehouse_id' => $request['source_warehouse_id'],
                'destination_warehouse_id' => $request['destination_warehouse_id'],
                'inventory_item_id' => $request['inventory_item_id'],
                'user_id' => auth()->id(),
                'notes' => $request['notes'] ?? null,
                'quantity' => $request['quantity'] ?? 1,
            ]
        );
    }
}
