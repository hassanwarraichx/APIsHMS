<?php

namespace App\DTOs\PatientDTO;

use App\DTOs\BaseDTO;
use Illuminate\Http\Request;

class UpdatePatientDTO extends BaseDTO
{
    public int $user_id;
    public ?string $name;
    public ?string $email;
    public ?string $address;
    public ?string $phone;
    public array $medical_histories;

    public function __construct(Request $request, $user_id)
    {
        $this->user_id = (int) $user_id;
        $this->name = $request->name;
        $this->email = $request->email;
        $this->address = $request->address;
        $this->phone = $request->phone;
        $this->medical_histories = is_array($request->medical_histories) ? $request->medical_histories : [];
    }

}
