<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientVisit extends Model
{
    protected $table = 'patient_visits';

    public $timestamps = false;

    protected $fillable = [
        'patient_id',
        'visit_date',
        'visit_time',
        'visit_type',
        'temperature',
        'weight',
        'blood_pressure',
        'pulse_rate',
        'reason_for_visit',
        'notes',
        'attended_by',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'attended_by');
    }

    public function services()
    {
        return $this->hasMany(PatientService::class, 'visit_id');
    }
}