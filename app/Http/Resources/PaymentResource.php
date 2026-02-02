<?php

namespace App\Http\Resources;

class PaymentResource extends AbstractResource
{
    /**
     * Format a single item
     */
    protected function formatItem($item): array
    {
        return [
            'id' => $item->id,
            'payment_id' => $item->payment_id,
            'status' => $item->status,
            'method' => $item->method,
            'amount' => $item->amount,
            'order' => new OrderResource($item->order),
            'gateway_response' => $item->gateway_response,
            'created_at' => $item->created_at?->format('F j, Y g:i A'),
            'updated_at' => $item->updated_at?->format('F j, Y g:i A'),
        ];
    }
}
