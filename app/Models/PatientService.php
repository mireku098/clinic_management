<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientService extends Model
{
    protected $table = 'patient_services';

    public $timestamps = false;

    protected $fillable = [
        'patient_id',
        'visit_id',
        'service_id',
        'service_price',
        'status',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit()
    {
        return $this->belongsTo(PatientVisit::class, 'visit_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function result()
    {
        return $this->hasOne(ServiceResult::class);
    }
}
 