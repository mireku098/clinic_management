<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;


class Patient extends Model
{
    use SoftDeletes;

    protected $table = 'patients';

    protected $fillable = [
        'patient_code',
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        'age',
        'phone',
        'email',
        'address',
        'occupation',
        'height',
        'marital_status',
        'emergency_contact_name',
        'emergency_contact_phone',
        'blood_group',
        'sickle_cell_status',
        'allergies',
        'chronic_conditions',
        'photo_path',
        'registered_at',
    ];

    public function visits()
    {
        return $this->hasMany(PatientVisit::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function packages()
    {
        return $this->hasMany(PatientPackage::class);
    }
}