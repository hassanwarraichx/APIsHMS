<?php

namespace App\DTOs\DoctorDTO;

use App\DTOs\BaseDTO;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;

class UpdateDoctorDTO extends BaseDTO
{
    public int $user_id;
    public ?string $name;
    public ?string $email;
    public ?string $password;
    public ?UploadedFile $profile_picture;
    public ?int $specialization_id;
    public ?array $availability; // <-- CHANGE from array to ?array

    public function __construct(Request $request, int $user_id)
    {
        //dd($request->all());
        $this->user_id = $user_id;
        $this->name = $request->input('name');
        $this->email = $request->input('email');
        $this->password = $request->filled('password') ? $request->input('password') : null;
        $this->profile_picture = $request->file('profile_picture');
        $this->specialization_id = $request->input('specialization_id');
        $this->availability = $request->input('availability'); // null or array
    }
}
