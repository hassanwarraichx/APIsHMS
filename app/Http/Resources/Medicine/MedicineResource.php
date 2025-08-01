<?php

namespace App\Http\Resources\Medicine;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'brand'        => $this->brand,
            'stock'        => $this->stock,
            'expiry_date'  => $this->expiry_date?->format('Y-m-d'),
            'price'        => $this->price,
        ];
    }
}
