<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DoctorProfile extends Model
{
    use HasFactory,softDeletes;
    protected $fillable = [
        'user_id',
        'specialization_id',
        'availability', // stored as JSON
    ];

    protected $casts = [
        'availability' => 'array',
    ];

    public function specialization()
    {
        return $this->belongsTo(Specialization::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }

}
