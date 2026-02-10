<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Required fields
            'patient_id' => ['required', 'exists:patients,id'],
            'visit_date' => ['required', 'date', 'before_or_equal:today'],
            'visit_time' => ['required', 'date_format:H:i:s'],
            'visit_type' => ['required', 'in:appointment,walk-in'],
            'practitioner' => ['required', 'in:dr-smith,dr-johnson,dr-williams,therapist-brown,therapist-davis'],
            
            // Optional fields
            'department' => ['nullable', 'in:general,physiotherapy,consultation,emergency'],
            
            // Package & Service Selection (optional)
            'selected_package' => ['nullable', 'string'],
            'selected_services' => ['nullable', 'string'],
            'total_amount' => ['nullable', 'numeric', 'min:0'],
            'package_id' => ['nullable', 'exists:packages,id'],
            
            // Chief complaint (optional)
            'chief_complaint' => ['nullable', 'string', 'max:500'],
            
            // Vital signs (optional - no range constraints to allow abnormal readings)
            'blood_pressure' => ['nullable', 'regex:/^\d{2,3}\/\d{2,3}$/'], // Format: 120/80
            'temperature' => ['nullable', 'numeric'], // °C - no range limits
            'weight' => ['nullable', 'numeric'], // kg - no range limits
            // 'height' => ['nullable', 'numeric'], // cm - removed - now using patient's permanent height
            'heart_rate' => ['nullable', 'integer'], // bpm - no range limits
            'oxygen_saturation' => ['nullable', 'integer'], // % - no range limits
            'respiratory_rate' => ['nullable', 'integer'], // breaths per minute - no range limits
            'pulse_rate' => ['nullable', 'integer'], // bpm - no range limits
            'bmi' => ['nullable', 'numeric', 'min:0', 'max:999.99'], // calculated - limited to database range
            
            // Clinical notes (optional)
            'reason_for_visit' => ['nullable', 'string', 'max:1000'],
            'history_present_illness' => ['nullable', 'string', 'max:2000'],
            'assessment' => ['nullable', 'string', 'max:2000'],
            'treatment_plan' => ['nullable', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.required' => 'Please select a patient.',
            'patient_id.exists' => 'Selected patient not found.',
            'visit_date.required' => 'Visit date is required.',
            'visit_date.before_or_equal' => 'Visit date cannot be in the future.',
            'visit_time.required' => 'Visit time is required.',
            'visit_time.date_format' => 'Please enter a valid time (HH:MM).',
            'visit_type.required' => 'Please select visit type.',
            'practitioner.required' => 'Please select a practitioner.',
            'blood_pressure.regex' => 'Please enter blood pressure in the format 120/80 (systolic/diastolic).',
        ];
    }
}
