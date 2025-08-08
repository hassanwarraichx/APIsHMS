<?php

namespace App\DTOs\AppointmentDTO;

use App\DTOs\BaseDTO;

class CreateAppointmentDTO extends BaseDTO
{
    public int $patient_id;
    public int $doctor_id;
    public string $appointment_time;
    public ?string $notes;

    public function __construct(array $data)
    {
        $this->patient_id       = auth()->id(); // ✅ Assign from authenticated user
        $this->doctor_id = $data['doctor_id'];
        $this->appointment_time = $data['appointment_time'];
        $this->notes = $data['notes'] ?? null;
    }


}
