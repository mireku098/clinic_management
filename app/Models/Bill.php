<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Patient;
use App\Models\PatientVisit;
use App\Models\BillItem;
use App\Models\Payment;
use App\Models\User;

class Bill extends Model
{
    use HasFactory;

    protected $table = 'bills';

    public $timestamps = true;

    protected $fillable = [
        'patient_id',
        'visit_id',
        'bill_type',
        'total_amount',
        'amount_paid',
        'balance',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function visit()
    {
        return $this->belongsTo(PatientVisit::class, 'visit_id');
    }

    public function items()
    {
        return $this->hasMany(BillItem::class, 'bill_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}