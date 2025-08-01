<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalHistory extends Model
{
    use HasFactory;
    protected $fillable = [
        'patient_id',
        'description',
        'document_path',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function patientProfile()
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }
}
