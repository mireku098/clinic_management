<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $table = 'packages';

    protected $fillable = [
        'package_name',
        'description',
        'category',
        'duration_weeks',
        'total_cost',
        'status',
        'package_code'
    ];

    protected $casts = [
        'total_cost' => 'decimal:2',
        'duration_weeks' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function services()
    {
        return $this->hasMany(PackageService::class);
    }

    public function packageServices()
    {
        return $this->hasMany(PackageService::class);
    }
}