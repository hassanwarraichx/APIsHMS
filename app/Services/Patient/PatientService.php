<?php

namespace App\Services\Patient;

use App\DTOs\PatientDTO\CreatePatientDTO;
use App\DTOs\PatientDTO\UpdatePatientDTO;
use App\Models\MedicalHistory;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PatientService
{
    public function create(CreatePatientDTO $dto): User
    {
        DB::beginTransaction();

        try {
            $data = $dto->toArray();

            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ];

            if ($dto->profile_picture) {
                $path = $dto->profile_picture->store('public/profile_picture');
                $userData['profile_picture'] = str_replace('public/', '', $path);
            }

            $user = User::create($userData);
            $user->assignRole('patient');

            $profileData = [
                'dob'     => $data['dob'],
                'gender'  => $data['gender'],
                'address' => $data['address'],
                'phone'   => $data['phone'] ?? null,
            ];

            $patientProfile = $user->patientProfile()->create($profileData);

            foreach ($data['medical_histories'] ?? [] as $history) {
                $description = $history['description'] ?? null;
                $document = $history['document'] ?? null;

                if (empty($description) && empty($document)) continue;

                $path = $document ? $document->store('medical_documents', 'public') : null;

                $patientProfile->medicalHistories()->create([
                    'description'    => $description,
                    'document_path'  => $path,
                ]);
            }

            DB::commit();
            return $user;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(UpdatePatientDTO $dto): void
    {
        DB::beginTransaction();

        try {
            $data = $dto->toArray();

            $user = User::findOrFail($dto->user_id);
            $patientProfile = $user->patientProfile;

            $userUpdateData = [];
            if (!empty($data['email'])) {
                $userUpdateData['email'] = $data['email'];
            }
            if (!empty($data['name'])) {
                $userUpdateData['name'] = $data['name'];
            }

            if (!empty($userUpdateData)) {
                $user->update($userUpdateData);
            }

            $profileUpdateData = [];
            if (!empty($data['address'])) {
                $profileUpdateData['address'] = $data['address'];
            }
            if (!empty($data['phone'])) {
                $profileUpdateData['phone'] = $data['phone'];
            }

            if (!empty($profileUpdateData)) {
                $patientProfile->update($profileUpdateData);
            }

            $existingIds = $patientProfile->medicalHistories()->pluck('id')->toArray();
            $submittedIds = [];

            foreach ($data['medical_histories'] ?? [] as $entry) {
                if (!empty($entry['id'])) {
                    $submittedIds[] = $entry['id'];
                    $history = MedicalHistory::find($entry['id']);

                    if ($history) {
                        $history->description = $entry['description'] ?? $history->description;

                        if (!empty($entry['document']) && $entry['document'] instanceof UploadedFile) {
                            $path = $entry['document']->store('public/medical_documents');
                            $history->document_path = str_replace('public/', '', $path);
                        }

                        $history->save();
                    }
                } else {
                    $patientProfile->medicalHistories()->create([
                        'description'   => $entry['description'] ?? '',
                        'document_path' => !empty($entry['document']) && $entry['document'] instanceof UploadedFile
                            ? str_replace('public/', '', $entry['document']->store('public/medical_documents'))
                            : null,
                    ]);
                }
            }

            $toDelete = array_diff($existingIds, $submittedIds);
            if (!empty($toDelete)) {
                MedicalHistory::whereIn('id', $toDelete)->delete();
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
