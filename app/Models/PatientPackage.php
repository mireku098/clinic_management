<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientPackage extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'patient_id',
        'package_id',
        'visit_id',
        'start_date',
        'status',
        'created_at'
    ];
    
    protected $casts = [
        'start_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
