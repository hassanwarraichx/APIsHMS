<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientProfile extends Model
{
    use HasFactory,softDeletes;

    protected $fillable = [
        'user_id',
        'dob',
        'gender',
        'address',
        'phone',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
    public function medicalHistories()
    {
        return $this->hasMany(MedicalHistory::class, 'patient_id');
    }

}
