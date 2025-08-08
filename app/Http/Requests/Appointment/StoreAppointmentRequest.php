<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    public function validationData()
    {
        $data = $this->all();

        if (auth()->check() && auth()->user()->hasRole('patient')) {
            $data['patient_id'] = auth()->user()->patientProfile->id;
            //dd($data['patient_id']);
        }

        return $data;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //'patient_id' => ['required', 'exists:users,id'],
            'doctor_id' => ['required', 'exists:users,id'],
            'appointment_time' => ['required', 'date', 'after:now'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
