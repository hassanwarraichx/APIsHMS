<?php

namespace App\Http\Controllers\API\Prescription;

use App\DTOs\PrescriptionDTO\CreatePrescriptionDTO;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Prescription\StorePrescriptionRequest;
use App\Http\Resources\Prescription\PrescriptionResource;
use App\Models\Appointment;
use App\Services\Prescription\PrescriptionService;
use Illuminate\Http\JsonResponse;

class PrescriptionController extends Controller
{
    protected PrescriptionService $service;

    public function __construct(PrescriptionService $service)
    {
        $this->service = $service;
    }

    public function store(StorePrescriptionRequest $request): JsonResponse
    {
        try {
            $prescriptionResource = $this->service->handleCreation($request->validated());

            return ResponseHelper::success(
                $prescriptionResource,
                'Prescription saved successfully.',
                201
            );
        } catch (\Throwable $e) {
            return ResponseHelper::error(
                'Failed to save prescription.',
                500,
                $e->getMessage()
            );
        }
    }


    public function show(Appointment $appointment): JsonResponse
    {
        try {
            $prescription = $appointment->prescription;

            if (!$prescription) {
                return ResponseHelper::error('Prescription not found.', 404);
            }

            return ResponseHelper::success(
                new PrescriptionResource($prescription),
                'Prescription fetched successfully.'
            );
        } catch (\Throwable $e) {
            return ResponseHelper::error(
                'Failed to load prescription.',
                500,
                $e->getMessage()
            );
        }
    }

}
