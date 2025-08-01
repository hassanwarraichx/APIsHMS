<?php

namespace App\Http\Resources\Billing;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'appointment_id'    => $this->appointment_id,
            'consultation_fee'  => $this->consultation_fee,
            'medicine_fee'      => $this->medicine_fee,
            'lab_fee'           => $this->lab_fee,
            'total'             => $this->total,
            'created_at'        => $this->created_at->toDateTimeString(),
            'updated_at'        => $this->updated_at->toDateTimeString(),
        ];
    }
}
