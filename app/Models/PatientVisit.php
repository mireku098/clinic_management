<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientVisit extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'patient_visits';

    public $timestamps = false;

    protected $fillable = [
        'patient_id',
        'chief_complaint',
        'history_present_illness',
        'assessment',
        'treatment_plan',
        'visit_date',
        'visit_time',
        'visit_type',
        'practitioner',
        'department',
        'temperature',
        'weight',
        'height',
        'blood_pressure',
        'heart_rate',
        'oxygen_saturation',
        'respiratory_rate',
        'bmi',
        'pulse_rate',
        'reason_for_visit',
        'notes',
        'attended_by',
        'created_at',
        'package_id',
        'total_amount',
        'selected_services',
        'selected_package',
        'payment_status',
        'amount_paid',
        'balance_due',
        'payment_method',
        'payment_date',
        'payment_reference',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'visit_time' => 'datetime',
        'temperature' => 'decimal:2',
        'weight' => 'decimal:2',
        'height' => 'decimal:2',
        'bmi' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'payment_date' => 'date',
        'created_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function attendingUser()
    {
        return $this->belongsTo(User::class, 'attended_by');
    }

    public function services()
    {
        return $this->hasMany(PatientService::class, 'visit_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function bill()
    {
        return $this->hasOne(Bill::class, 'visit_id');
    }
}