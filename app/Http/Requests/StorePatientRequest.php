<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePatientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $patientId = $this->route('patient');
        
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'gender' => ['required', Rule::in(['male', 'female', 'other'])],
            'date_of_birth' => ['required', 'date', 'before_or_equal:today'],
            'age' => ['nullable', 'integer', 'min:0', 'max:120'],
            'phone' => ['required', 'regex:/^0[0-9]{9}$/', Rule::unique('patients')->ignore($patientId)],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'marital_status' => ['nullable', Rule::in(['single', 'married', 'divorced', 'widowed'])],
            'blood_group' => ['nullable', Rule::in(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])],
            'sickle_cell_status' => ['nullable', Rule::in(['AA', 'AS', 'SS', 'Unknown'])],
            'allergies' => ['nullable', 'string', 'max:1000'],
            'chronic_conditions' => ['nullable', 'string', 'max:1000'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'regex:/^0[0-9]{9}$/'],
            'patient_photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Please enter a valid Ghanaian phone number (e.g., 0201234567).',
            'emergency_contact_phone.regex' => 'Please enter a valid emergency contact number (e.g., 0201234567).',
            'patient_photo.image' => 'The file must be an image.',
            'patient_photo.mimes' => 'The photo must be a JPEG, JPG, or PNG file.',
            'patient_photo.max' => 'The photo may not be larger than 2MB.',
        ];
    }

    protected function passedValidation(): void
    {
        if (!$this->filled('age') && $this->filled('date_of_birth')) {
            $this->merge([
                'age' => null,
            ]);
        }
    }

    protected function failedValidation(Validator $validator)
    {
        session()->flash('swal', [
            'icon' => 'error',
            'title' => 'Validation Error',
            'text' => $validator->errors()->first(),
            'showConfirmButton' => true,
        ]);

        parent::failedValidation($validator);
    }
}
