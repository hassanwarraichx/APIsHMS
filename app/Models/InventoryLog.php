<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'medicine_id',
        'action',   // e.g., 'added', 'used', 'expired'
        'quantity',
    ];
}
