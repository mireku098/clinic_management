@extends('layouts.app')

@section('title', 'Add Service Result - ' . ($patient->first_name ?? 'New') . ' ' . ($patient->last_name ?? 'Result'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">Add Service Result</h1>
            <p class="text-muted mb-0">Record test results and documentation</p>
            @if($patient)
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li><a href="{{ route('patients') }}">Patients</a></li>
                        <li><a href="{{ route('patients.show', $patient->id) }}">{{ $patient->first_name }} {{ $patient->last_name }}</a></li>
                        <li class="active">Add Result</li>
                    </ol>
                </nav>
            @endif
        </div>
        <div>
            @if($patient)
                <a href="{{ route('patients.service-results', $patient->id) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Results
                </a>
            @else
                <a href="{{ route('service-results.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Results
                </a>
            @endif
        </div>
    </div>

    <!-- Result Form -->
    <div class="row">
        <div class="col-lg-8">
            <form id="add_result_form" method="POST" action="{{ $patient ? route('patients.service-results.save', $patient->id) : route('service-results.save') }}" enctype="multipart/form-data">
                @csrf
                <!-- Patient & Service Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-user-injured me-2"></i>Patient & Service Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @if(request('patient_id'))
                                <!-- Pre-filled patient info -->
                                <div class="col-md-6">
                                    <label class="form-label">Patient</label>
                                    <div class="form-control">
                                        <i class="fas fa-user me-2"></i>
                                        {{ $patients->where('id', request('patient_id'))->first()->first_name }} {{ $patients->where('id', request('patient_id'))->first()->last_name }} ({{ $patients->where('id', request('patient_id'))->first()->patient_code }})
                                    </div>
                                    <input type="hidden" name="patient_id" value="{{ request('patient_id') }}">
                                </div>
                            @else
                                <!-- Patient selection dropdown -->
                                <div class="col-md-6">
                                    <label for="patient_id" class="form-label">Patient *</label>
                                    <select class="form-select" id="patient_id" name="patient_id" required>
                                        <option value="">Select Patient</option>
                                        @foreach($patients as $patient)
                                            <option value="{{ $patient->id }}" {{ request('patient_id') == $patient->id ? 'selected' : '' }}>
                                                {{ $patient->first_name }} {{ $patient->last_name }} ({{ $patient->patient_code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            @if(request('service_id'))
                                <!-- Pre-filled service info -->
                                @php 
                                    $serviceId = request('service_id');
                                    $selectedService = $services->where('id', $serviceId)->first();
                                    $availableServiceIds = $services->pluck('id')->toArray();
                                @endphp
                                @if($selectedService && !is_null($selectedService))
                                    <div class="col-md-6">
                                        <label class="form-label">Service</label>
                                        <div class="form-control">
                                            <i class="fas fa-flask me-2"></i>
                                            {{ $selectedService->service_name ?? 'Unknown' }} - {{ ucfirst($selectedService->category ?? 'unknown') }}
                                        </div>
                                        <input type="hidden" name="service_id" value="{{ request('service_id') }}">
                                    </div>
                                @else
                                    <div class="col-md-12">
                                        <div class="alert alert-danger">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            Service with ID {{ request('service_id') }} not found.
                                            <br>
                                            <small>Looking for service ID: {{ $serviceId }}</small>
                                            <br>
                                            <small>Available service IDs: {{ implode(', ', $availableServiceIds) }}</small>
                                            <br>
                                            <small>Total services loaded: {{ $services->count() }}</small>
                                            <br>
                                            <small>Selected service is null: {{ is_null($selectedService) ? 'YES' : 'NO' }}</small>
                                        </div>
                                    </div>
                                @endif
                            @elseif(request('package_id'))
                                <!-- Pre-filled package info -->
                                @php $selectedPackage = \App\Models\Package::find(request('package_id')); @endphp
                                @if($selectedPackage)
                                    <div class="col-md-6">
                                        <label class="form-label">Package</label>
                                        <div class="form-control">
                                            <i class="fas fa-box me-2"></i>
                                            {{ $selectedPackage->package_name }}
                                        </div>
                                        <input type="hidden" name="package_id" value="{{ request('package_id') }}">
                                    </div>
                                @else
                                    <div class="col-md-12">
                                        <div class="alert alert-danger">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            Package with ID {{ request('package_id') }} not found.
                                        </div>
                                    </div>
                                @endif
                            @else
                                <!-- Service/Package selection dropdown -->
                                <div class="col-md-6">
                                    <label for="service_or_package" class="form-label">Service/Package *</label>
                                    <select class="form-select" id="service_or_package" name="service_or_package" required>
                                        <option value="">Select Type</option>
                                        <option value="service">Individual Service</option>
                                        <option value="package">Package</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <div id="service_selection_section">
                                        <label for="service_id" class="form-label">Service *</label>
                                        <select class="form-select" id="service_id" name="service_id">
                                            <option value="">Select Service</option>
                                            @foreach($services as $service)
                                                <option value="{{ $service->id }}" data-result-type="{{ $service->result_type }}">
                                                    {{ $service->service_name ?? 'Unknown Service' }} - {{ ucfirst($service->category ?? 'unknown') }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div id="package_selection_section" style="display: none;">
                                        <label for="package_id" class="form-label">Package *</label>
                                        <select class="form-select" id="package_id" name="package_id">
                                            <option value="">Select Package</option>
                                            @foreach(App\Models\Package::where('status', 'active')->orderBy('package_name')->get() as $package)
                                                <option value="{{ $package->id }}">
                                                    {{ $package->package_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                            @if(request('visit_id'))
                                <!-- Pre-filled visit info -->
                                @php 
                                    $debugVisitId = request('visit_id');
                                    $selectedVisit = $visits->where('id', $debugVisitId)->first();
                                    // Debug info
                                    $availableVisitIds = $visits->pluck('id')->toArray();
                                @endphp
                                @if($selectedVisit)
                                    <div class="col-md-6">
                                        <label class="form-label">Visit</label>
                                        <div class="form-control">
                                            <i class="fas fa-calendar me-2"></i>
                                            Visit #{{ $selectedVisit->id }} - {{ $selectedVisit->visit_date ? $selectedVisit->visit_date->format('M d, Y') : 'No date' }} @ {{ $selectedVisit->visit_time ? $selectedVisit->visit_time->format('H:i') : 'No time' }}
                                        </div>
                                        <input type="hidden" name="visit_id" value="{{ request('visit_id') }}">
                                    </div>
                                @else
                                    <div class="col-md-12">
                                        <div class="alert alert-danger">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            Visit with ID {{ request('visit_id') }} not found.
                                            <br>
                                            <small>Looking for visit ID: {{ $debugVisitId }}</small>
                                            <br>
                                            <small>Available visit IDs: {{ implode(', ', $availableVisitIds) }}</small>
                                            <br>
                                            <small>Total visits loaded: {{ $visits->count() }}</small>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <!-- Visit selection dropdown -->
                                <div class="col-md-6">
                                    <label for="visit_id" class="form-label">Visit (Optional)</label>
                                    <select class="form-select" id="visit_id" name="visit_id">
                                        <option value="">Select Visit</option>
                                        @foreach($visits as $visit)
                                            <option value="{{ $visit->id }}" {{ request('visit_id') == $visit->id ? 'selected' : '' }}>
                                                Visit #{{ $visit->id }} - {{ $visit->created_at ? $visit->created_at->format('M d, Y H:i') : 'No date' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Select a visit if this result is related to a specific visit</small>
                                </div>
                            @endif
                            <div class="col-md-6">
                                <label for="result_type" class="form-label">Result Type *</label>
                                <select class="form-select" id="result_type" name="result_type" required>
                                    <option value="">Select Result Type</option>
                                    <option value="text">Text</option>
                                    <option value="numeric">Numeric</option>
                                    <option value="file">File Upload</option>
                                </select>
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
                        <div id="text_result_section" class="result-section" style="display: none;">
                            <label for="result_text" class="form-label">Text Result *</label>
                            <textarea class="form-control" id="result_text" name="result_text" rows="6" placeholder="Enter the text result..."></textarea>
                            <small class="text-muted">Enter the complete text result or findings</small>
                        </div>

                        <!-- Numeric Result -->
                        <div id="numeric_result_section" class="result-section" style="display: none;">
                            <label for="result_numeric" class="form-label">Numeric Result *</label>
                            <input type="number" class="form-control" id="result_numeric" name="result_numeric" step="0.01" placeholder="Enter numeric value...">
                            <small class="text-muted">Enter the numeric measurement or value</small>
                        </div>

                        <!-- File Result -->
                        <div id="file_result_section" class="result-section" style="display: none;">
                            <label for="result_file" class="form-label">Upload File *</label>
                            <input type="file" class="form-control" id="result_file" name="result_file" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Upload PDF, JPG, JPEG, or PNG files (Max: 5MB)</small>
                            <div id="file_preview" class="mt-3" style="display: none;">
                                <div class="alert alert-info">
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
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="draft">Draft</option>
                                    <option value="pending_approval">Pending Approval</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                                <small class="text-muted">Select the status of this result</small>
                            </div>
                            <div class="col-md-6">
                                <label for="recorded_at" class="form-label">Recorded Date *</label>
                                <input type="datetime-local" class="form-control" id="recorded_at" name="recorded_at" required>
                                <small class="text-muted">When was this result recorded?</small>
                            </div>
                            <div class="col-12">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="4" placeholder="Add any additional notes or observations..."></textarea>
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
                                <a href="{{ route('service-results.index') }}" class="btn btn-outline-info">
                                    <i class="fas fa-times me-2"></i>
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    Save Result
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Help -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-question-circle me-2"></i>Help</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled small">
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Select a patient and service first
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Result type determines how you enter the data
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Text: Enter detailed findings or descriptions
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Numeric: Enter measurements or values
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            File: Upload test reports or images
                        </li>
                        <li>
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Results are saved as draft and can be submitted for approval
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Recent Results -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-history me-2"></i>Recent Results</h5>
                </div>
                <div class="card-body">
                    @php
                        $recentResults = App\Models\ServiceResult::with(['service', 'patient'])
                            ->latest()
                            ->take(5)
                            ->get();
                    @endphp
                    @if($recentResults->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentResults as $recent)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $recent->service->service_name ?? 'Unknown Service' }}</h6>
                                            <small class="text-muted">{{ $recent->patient->first_name }} {{ $recent->patient->last_name }}</small>
                                        </div>
                                        <span class="badge bg-{{ $recent->status === 'approved' ? 'success' : ($recent->status === 'pending_approval' ? 'warning' : 'secondary') }} small">
                                            {{ ucfirst(str_replace('_', ' ', $recent->status)) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No recent results</p>
                    @endif
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
document.getElementById("add_result_form").addEventListener("submit", function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
    
    fetch('{{ $patient ? route("patients.service-results.save", $patient->id) : route("service-results.save") }}', {
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
                text: 'Service result created successfully!',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = '{{ $patient ? route("patients.service-results", $patient->id) : route("service-results.index") }}';
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

// Set default values for status and recorded_at
document.addEventListener('DOMContentLoaded', function() {
    // Set default status
    const statusSelect = document.getElementById('status');
    if (statusSelect && !statusSelect.value) {
        statusSelect.value = 'draft';
    }
    
    // Set default recorded_at to current datetime
    const recordedAtInput = document.getElementById('recorded_at');
    if (recordedAtInput && !recordedAtInput.value) {
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        recordedAtInput.value = now.toISOString().slice(0, 16);
    }
    
    // Handle pre-selected service from URL
    const serviceSelect = document.getElementById('service_id');
    if (serviceSelect && serviceSelect.value) {
        // Trigger change event to set result type
        serviceSelect.dispatchEvent(new Event('change'));
    }
    
    // Handle pre-selected result type from URL or service default
    const resultTypeSelect = document.getElementById('result_type');
    if (resultTypeSelect && resultTypeSelect.value) {
        // Trigger change event to show appropriate section
        resultTypeSelect.dispatchEvent(new Event('change'));
    }
});

// Service/Package selection - auto-set result type
document.getElementById('service_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const resultType = selectedOption.getAttribute('data-result-type');
    const isService = selectedOption.getAttribute('data-is-service') === 'true';
    
    if (resultType) {
        document.getElementById('result_type').value = resultType;
        document.getElementById('result_type').dispatchEvent(new Event('change'));
    }
    
    // Update visual indicator based on selection
    const selectElement = this;
    if (isService) {
        selectElement.style.borderColor = '#007bff'; // Blue for services
    } else {
        selectElement.style.borderColor = '#28a745'; // Green for packages
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
                if (data.success && data.visits) {
                    data.visits.forEach(visit => {
                        const visitDate = visit.visit_date ? new Date(visit.visit_date).toLocaleDateString() : 'No date';
                        const visitTime = visit.visit_time ? new Date(visit.visit_time).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : 'No time';
                        visitSelect.innerHTML += `<option value="${visit.id}">Visit #${visit.id} - ${visitDate} @ ${visitTime}</option>`;
                    });
                }
            })
            .catch(error => {
                console.error('Error loading visits:', error);
            });
    } else {
        visitSelect.innerHTML = '<option value="">Select Visit</option>';
    }
});

// Form submission debugging
$('#add_result_form').on('submit', function(e) {
    console.log('Form submission data:');
    const formData = new FormData(this);
    for (let [key, value] of formData.entries()) {
        console.log(key + ':', value);
    }
    
    // Show debug alert for package submission
    if (formData.get('package_id')) {
        console.log('Package ID being submitted:', formData.get('package_id'));
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
</script>
@endsection
