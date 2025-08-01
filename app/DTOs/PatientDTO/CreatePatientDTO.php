<?php

namespace App\DTOs\PatientDTO;

use App\DTOs\BaseDTO;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;

class CreatePatientDTO extends BaseDTO
{
    public string $name;
    public string $email;
    public string $password;
    public string $dob;
    public string $gender;
    public string $address;
    public ?string $phone;
    public ?UploadedFile $profile_picture;
    public array $medical_histories;

    public function __construct(Request $request)
    {
        $this->name = $request->name;
        $this->email = $request->email;
        $this->password = $request->password;
        $this->dob = $request->dob;
        $this->gender = $request->gender;
        $this->address = $request->address;
        $this->phone = $request->phone;
        $this->profile_picture = $request->file('profile_picture');
        $this->medical_histories = is_array($request->medical_histories) ? $request->medical_histories : [];
    }
}
