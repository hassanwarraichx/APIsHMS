<?php

namespace App\Http\Resources\Patient;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'email'     => $this->email,
            'profile_picture' => $this->profile_picture ? asset("storage/{$this->profile_picture}") : null,
            'dob'       => $this->patientProfile->dob ?? null,
            'gender'    => $this->patientProfile->gender ?? null,
            'address'   => $this->patientProfile->address ?? null,
            'phone'     => $this->patientProfile->phone ?? null,
            'created_at'=> $this->created_at,
        ];
    }
}
