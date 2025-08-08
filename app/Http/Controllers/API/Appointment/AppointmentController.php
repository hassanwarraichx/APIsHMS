<?php

namespace App\Http\Controllers\API\Appointment;

use App\DTOs\AppointmentDTO\CreateAppointmentDTO;
use App\Filters\Appointment\DateFilter;
use App\Filters\Appointment\DoctorFilter;
use App\Filters\Appointment\PatientFilter;
use App\Filters\Appointment\StatusFilter;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\StoreAppointmentRequest;
use App\Http\Requests\Appointment\UpdateAppointmentStatusRequest;
use App\Http\Resources\Appointment\AppointmentResource;
use App\Models\Appointment;
use App\Notifications\AppointmentCreatedNotification;
use App\Notifications\AppointmentStatusChanged;
use App\Services\Appointment\AppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class AppointmentController extends Controller
{
    protected AppointmentService $service;

    public function __construct(AppointmentService $service)
    {
        $this->service = $service;
    }

    /**
     * Book a new appointment
     */
    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        try {
            $data = $request->all();

            if (Auth::user()->hasRole('patient')) {
                $data['patient_id'] = Auth::id(); // instead of Auth::user()->patientProfile->id;
            }

            $dto = new CreateAppointmentDTO($data);
            $appointment = $this->service->create($dto);


            return ResponseHelper::success(
                new AppointmentResource($appointment),
                'Appointment booked successfully.',
                201
            );
        } catch (\Throwable $e) {
            return ResponseHelper::error(
                'Failed to book appointment.',
                500,
                //"somthing went wrong",
                $e->getMessage(),
            );
        }
    }


    /**
     * Get all appointments for the authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $query = app(Pipeline::class)
                ->send($this->service->forUserQuery($user))
                ->through([
                    StatusFilter::class,
                    DoctorFilter::class,
                    PatientFilter::class,
                    DateFilter::class,
                ])
                ->thenReturn();

            $perPage = $request->get('per_page', 10);

            return ResponseHelper::success(
                AppointmentResource::collection($query->paginate($perPage)),
                'Appointments fetched successfully.'
            );
        } catch (\Throwable $e) {
            return ResponseHelper::error(
                'Failed to load appointments.',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Update appointment status (approve/reject)
     */
    public function updateStatus(UpdateAppointmentStatusRequest $request, Appointment $appointment): JsonResponse
    {
        try {
            $this->service->updateStatus($appointment, $request->status);

             // Notification logic
//             if ($appointment->patient && $appointment->patient->user) {
//                 $appointment->patient->user->notify(new AppointmentStatusChanged($appointment));
//             }
            if ($appointment->patient) {
                $appointment->patient->notify(new AppointmentStatusChanged($appointment));
            }


            return ResponseHelper::success(
                new AppointmentResource($appointment),
                'Appointment status updated successfully.'
            );
        } catch (\Throwable $e) {
            Log::error('Failed to update appointment status', ['error' => $e->getMessage()]);

            return ResponseHelper::error(
                'Failed to update status.',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Show a single appointment
     */
    public function show(Appointment $appointment): JsonResponse
    {
        return ResponseHelper::success(
            new AppointmentResource($appointment),
            'Appointment details fetched successfully.'
        );
    }

    /**
     * Delete an appointment
     */
    public function destroy(Appointment $appointment): JsonResponse
    {
        try {
            $this->service->delete($appointment);

            return ResponseHelper::success([], 'Appointment deleted successfully.');
        } catch (\Throwable $e) {
            Log::error('Failed to delete appointment', ['error' => $e->getMessage()]);

            return ResponseHelper::error('Failed to delete appointment.', 500, $e->getMessage());
        }
    }
}
