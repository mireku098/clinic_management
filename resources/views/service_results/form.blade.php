@extends('layouts.app')

@section('title', 'Service Result - ' . $service->service_name)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="fas fa-flask text-info me-2"></i>
            Service Result
        </h4>
        <a href="{{ route('visits.show', $visit->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Visit Details
        </a>
    </div>

    <!-- Context Panel (Read-Only) -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-info-circle text-primary me-2"></i>
                Context Information
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Patient Information</h6>
                    <div class="row">
                        <div class="col-sm-4">
                            <strong>Name:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ $patient->first_name }} {{ $patient->last_name }}
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-4">
                            <strong>Code:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ $patient->patient_code }}
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-4">
                            <strong>Age:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ $patient->age }} years
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Visit Information</h6>
                    <div class="row">
                        <div class="col-sm-4">
                            <strong>Date:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ is_object($visit->visit_date) ? $visit->visit_date->format('M d, Y') : \Carbon\Carbon::parse($visit->visit_date)->format('M d, Y') }}
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-4">
                            <strong>Type:</strong>
                        </div>
                        <div class="col-sm-8">
                            <span class="badge bg-{{ $visit->visit_type == 'appointment' ? 'primary' : 'warning' }}">
                                {{ ucfirst($visit->visit_type) }}
                            </span>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-4">
                            <strong>Purpose:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ $visit->purpose }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Service Information</h6>
                    <div class="row">
                        <div class="col-sm-4">
                            <strong>Service:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ $service->service_name }}
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-4">
                            <strong>Category:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ ucfirst($service->category) }}
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-4">
                            <strong>Result Type:</strong>
                        </div>
                        <div class="col-sm-8">
                            <span class="badge bg-info">{{ ucfirst($service->result_type) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    @if($package)
                    <h6 class="text-muted mb-3">Package Information</h6>
                    <div class="row">
                        <div class="col-sm-4">
                            <strong>Package:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ $package->package_name }}
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-4">
                            <strong>Description:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ $package->description }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Dynamic Result Entry Form -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-edit text-primary me-2"></i>
                @if($existingResult)
                    Edit Service Result
                @else
                    Add Service Result
                @endif
            </h5>
        </div>
        <div class="card-body">
            <form id="serviceResultForm" method="POST" action="{{ route('service-results.save') }}" enctype="multipart/form-data">
                @csrf
                
                <!-- Hidden Fields -->
                <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                <input type="hidden" name="visit_id" value="{{ $visit->id }}">
                <input type="hidden" name="service_id" value="{{ $service->id }}">
                @if($existingResult)
                <input type="hidden" name="result_id" value="{{ $existingResult->id }}">
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" 
                                    @if($existingResult && $existingResult->status === 'approved') disabled @endif>
                                <option value="draft" @if(($existingResult && $existingResult->status === 'draft') || !$existingResult) selected @endif>Draft</option>
                                <option value="pending_approval" @if($existingResult && $existingResult->status === 'pending_approval') selected @endif>Pending Approval</option>
                                <option value="approved" @if($existingResult && $existingResult->status === 'approved') selected @endif>Approved</option>
                                <option value="rejected" @if($existingResult && $existingResult->status === 'rejected') selected @endif>Rejected</option>
                            </select>
                            @if($existingResult && $existingResult->status === 'approved')
                            <small class="text-muted">Approved results cannot be edited</small>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="recorded_at" class="form-label">Recorded At</label>
                            <input type="datetime-local" class="form-control" id="recorded_at" name="recorded_at" 
                                   value="{{ $existingResult ? (is_object($existingResult->recorded_at) ? $existingResult->recorded_at->format('Y-m-d\TH:i') : \Carbon\Carbon::parse($existingResult->recorded_at)->format('Y-m-d\TH:i')) : now()->format('Y-m-d\TH:i') }}"
                                   @if($existingResult && $existingResult->status === 'approved') readonly @endif>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Result Field Based on service.result_type -->
                <div class="mb-3">
                    <label class="form-label">Result Value</label>
                    
                    @if($service->result_type === 'numeric')
                        <input type="number" class="form-control" id="result_numeric" name="result_numeric" 
                               step="any" placeholder="Enter numeric result..."
                               value="{{ $existingResult->result_numeric ?? '' }}"
                               @if($existingResult && $existingResult->status === 'approved') readonly @endif>
                    
                    @elseif($service->result_type === 'text')
                        <textarea class="form-control" id="result_text" name="result_text" rows="6" 
                                  placeholder="Enter text result..." 
                                  @if($existingResult && $existingResult->status === 'approved') readonly @endif>{{ $existingResult->result_text ?? '' }}</textarea>
                    
                    @elseif($service->result_type === 'file')
                        @if($existingResult && $existingResult->result_file_path)
                            <div class="alert alert-info d-flex align-items-center">
                                <i class="fas fa-file me-3"></i>
                                <div class="flex-grow-1">
                                    <strong>Current File:</strong> {{ $existingResult->result_file_name }}<br>
                                    <small class="text-muted">Uploaded: {{ $existingResult->created_at->format('M d, Y H:i') }}</small>
                                </div>
                                <a href="/storage/{{ $existingResult->result_file_path }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-download me-1"></i>Download
                                </a>
                            </div>
                        @endif
                        @if(!$existingResult || $existingResult->status !== 'approved')
                        <input type="file" class="form-control" id="result_file" name="result_file" 
                               accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        <small class="form-text text-muted">Upload PDF, Image, or Document files (Max: 10MB)</small>
                        @endif
                    @endif
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="4" 
                              placeholder="Additional notes about this result..."
                              @if($existingResult && $existingResult->status === 'approved') readonly @endif>{{ $existingResult->notes ?? '' }}</textarea>
                </div>

                <!-- Existing Result Metadata (Read-Only) -->
                @if($existingResult)
                <div class="row mb-3">
                    <div class="col-md-6">
                        <small class="text-muted">
                            <strong>Created:</strong> {{ is_object($existingResult->created_at) ? $existingResult->created_at->format('M d, Y H:i') : \Carbon\Carbon::parse($existingResult->created_at)->format('M d, Y H:i') }}<br>
                            <strong>Updated:</strong> {{ is_object($existingResult->updated_at) ? $existingResult->updated_at->format('M d, Y H:i') : \Carbon\Carbon::parse($existingResult->updated_at)->format('M d, Y H:i') }}
                        </small>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">
                            <strong>Recorded By:</strong> {{ $existingResult->recorder ? $existingResult->recorder->name : 'System' }}<br>
                            @if($existingResult->approved_by)
                            <strong>Approved By:</strong> {{ $existingResult->approver ? $existingResult->approver->name : 'System' }}
                            @endif
                        </small>
                    </div>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="d-flex justify-content-between">
                    <div>
                        <a href="{{ route('service-results.result-page', ['patient' => $patient->id, 'visit' => $visit->id, 'service' => $service->id]) }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                        @if($existingResult && $existingResult->status !== 'approved' && auth()->user() && auth()->user()->role === 'admin')
                        <button type="button" class="btn btn-success ms-2" onclick="approveResult()">
                            <i class="fas fa-check me-1"></i>Approve
                        </button>
                        @endif
                    </div>
                    <div>
                        @if(!$existingResult || $existingResult->status !== 'approved')
                        <button type="submit" class="btn btn-primary" id="saveBtn">
                            <i class="fas fa-save me-1"></i>
                            @if($existingResult) Update Result @else Save Result @endif
                        </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('serviceResultForm');
    const saveBtn = document.getElementById('saveBtn');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Client-side validation
        const status = document.getElementById('status').value;
        if (status === 'approved') {
            Swal.fire({
                icon: 'error',
                title: 'Cannot Edit Approved Result',
                text: 'Approved results cannot be modified.',
                confirmButtonColor: '#3085d6'
            });
            return;
        }
        
        // Validate result field based on type
        const resultType = '{{ $service->result_type }}';
        let isValid = true;
        let errorMessage = '';
        
        if (resultType === 'numeric') {
            const numericValue = document.getElementById('result_numeric').value;
            if (!numericValue || isNaN(numericValue)) {
                isValid = false;
                errorMessage = 'Please enter a valid numeric value';
            }
        } else if (resultType === 'text') {
            const textValue = document.getElementById('result_text').value;
            if (!textValue || textValue.trim().length === 0) {
                isValid = false;
                errorMessage = 'Please enter a text result';
            }
        } else if (resultType === 'file' && !{{ $existingResult ? 'true' : 'false' }}) {
            const fileInput = document.getElementById('result_file');
            if (!fileInput.files || fileInput.files.length === 0) {
                isValid = false;
                errorMessage = 'Please select a file to upload';
            }
        }
        
        if (!isValid) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: errorMessage,
                confirmButtonColor: '#3085d6'
            });
            return;
        }
        
        // Show loading state
        const originalText = saveBtn.innerHTML;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saving...';
        saveBtn.disabled = true;
        
        // Submit form via AJAX
        const formData = new FormData(form);
        
        fetch('{{ route('service-results.save') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = '{{ route('service-results.result-page', ['patient' => $patient->id, 'visit' => $visit->id, 'service' => $service->id]) }}';
                });
            } else {
                if (data.errors) {
                    // Handle validation errors
                    let errorMessage = 'Please fix the following errors:\n';
                    for (let field in data.errors) {
                        errorMessage += '\n• ' + data.errors[field][0];
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: errorMessage,
                        confirmButtonColor: '#3085d6'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message || 'An error occurred while saving the result.',
                        confirmButtonColor: '#3085d6'
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'An unexpected error occurred. Please try again.',
                confirmButtonColor: '#3085d6'
            });
        })
        .finally(() => {
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;
        });
    });
    
    // Approve function (admin only)
    window.approveResult = function() {
        Swal.fire({
            title: 'Approve Result?',
            text: 'This will mark the result as approved and it cannot be edited afterward.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Approve'
        }).then((result) => {
            if (result.isConfirmed) {
                // Set status to approved and submit
                document.getElementById('status').value = 'approved';
                form.dispatchEvent(new Event('submit'));
            }
        });
    };
});
</script>
@endsection
