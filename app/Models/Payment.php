<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';

    public $timestamps = false;

    protected $fillable = [
        'patient_id',
        'bill_id',
        'amount_before',
        'amount_paid',
        'balance_after',
        'payment_method',
        'received_by',
        'payment_date',
        'payment_time',
    ];

    protected $casts = [
        'amount_before' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'payment_date' => 'date',
        'payment_time' => 'datetime',
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'received_by', 'id');
    }
}