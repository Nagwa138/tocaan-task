<?php

namespace App\Http\Resources;

class OrderResource extends AbstractResource
{
    /**
     * Format a single item
     */
    protected function formatItem($item): array
    {
        return [
            'id' => $item->id,
            'order_number' => $item->order_number,
            'status' => $item->status,
            'total_amount' => (float) $item->total_amount,
            'items' => $item->items,
            'notes' => $item->notes,
            'created_at' => $item->created_at?->format('F j, Y g:i A'),
            'updated_at' => $item->updated_at?->format('F j, Y g:i A'),
            'user' => new UserResource($item->user),
        ];
    }
}
