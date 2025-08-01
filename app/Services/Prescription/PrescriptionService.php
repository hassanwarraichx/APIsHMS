<?php

namespace App\Services\Prescription;

use App\DTOs\PrescriptionDTO\CreatePrescriptionDTO;
use App\Http\Resources\Prescription\PrescriptionResource;
use App\Models\Prescription;
use Illuminate\Support\Facades\DB;

class PrescriptionService
{
    public function handleCreation(array $validated): PrescriptionResource
    {
        return new PrescriptionResource(
            DB::transaction(function () use ($validated) {
                $dto = new CreatePrescriptionDTO($validated);

                $data = $dto->toArray();
                $data['medications'] = json_encode($dto->medications);

                $prescription = Prescription::create($data);

                return $prescription;
            })
        );
    }


}
