<?php

namespace App\DTOs\PrescriptionDTO;

use App\DTOs\BaseDTO;

class CreatePrescriptionDTO extends BaseDTO
{
    public int $appointment_id;
    public ?string $notes;
    public array $medications;

    public function __construct(array $data)
    {
        $this->appointment_id = $data['appointment_id'];
        $this->notes = $data['notes'] ?? null;
        $this->medications = $data['medications'];
    }

}
