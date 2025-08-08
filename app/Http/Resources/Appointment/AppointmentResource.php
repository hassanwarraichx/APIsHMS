<?php

namespace App\Http\Resources\Appointment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the appointment resource into an array.
     *
     * @param Request $request
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'appointment_time' => $this->appointment_time ? $this->appointment_time->format('d M Y, h:i A') : null,
            'status'           => $this->status,
            'notes'            => $this->notes,

            'doctor' => [
                'name'  => optional($this->doctor)->name,
                'email' => optional($this->doctor)->email,
            ],

            'patient' => [
                'name'  => optional($this->patient)->name,
                'email' => optional($this->patient)->email,
            ],

            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
