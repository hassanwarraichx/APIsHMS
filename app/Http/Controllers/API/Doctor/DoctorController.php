<?php

namespace App\Http\Controllers\API\Doctor;

use App\DTOs\DoctorDTO\CreateDoctorDTO;
use App\DTOs\DoctorDTO\UpdateDoctorDTO;
use App\Filters\User\NameFilter;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\StoreDoctorRequest;
use App\Http\Requests\Doctor\UpdateDoctorRequest;
use App\Http\Resources\Doctor\DoctorResource;
use App\Models\User;
use App\Services\Doctor\DoctorService;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pipeline\Pipeline;

class DoctorController extends Controller
{
    protected DoctorService $doctorService;

    public function __construct(DoctorService $doctorService)
    {
        $this->doctorService = $doctorService;
    }

    public function index(Request $request)
    {
        $query = app(Pipeline::class)
            ->send(User::query()->role('doctor')->latest())
            ->through([
                NameFilter::class,
            ])
            ->thenReturn();

        $perPage = $request->get('per_page', 25);
        $paginated = $query->paginate($perPage);
        return ResponseHelper::success(
            DoctorResource::collection($paginated),
            'Filtered doctor list'
        );
    }

    public function store(StoreDoctorRequest $request)
    {
        $dto = new CreateDoctorDTO($request);
        $this->doctorService->create($dto);

        return ResponseHelper::success(null, 'Doctor created successfully.');
    }

    public function show($id)
    {
        try {
            $doctor = User::where('id', $id)
                ->role('doctor')
                ->with('doctorProfile.specialization')
                ->firstOrFail();

            return ResponseHelper::success(new DoctorResource($doctor), 'Doctor retrieved successfully.');
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error('Doctor not found or does not have the required role.', 404);
        }
    }

    public function update(UpdateDoctorRequest $request, $id)
    {
        //dd($request->all());
        $user = User::where('id',$id)->exists();
        if (!$user) {
            return ResponseHelper::error('Doctor not found or does not have the required role.', 404);
        }

        $data = $request->validated();
        $data['user_id'] = $id;

        $dto = new UpdateDoctorDTO($request,$id);
        $this->doctorService->update($dto);

        $updatedDoctor = User::role('doctor')->with('doctorProfile.specialization')->findOrFail($id);
        return ResponseHelper::success(new DoctorResource($updatedDoctor), 'Doctor updated successfully.');
    }

    public function destroy($id)
    {
        $doctor = User::withTrashed()->find($id);

        if (!$doctor) {
            return ResponseHelper::error('Patient not found.', 404);
        }

        if ($doctor->trashed()) {
            return ResponseHelper::error('Patient is already deleted.', 400);
        }

        if (!$doctor->hasRole('doctor')) {
            return ResponseHelper::error('The user is not a patient.', 400);
        }

        $doctor->delete();

        return ResponseHelper::success(null, 'Doctor deleted successfully.');
    }




}
