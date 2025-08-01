<?php

namespace App\Services\Appointment;

use App\DTOs\AppointmentDTO\CreateAppointmentDTO;
use App\Jobs\SendAppointmentNotificationJob;
use App\Jobs\SendAppointmentStatusChangedNotificationJob;
use App\Models\Appointment;
use App\Models\User;
use App\Notifications\AppointmentCreatedNotification;
use App\Notifications\AppointmentStatusChanged;
use Illuminate\Support\Facades\DB;

class AppointmentService
{
    public function forUserQuery(?User $user = null)
    {
        $user = $user ?? auth()->user();

        if ($user->hasRole('admin')) {
            return Appointment::with(['patient.user', 'doctor.user'])->latest();
        }

        if ($user->hasRole('doctor') && $user->doctorProfile) {
            return Appointment::where('doctor_id', $user->doctorProfile->id)
                ->with(['patient.user'])
                ->latest();
        }

        if ($user->hasRole('patient') && $user->patientProfile) {
            return Appointment::where('patient_id', $user->patientProfile->id)
                ->with(['doctor.user'])
                ->latest();
        }

        return Appointment::query()->whereRaw('0 = 1');
    }



    public function create(CreateAppointmentDTO $dto): Appointment
    {
        return DB::transaction(function () use ($dto) {
            $conflict = Appointment::where('doctor_id', $dto->doctor_id)
                ->where('appointment_time', $dto->appointment_time)
                ->where('status', '!=', 'rejected')
                ->exists();

            if ($conflict) {
                throw new \Exception('This time slot is already booked for the selected doctor.');
            }

            $data = $dto->toArray();
            $data['status'] = 'pending';

            $appointment = Appointment::create($data);

            dispatch(new SendAppointmentNotificationJob($appointment));


            return $appointment;
        });
    }



    public function updateStatus(Appointment $appointment, string $status): Appointment
    {
        DB::beginTransaction();

        try {
            $appointment->status = $status;
            $appointment->save();

            dispatch(new SendAppointmentStatusChangedNotificationJob($appointment));


            DB::commit();
            return $appointment;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete an appointment.
     */
    public function delete(Appointment $appointment): void
    {
        $appointment->delete();
    }

    /**
     * Get all appointments for a doctor.
     */
    public function forDoctor(int $doctorId)
    {
        return Appointment::where('doctor_id', $doctorId)->latest()->get();
    }

    /**
     * Get all appointments for a patient.
     */
    public function forPatient(int $patientId)
    {
        return Appointment::where('patient_id', $patientId)->latest()->get();
    }
}
