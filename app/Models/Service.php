<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'services';

    protected $fillable = [
        'service_code',
        'service_name',
        'category',
        'price',
        'result_type',
        'description',
        'status',
    ];

    public function patientServices()
    {
        return $this->hasMany(PatientService::class);
    }
}