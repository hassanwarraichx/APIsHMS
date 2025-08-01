<?php

namespace App\Http\Controllers\API;

use App\DTOs\PatientDTO\CreatePatientDTO;
use App\DTOs\PatientDTO\UpdatePatientDTO;
use App\Filters\User\NameFilter;
use App\Filters\User\RoleFilter;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\StorePatientRequest;
use App\Http\Requests\Patient\UpdatePatientRequest;
use App\Http\Resources\Patient\PatientResource;
use App\Models\User;
use App\Services\Patient\PatientService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;

class PatientController extends Controller
{
    protected PatientService $patientService;

    public function __construct(PatientService $patientService)
    {
        $this->patientService = $patientService;
    }


    public function index(Request $request)
    {
        $query = app(Pipeline::class)
            ->send(User::query()->role('patient'))
            ->through([
                NameFilter::class,
            ])
            ->thenReturn();


        $perPage = $request->get('per_page', 10);

        $paginated = $query->paginate($perPage);
        return ResponseHelper::success(
            PatientResource::collection($paginated),
            'Filtered patient list'
        );
    }

    public function store(StorePatientRequest $request)
    {
        $validated = $request->validated();

        // Handle nested file uploads
        $medicalHistories = [];
        if ($request->has('medical_histories')) {
            foreach ($request->medical_histories as $index => $history) {
                $medicalHistories[] = [
                    'description' => $history['description'],
                    'document' => $request->file("medical_histories.$index.document") ?? null,
                ];
            }
        }

        $request->merge(['medical_histories' => $medicalHistories]);
        $dto = new CreatePatientDTO($request);
        $user = $this->patientService->create($dto);

        return ResponseHelper::success(new PatientResource($user->load('patientProfile')), 'Patient created successfully.');
    }

    public function show($id)
    {
        try {
            $patient = User::where('id', $id)
                ->role('patient')
                ->with('patientProfile')
                ->firstOrFail();

            return ResponseHelper::success(new PatientResource($patient), 'Patient retrieved successfully.');
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error('Patient not found or does not have the required role.', 404);
        }
    }

    public function update(UpdatePatientRequest $request, $id)
    {
        $dto = new UpdatePatientDTO($request, $id);
        $this->patientService->update($dto);

        $patient = User::role('patient')->with('patientProfile')->findOrFail($id);
        return ResponseHelper::success(new PatientResource($patient), 'Patient updated successfully.');
    }

    public function destroy($id)
    {
        $patient = User::role('patient')->findOrFail($id);
        $patient->delete();

        return ResponseHelper::success(null, 'Patient deleted successfully.');
    }
}
