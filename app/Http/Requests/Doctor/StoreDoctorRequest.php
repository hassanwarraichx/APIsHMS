<?php

namespace App\Http\Requests\Doctor;

use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
            'specialization_id' => ['required', 'exists:specializations,id'],
            'availability' => ['nullable', 'array'],
            'availability.*' => ['array'],
            'availability.*.*.start' => ['nullable', 'date_format:H:i'],
            'availability.*.*.end' => ['nullable', 'date_format:H:i'],
        ];
    }
}
