<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
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

        $this->merge(['visit_type' => 'appointment']);
    }

    public function rules(): array
    {
        $practitionerKeys = implode(',', array_keys(config('clinic.practitioners', [])));
        $departmentKeys = implode(',', array_keys(config('clinic.departments', [])));

        $dateRule = $this->route('appointment')
            ? 'required|date'
            : 'required|date|after_or_equal:today';

        return [
            'patient_id' => ['required', 'exists:patients,id'],
            'visit_date' => [$dateRule],
            'visit_time' => ['required'],
            'visit_type' => ['required', 'in:appointment'],
            'practitioner' => ['required', 'array', 'min:1'],
            'practitioner.*' => ['required', 'in:' . $practitionerKeys],
            'department' => ['nullable', 'array'],
            'department.*' => ['in:' . $departmentKeys],
            'reason_for_visit' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.required' => 'Please select a patient.',
            'visit_date.after_or_equal' => 'Appointment date cannot be in the past.',
            'visit_time.required' => 'Appointment time is required.',
            'practitioner.required' => 'Please select at least one practitioner.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            throw new \Illuminate\Validation\ValidationException(
                $validator,
                response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422)
            );
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
