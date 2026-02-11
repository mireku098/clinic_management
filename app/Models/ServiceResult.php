<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceResult extends Model
{
    use HasFactory;

    protected $table = 'service_results';

    protected $fillable = [
        'service_id',
        'patient_id',
        'visit_id',
        'patient_service_id',
        'package_id',
        'patient_package_id',
        'result_type',
        'result_text',
        'result_numeric',
        'result_file_path',
        'result_file_name',
        'notes',
        'status',
        'recorded_by',
        'approved_by',
        'approved_at',
        'approval_notes',
    ];

    protected $casts = [
        'result_numeric' => 'decimal:2',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit()
    {
        return $this->belongsTo(PatientVisit::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function patientPackage()
    {
        return $this->belongsTo(PatientPackage::class);
    }

    public function patientService()
    {
        return $this->belongsTo(PatientService::class);
    }

    public function isEditable()
    {
        return in_array($this->status, ['draft', 'rejected', 'pending_approval']);
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function getResultValue()
    {
        switch ($this->result_type) {
            case 'numeric':
                return $this->result_numeric;
            case 'text':
                return $this->result_text;
            case 'file':
                return $this->result_file_name;
            default:
                return null;
        }
    }
}