<?php

namespace App\DTOs\DoctorDTO;

use App\DTOs\BaseDTO;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CreateDoctorDTO extends BaseDTO
{
    public string $name;
    public string $email;
    public string $password;
    public ?object $profile_picture;
    public int $specialization_id;
    public array $availability;

    public function __construct(Request $request)
    {
        $this->name = $request->name;
        $this->email = $request->email;
        $this->password = Hash::make($request->password);
        $this->profile_picture = $request->file('profile_picture');
        $this->specialization_id = $request->specialization_id;
        $this->availability = $request->availability ?? [];
    }
}
