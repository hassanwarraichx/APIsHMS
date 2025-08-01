<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;
    protected $fillable = [
        'appointment_id',
        'notes',
        'medications', // JSON
    ];
    protected $casts = [
        'medications' => 'array',
    ];
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }


}
