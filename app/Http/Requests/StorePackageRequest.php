<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePackageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // You can add authorization logic here if needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'durationValue' => 'required|integer|min:1',
            'durationType' => 'required|in:weeks,months',
            'totalWeeks' => 'required|integer|min:1',
            'category' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
            'services' => 'required|array|min:1',
            'services.*.serviceId' => 'required|integer|exists:services,id',
            'services.*.serviceName' => 'required|string',
            'services.*.costPerSession' => 'required|numeric|min:0',
            'services.*.frequencyType' => 'required|in:once,per_week,per_month',
            'services.*.frequencyValue' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    // Get the service index from the attribute name
                    preg_match('/services\.(\d+)\./', $attribute, $matches);
                    $index = $matches[1] ?? null;
                    
                    if ($index !== null) {
                        $frequencyType = $this->input("services.{$index}.frequencyType");
                        
                        // Additional validation for frequency logic
                        if ($frequencyType === 'once' && $value > 1) {
                            $fail('For "once" frequency, value should be 1');
                        }
                        
                        if ($frequencyType === 'per_week' && $value > 7) {
                            $fail('For "per_week" frequency, value should not exceed 7 (days in a week)');
                        }
                        
                        if ($frequencyType === 'per_month' && $value > 31) {
                            $fail('For "per_month" frequency, value should not exceed 31 (days in a month)');
                        }
                    }
                }
            ],
            'services.*.totalSessions' => 'required|integer|min:1',
            'services.*.totalCost' => 'required|numeric|min:0'
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Package name is required',
            'name.max' => 'Package name cannot exceed 255 characters',
            'description.max' => 'Description cannot exceed 1000 characters',
            'durationValue.required' => 'Duration value is required',
            'durationValue.min' => 'Duration must be at least 1',
            'durationType.required' => 'Duration type is required',
            'durationType.in' => 'Duration type must be either weeks or months',
            'totalWeeks.required' => 'Total weeks calculation is required',
            'totalWeeks.min' => 'Total weeks must be at least 1',
            'category.max' => 'Category cannot exceed 100 characters',
            'status.required' => 'Status is required',
            'status.in' => 'Status must be either active or inactive',
            'services.required' => 'At least one service must be added',
            'services.min' => 'At least one service must be added',
            'services.array' => 'Services must be an array',
            'services.*.serviceId.required' => 'Service ID is required for all services',
            'services.*.serviceId.exists' => 'Selected service is invalid',
            'services.*.serviceName.required' => 'Service name is required',
            'services.*.costPerSession.required' => 'Cost per session is required',
            'services.*.costPerSession.min' => 'Cost per session cannot be negative',
            'services.*.frequencyType.required' => 'Frequency type is required',
            'services.*.frequencyType.in' => 'Frequency type must be once, per_week, or per_month',
            'services.*.frequencyValue.required' => 'Frequency value is required',
            'services.*.frequencyValue.min' => 'Frequency value must be at least 1',
            'services.*.totalSessions.required' => 'Total sessions is required',
            'services.*.totalSessions.min' => 'Total sessions must be at least 1',
            'services.*.totalCost.required' => 'Service total cost is required',
            'services.*.totalCost.min' => 'Service total cost cannot be negative'
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
