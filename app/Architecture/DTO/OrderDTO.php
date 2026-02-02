<?php

namespace App\Architecture\DTO;

class OrderDTO extends DataTransferObject
{
    public array $items = [];
    public string $notes = "";
    public string $status = "";

    static public function fromRequest(array $request): self
    {
        return new self(
            [
                'items' => $request['items'],
                'user_id' => auth()->id(),
                'notes' => $request['notes'] ?? null,
            ]
        );
    }
}
