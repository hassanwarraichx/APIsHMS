<?php

namespace App\DTOs\Auth;

namespace App\DTOs\Auth;


use App\DTOs\BaseDTO;

class RegisterDTO extends BaseDTO
{
    public string $name;
    public string $email;
    public string $password;
    public string $role;

    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->password = $data['password'];
        $this->role = $data['role'];
    }
}



