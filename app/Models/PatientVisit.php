<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientVisit extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'patient_visits';

    public $timestamps = false;

    protected $fillable = [
        'patient_id',
        'user_id',
        'chief_complaint',
        'history_present_illness',
        'assessment',
        'treatment_plan',
        'visit_date',
        'visit_time',
        'visit_type',
        'status',
        'practitioner',
        'department',
        'temperature',
        'weight',
        'height',
        'blood_pressure',
        'oxygen_saturation',
        'respiratory_rate',
        'bmi',
        'pulse_rate',
        'reason_for_visit',
        'notes',
        'attended_by',
        'created_at',
        'package_id',
        'total_amount',
        'selected_services',
        'selected_package',
        'payment_status',
        'amount_paid',
        'balance_due',
        'payment_method',
        'payment_date',
        'payment_reference',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'visit_time' => 'datetime',
        'temperature' => 'decimal:2',
        'weight' => 'decimal:2',
        'height' => 'decimal:2',
        'bmi' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'payment_date' => 'date',
        'created_at' => 'datetime',
    ];

    public function scopeAppointments($query)
    {
        return $query->where('visit_type', 'appointment');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function isScheduledAppointment(): bool
    {
        return $this->visit_type === 'appointment' && $this->status === 'scheduled';
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function services()
    {
        return $this->hasMany(PatientService::class, 'visit_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    /**
     * Check if visit can be marked as completed
     * A visit is completed only if all associated services and packages are approved
     */
    public function canBeMarkedAsCompleted()
    {
        // Check if there are any associated service results that are not approved
        $serviceResults = $this->services()->with('result')->get();
        
        foreach ($serviceResults as $service) {
            if ($service->result && $service->result->status !== 'approved') {
                return false;
            }
        }
        
        // Check if package (if any) is approved
        if ($this->package) {
            $packageResults = \App\Models\ServiceResult::where('visit_id', $this->id)
                ->where('package_id', $this->package_id)
                ->get();
                
            foreach ($packageResults as $result) {
                if ($result->status !== 'approved') {
                    return false;
                }
            }
        }
        
        return true;
    }

    /**
     * Update visit status based on associated service/package approval status
     */
    public function updateCompletionStatus()
    {
        $newStatus = $this->canBeMarkedAsCompleted() ? 'completed' : 'pending';
        
        if ($this->status !== $newStatus) {
            $this->status = $newStatus;
            $this->save();
            
            // Log the status change
            \Log::info("Visit {$this->id} status updated to {$newStatus}", [
                'patient_id' => $this->patient_id,
                'visit_date' => $this->visit_date,
                'previous_status' => $this->getOriginal('status'),
                'new_status' => $newStatus
            ]);
        }
        
        return $newStatus;
    }

    public function bill()
    {
        return $this->hasOne(Bill::class, 'visit_id');
    }

    public function getPractitionerAttribute($value): array
    {
        return $this->decodeMultiValue($value);
    }

    public function setPractitionerAttribute($value): void
    {
        $this->attributes['practitioner'] = $this->encodeMultiValue($value);
    }

    public function getDepartmentAttribute($value): array
    {
        return $this->decodeMultiValue($value);
    }

    public function setDepartmentAttribute($value): void
    {
        $this->attributes['department'] = $this->encodeMultiValue($value);
    }

    public function getPractitionerDisplayAttribute(): string
    {
        return $this->formatOptionLabels($this->practitioner, config('clinic.practitioners', []));
    }

    public function getDepartmentDisplayAttribute(): string
    {
        return $this->formatOptionLabels($this->department, config('clinic.departments', []));
    }

    protected function decodeMultiValue($value): array
    {
        if (empty($value)) {
            return [];
        }

        if (is_array($value)) {
            return array_values($value);
        }

        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return array_values($decoded);
        }

        return [$value];
    }

    protected function encodeMultiValue($value): ?string
    {
        $items = $this->decodeMultiValue($value);

        if (empty($items)) {
            return null;
        }

        return json_encode(array_values(array_unique($items)));
    }

    protected function formatOptionLabels(array $keys, array $options): string
    {
        if (empty($keys)) {
            return 'Not assigned';
        }

        $labels = array_map(function ($key) use ($options) {
            return $options[$key] ?? ucwords(str_replace('-', ' ', $key));
        }, $keys);

        return implode(', ', $labels);
    }
}