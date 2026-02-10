<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageService extends Model
{
    protected $table = 'package_services';

    protected $fillable = [
        'package_id',
        'service_id',
        'unit_price',
        'frequency_type',
        'frequency_value',
        'sessions',
        'service_total'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'service_total' => 'decimal:2',
        'sessions' => 'integer',
        'frequency_value' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
