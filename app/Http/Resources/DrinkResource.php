<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DrinkResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => (float) $this->price,
            'category' => $this->category,
            'image_url' => $this->image_url,
            'is_featured' => (bool) $this->is_featured,
            'description' => $this->description,
            'created_at' => $this->created_at,
        ];
    }
}
