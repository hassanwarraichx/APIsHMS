<?php

namespace App\Services\Doctor;

use App\DTOs\DoctorDTO\CreateDoctorDTO;
use App\DTOs\DoctorDTO\UpdateDoctorDTO;
use App\Helpers\ResponseHelper;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class DoctorService
{
    public function create(CreateDoctorDTO $dto): void
    {
        DB::beginTransaction();

        try {
            $userData = [
                'name' => $dto->name,
                'email' => $dto->email,
                'password' => $dto->password,
            ];

            if ($dto->profile_picture instanceof UploadedFile) {
                $path = $dto->profile_picture->store('public/profile_picture');
                $userData['profile_picture'] = str_replace('public/', '', $path);
            }

            $user = User::create($userData);
            $user->assignRole('doctor');

            $cleanedAvailability = $this->sanitizeAvailability($dto->availability);

            $user->doctorProfile()->create([
                'specialization_id' => $dto->specialization_id,
                'availability' => $cleanedAvailability,
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            //throw $e;
            //return ResponseHelper::error("Failed to create a doctor",422);
        }
    }

    public function update(UpdateDoctorDTO $dto): void
    {
        DB::beginTransaction();

        try {
            $user = User::findOrFail($dto->user_id);

            $userData = [];

            if (!empty($dto->name)) {
                $userData['name'] = $dto->name;
            }

            if (!empty($dto->email)) {
                $userData['email'] = $dto->email;
            }

            if (!empty($dto->password)) {
                $userData['password'] = Hash::make($dto->password);
            }

            if ($dto->profile_picture instanceof UploadedFile) {
                $path = $dto->profile_picture->store('public/profile_picture');
                $userData['profile_picture'] = str_replace('public/', '', $path);
            }

            if (!empty($userData)) {
                $user->update($userData);
            }

            $profileData = [];

            if (!empty($dto->specialization_id)) {
                $profileData['specialization_id'] = $dto->specialization_id;
            }

            if (is_array($dto->availability)) {
                $cleanedAvailability = [];

                foreach ($dto->availability as $day => $slots) {
                    $validSlots = [];

                    foreach ($slots as $slot) {
                        if (!empty($slot['start']) && !empty($slot['end'])) {
                            $validSlots[] = [
                                'start' => $slot['start'],
                                'end' => $slot['end'],
                            ];
                        }
                    }

                    if (!empty($validSlots)) {
                        $cleanedAvailability[$day] = $validSlots;
                    }
                }

                $profileData['availability'] = $cleanedAvailability;
            }

            if (!empty($profileData)) {
                $user->doctorProfile->update($profileData);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            //throw $e;
        }
    }


    private function sanitizeAvailability(array $availability): array
    {
        $cleaned = [];

        foreach ($availability as $day => $slots) {
            $validSlots = [];

            foreach ($slots as $slot) {
                if (!empty($slot['start']) && !empty($slot['end'])) {
                    $validSlots[] = [
                        'start' => $slot['start'],
                        'end' => $slot['end'],
                    ];
                }
            }

            if (!empty($validSlots)) {
                $cleaned[$day] = $validSlots;
            }
        }

        return $cleaned;
    }
}
