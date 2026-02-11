@extends('layouts.app')

@section('title', 'Edit Service Result')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1>Edit Service Result</h1>
            <p class="text-muted">Update test result information</p>
        </div>
        <div>
            <a href="{{ $result->patient ? route('patients.service-results', $result->patient->id) : route('service-results.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Results
            </a>
        </div>
    </div>

    <!-- Result Form -->
    <div class="row">
        <div class="col-lg-8">
            <form id="edit_result_form" method="POST" action="{{ route('service-results.update', $result->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <!-- Patient & Service Selection -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-user-injured me-2"></i>Patient & Service Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="patient_id" class="form-label">Patient *</label>
                                <select class="form-select" id="patient_id" name="patient_id" required>
                                    <option value="">Select Patient</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}" {{ $result->patient_id == $patient->id ? 'selected' : '' }}>
                                            {{ $patient->first_name }} {{ $patient->last_name }} ({{ $patient->patient_code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                @if($result->package_id)
                                    <label for="package_id" class="form-label">Package *</label>
                                    <div class="form-control">
                                        <i class="fas fa-box me-2"></i>
                                        {{ $result->package->package_name ?? 'Unknown Package' }} - Package
                                    </div>
                                    <input type="hidden" name="package_id" value="{{ $result->package_id }}">
                                    <small class="text-muted">This result is linked to the above package</small>
                                @else
                                    <label for="service_id" class="form-label">Service *</label>
                                    <select class="form-select" id="service_id" name="service_id" required>
                                        <option value="">Select Service</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}" 
                                                    data-result-type="{{ $service->result_type }}"
                                                    {{ $result->service_id == $service->id ? 'selected' : '' }}>
                                                {{ $service->service_name }} - {{ ucfirst($service->category) }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div class="col-md-6">
                                @if($result->visit_id)
                                    <label for="visit_id" class="form-label">Visit</label>
                                    <div class="form-control">
                                        <i class="fas fa-calendar me-2"></i>
                                        Visit #{{ $result->visit->id }} - {{ $result->visit->visit_date ? \Carbon\Carbon::parse($result->visit->visit_date)->format('M d, Y') : 'No date' }} @ {{ $result->visit->visit_time ? \Carbon\Carbon::parse($result->visit->visit_time)->format('H:i') : 'No time' }}
                                    </div>
                                    <input type="hidden" name="visit_id" value="{{ $result->visit_id }}">
                                    <small class="text-muted">This result is linked to the above visit</small>
                                @else
                                    <label for="visit_id" class="form-label">Visit (Optional)</label>
                                    <select class="form-select" id="visit_id" name="visit_id">
                                        <option value="">Select Visit</option>
                                        @foreach($visits as $visit)
                                            <option value="{{ $visit->id }}" {{ $result->visit_id == $visit->id ? 'selected' : '' }}>
                                                Visit #{{ $visit->id }} - {{ $visit->visit_date ? \Carbon\Carbon::parse($visit->visit_date)->format('M d, Y') : 'No date' }} @ {{ $visit->visit_time ? \Carbon\Carbon::parse($visit->visit_time)->format('H:i') : 'No time' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Select a visit if this result is related to a specific visit</small>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label for="result_type" class="form-label">Result Type *</label>
                                <select class="form-select" id="result_type" name="result_type" required>
                                    <option value="">Select Result Type</option>
                                    <option value="text" {{ $result->result_type === 'text' ? 'selected' : '' }}>Text</option>
                                    <option value="numeric" {{ $result->result_type === 'numeric' ? 'selected' : '' }}>Numeric</option>
                                    <option value="file" {{ $result->result_type === 'file' ? 'selected' : '' }}>File Upload</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="draft" {{ $result->status === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="pending_approval" {{ $result->status === 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                                    <option value="approved" {{ $result->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ $result->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="recorded_at" class="form-label">Recorded Date *</label>
                                <input type="datetime-local" class="form-control" id="recorded_at" name="recorded_at" required 
                                       value="{{ $result->recorded_at ? $result->recorded_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i') }}">
                                <small class="text-muted">When this result was recorded</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Result Entry -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-clipboard-list me-2"></i>Result Entry</h5>
                    </div>
                    <div class="card-body">
                        <!-- Text Result -->
                        <div id="text_result_section" class="result-section" {{ $result->result_type === 'text' ? '' : 'style="display: none;"' }}>
                            <label for="result_text" class="form-label">Text Result *</label>
                            <textarea class="form-control" id="result_text" name="result_text" rows="6" placeholder="Enter the text result...">{{ $result->result_text ?? '' }}</textarea>
                            <small class="text-muted">Enter the complete text result or findings</small>
                        </div>

                        <!-- Numeric Result -->
                        <div id="numeric_result_section" class="result-section" {{ $result->result_type === 'numeric' ? '' : 'style="display: none;"' }}>
                            <label for="result_numeric" class="form-label">Numeric Result *</label>
                            <input type="number" class="form-control" id="result_numeric" name="result_numeric" step="0.01" placeholder="Enter numeric value..." value="{{ $result->result_numeric ?? '' }}">
                            <small class="text-muted">Enter the numeric measurement or value</small>
                        </div>

                        <!-- File Result -->
                        <div id="file_result_section" class="result-section" {{ $result->result_type === 'file' ? '' : 'style="display: none;"' }}>
                            <label for="result_file" class="form-label">Upload File</label>
                            <input type="file" class="form-control" id="result_file" name="result_file" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Upload new file to replace existing (Max: 5MB)</small>
                            
                            @if ($result->result_file_path)
                                <div class="mt-3">
                                    <div class="alert alert-info">
                                        <i class="fas fa-file me-2"></i>
                                        Current file: <a href="{{ asset('storage/' . $result->result_file_path) }}" target="_blank">{{ $result->result_file_name }}</a>
                                    </div>
                                </div>
                            @endif
                            
                            <div id="file_preview" class="mt-3" style="display: none;">
                                <div class="alert alert-success">
                                    <i class="fas fa-file me-2"></i>
                                    <span id="file_name"></span>
                                    <button type="button" class="btn btn-sm btn-outline-danger float-end" onclick="clearFile()">Clear</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-sticky-note me-2"></i>Additional Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="4" placeholder="Add any additional notes or observations...">{{ $result->notes ?? '' }}</textarea>
                                <small class="text-muted">Optional notes about the result or testing conditions</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="form-actions">
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>
                                Reset Form
                            </button>
                            <div class="d-flex gap-2">
                                <a href="{{ $result->patient ? route('patients.service-results', $result->patient->id) : route('service-results.index') }}" class="btn btn-outline-info">
                                    <i class="fas fa-times me-2"></i>
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    Update Result
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Result Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle me-2"></i>Result Information</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Status</span>
                        <span class="badge bg-{{ $result->status === 'approved' ? 'success' : ($result->status === 'pending_approval' ? 'warning' : ($result->status === 'rejected' ? 'danger' : 'secondary')) }}">
                            {{ ucfirst(str_replace('_', ' ', $result->status)) }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Created</span>
                        <span class="text-muted">{{ $result->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Last Updated</span>
                        <span class="text-muted">{{ $result->updated_at->format('M d, Y H:i') }}</span>
                    </div>
                    @if ($result->approved_at)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Approved</span>
                            <span class="text-muted">{{ $result->approved_at->format('M d, Y H:i') }}</span>
                        </div>
                    @endif
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Recorded By</span>
                        <span class="text-muted">{{ $result->recorder->name ?? 'Unknown' }}</span>
                    </div>
                </div>
            </div>

            <!-- Help -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-question-circle me-2"></i>Help</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled small">
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Only draft and rejected results can be edited
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Changing result type will clear existing data
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            File upload replaces existing file
                        </li>
                        <li>
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Submit for approval after editing
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alert Container -->
<div id="alert-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1050"></div>
@endsection

@section('js')
<script>
document.getElementById("edit_result_form").addEventListener("submit", function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
    
    fetch('{{ route("service-results.update", $result->id) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Service result updated successfully!',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = '{{ $result->patient ? route("patients.service-results", $result->patient->id) : route("service-results.index") }}';
            });
        } else {
            let errorMessage = data.message || 'Something went wrong. Please try again.';
            
            if (data.errors) {
                const errorMessages = Object.values(data.errors).flat();
                errorMessage = errorMessages.join('\n');
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: errorMessage,
                confirmButtonColor: '#3085d6'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Network error. Please check your connection and try again.',
            confirmButtonColor: '#3085d6'
        });
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

// Result type switching
document.getElementById('result_type').addEventListener('change', function() {
    const resultType = this.value;
    
    // Hide all result sections
    document.querySelectorAll('.result-section').forEach(section => {
        section.style.display = 'none';
    });
    
    // Show selected result section
    if (resultType) {
        document.getElementById(resultType + '_result_section').style.display = 'block';
    }
});

// Service selection - auto-set result type
document.getElementById('service_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const resultType = selectedOption.getAttribute('data-result-type');
    
    if (resultType) {
        document.getElementById('result_type').value = resultType;
        document.getElementById('result_type').dispatchEvent(new Event('change'));
    }
});

// Patient selection - load visits
document.getElementById('patient_id').addEventListener('change', function() {
    const patientId = this.value;
    const visitSelect = document.getElementById('visit_id');
    
    if (patientId) {
        fetch(`/api/patients/${patientId}/visits`)
            .then(response => response.json())
            .then(data => {
                visitSelect.innerHTML = '<option value="">Select Visit</option>';
                data.visits.forEach(visit => {
                    visitSelect.innerHTML += `<option value="${visit.id}">Visit #${visit.id} - ${new Date(visit.created_at).toLocaleString()}</option>`;
                });
            })
            .catch(error => {
                console.error('Error loading visits:', error);
            });
    } else {
        visitSelect.innerHTML = '<option value="">Select Visit</option>';
    }
});

// File preview
document.getElementById('result_file').addEventListener('change', function() {
    const file = this.files[0];
    const preview = document.getElementById('file_preview');
    const fileName = document.getElementById('file_name');
    
    if (file) {
        fileName.textContent = file.name;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
});

function clearFile() {
    document.getElementById('result_file').value = '';
    document.getElementById('file_preview').style.display = 'none';
}

// Initialize result type on page load
document.addEventListener('DOMContentLoaded', function() {
    const resultType = document.getElementById('result_type').value;
    showResultSection(resultType);
});

// Result type change handler
document.getElementById('result_type').addEventListener('change', function() {
    showResultSection(this.value);
});

function showResultSection(resultType) {
    // Hide all result sections
    document.querySelectorAll('.result-section').forEach(section => {
        section.style.display = 'none';
    });
    
    // Show the selected result section
    if (resultType) {
        const targetSection = document.getElementById(resultType + '_result_section');
        if (targetSection) {
            targetSection.style.display = 'block';
        }
    }
}
</script>
@endsection
