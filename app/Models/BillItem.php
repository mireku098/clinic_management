<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Bill;
use App\Models\Service;
use App\Models\Package;

class BillItem extends Model
{
    use HasFactory;

    protected $table = 'bill_items';

    protected $fillable = [
        'bill_id',
        'service_id',
        'package_id',
        'description',
        'quantity',
        'unit_price',
        'total_price',
        'item_type',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    // Scopes for common queries
    public function scopeServices($query)
    {
        return $query->where('item_type', 'service');
    }

    public function scopePackages($query)
    {
        return $query->where('item_type', 'package');
    }
}
