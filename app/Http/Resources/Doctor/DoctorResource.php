<?php

namespace App\Http\Resources\Doctor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
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
            'name'              => $this->name,
            'email'             => $this->email,
            'profile_picture'   => $this->profile_picture
                ? asset('storage/' . $this->profile_picture)
                : null,
            'specialization'    => $this->doctorProfile?->specialization?->name,
            'specialization_id' => $this->doctorProfile?->specialization_id,
            'availability'      => $this->doctorProfile?->availability ?? [],
            'created_at'        => $this->created_at?->toDateTimeString(),
        ];
    }
}
