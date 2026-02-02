<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceResult extends Model
{
    protected $table = 'service_results';

    public $timestamps = false;

    protected $fillable = [
        'patient_service_id',
        'result_text',
        'result_file_path',
        'recorded_by',
    ];

    public function patientService()
    {
        return $this->belongsTo(PatientService::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}