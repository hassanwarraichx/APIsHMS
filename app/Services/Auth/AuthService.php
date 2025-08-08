<?php

namespace App\Services\Auth;

use App\DTOs\Auth\RegisterDTO;
use App\Helpers\ResponseHelper;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(RegisterDTO $dto): User
    {
        //try{
            return DB::transaction(function () use ($dto) {
                $user = User::create([
                    'name'     => $dto->name,
                    'email'    => $dto->email,
                    'password' => Hash::make($dto->password),
                ]);

                $user->assignRole($dto->role);

                if ($dto->role === 'patient') {
                    $user->patientProfile()->create();
                } elseif ($dto->role === 'doctor') {
                    $defaultSpecializationId = 1;
                    $user->doctorProfile()->create([
                        'specialization_id' => $defaultSpecializationId,
                    ]);
                }


                return $user;
            });
//        }catch (\Throwable $exception){
//            return ResponseHelper::error("something went wrong while registering",500);
//        }

    }

}
