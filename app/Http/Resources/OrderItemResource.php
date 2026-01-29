<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'drink_id' => $this->drink_id,
            'name' => $this->drink_name,
            'unit_price' => (float) $this->unit_price,
            'quantity' => (int) $this->quantity,
            'customizations' => $this->customizations,
            'item_total' => (float) ($this->unit_price * $this->quantity),
        ];
    }
}
