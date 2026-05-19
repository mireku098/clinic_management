<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (!$this->has('department')) {
            $this->merge(['department' => []]);
        }
    }

    public function rules(): array
    {
        $practitionerKeys = implode(',', array_keys(config('clinic.practitioners', [])));
        $departmentKeys = implode(',', array_keys(config('clinic.departments', [])));

        return [
            // Required fields
            'patient_id' => ['required', 'exists:patients,id'],
            'visit_date' => ['required', 'date', 'before_or_equal:today'],
            'visit_time' => ['required'],
            'visit_type' => ['required', 'in:appointment,walk-in,telemedicine'],
            'practitioner' => ['required', 'array', 'min:1'],
            'practitioner.*' => ['required', 'in:' . $practitionerKeys],

            // User fields (auto-filled)
            'user_id' => ['nullable', 'exists:users,id'],
            'attended_by' => ['nullable', 'string', 'max:255'],

            // Optional fields (multiple allowed)
            'department' => ['nullable', 'array'],
            'department.*' => ['in:' . $departmentKeys],
            
            // Package & Service Selection (At least one package or service)
            'package_id' => ['required_without:selected_services', 'nullable', 'exists:packages,id'],
            'selected_services' => ['required_without:package_id', 'nullable', 'string'],
            'total_amount' => ['nullable', 'numeric', 'min:0'],
            
            // Chief complaint (optional)
            'chief_complaint' => ['nullable', 'string', 'max:500'],
            
            // Vital signs (required as per user request)
            'blood_pressure' => ['required', 'regex:/^\d{2,3}\/\d{2,3}$/'], // Format: 120/80
            'temperature' => ['required', 'numeric'], // °C
            'weight' => ['required', 'numeric'], // kg
            // 'height' => ['nullable', 'numeric'], // cm - removed - now using patient's permanent height
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
            'visit_type.required' => 'Please select visit type.',
            'practitioner.required' => 'Please select at least one practitioner.',
            'practitioner.min' => 'Please select at least one practitioner.',
            'practitioner.*.in' => 'One or more selected practitioners are invalid.',
            'department.*.in' => 'One or more selected departments are invalid.',
            'blood_pressure.required' => 'Blood pressure is required.',
            'blood_pressure.regex' => 'Please enter blood pressure in the format 120/80 (systolic/diastolic).',
            'temperature.required' => 'Temperature is required.',
            'weight.required' => 'Weight is required.',
            'package_id.required_without' => 'Please select at least one package or one individual service.',
            'selected_services.required_without' => 'Please select at least one package or one individual service.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            $response = response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
            
            throw new \Illuminate\Validation\ValidationException($validator, $response);
        }

        session()->flash('swal', [
            'icon' => 'error',
            'title' => 'Validation Error',
            'text' => $validator->errors()->first(),
            'showConfirmButton' => true,
        ]);

        parent::failedValidation($validator);
    }
}
