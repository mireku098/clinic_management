<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $table = 'packages';

    protected $fillable = [
        'package_code',
        'package_name',
        'category',
        'description',
        'duration_weeks',
        'total_cost',
        'status',
    ];

    public function services()
    {
        return $this->hasMany(PackageService::class);
    }
}