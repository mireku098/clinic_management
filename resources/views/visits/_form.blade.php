<!-- Visit Form Partial -->
<!-- DEBUG: Action URL = {{ $action ?? route('visits.store') }} -->
<!-- DEBUG: Is Edit Visit = {{ isset($visit) ? 'YES' : 'NO' }} -->
<!-- DEBUG: Visit ID = {{ isset($visit) ? $visit->id : 'NONE' }} -->
<!-- DEBUG: Packages Count = {{ isset($packages) ? $packages->count() : 'NOT_SET' }} -->
<!-- DEBUG: Services Count = {{ isset($services) ? $services->count() : 'NOT_SET' }} -->
<form id="visit_form" method="POST" action="{{ $action ?? route('visits.store') }}" class="needs-validation" novalidate>
    @csrf
    @if(isset($visit))
        @method('PUT')
    @endif
    
    <!-- DEBUG: Patient ID = {{ isset($visit) ? $visit->patient_id : 'NONE' }} -->
    
    <!-- Patient Information -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="fas fa-user-injured me-2"></i>Patient Information</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="patient_search" class="form-label">Search Patient</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input
                            type="text"
                            class="form-control"
                            id="patient_search"
                            placeholder="Search by name or code..."
                            @if(isset($visit)) readonly @endif
                        />
                    </div>
                    <div id="patient_results"></div>
                </div>
                <div class="col-md-6">
                    <label for="patient_name" class="form-label">Selected Patient</label>
                    <input type="text" class="form-control" id="patient_name" readonly 
                           value="{{ isset($visit) ? $visit->patient->first_name . ' ' . $visit->patient->last_name : (isset($patient) ? $patient->first_name . ' ' . $patient->last_name : '') }}" />
                    <input type="hidden" id="patient_id" name="patient_id" 
                           value="{{ isset($visit) ? $visit->patient_id : (isset($patient) ? $patient->id : old('patient_id')) }}" />
                </div>
            </div>
        </div>
    </div>

    <!-- Visit Details -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="fas fa-calendar-check me-2"></i>Visit Details</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="visit_date" class="form-label">Visit Date</label>
                    <input type="date" class="form-control date-picker" id="visit_date" name="visit_date" 
                           value="{{ isset($visit) ? ($visit->visit_date ? $visit->visit_date->format('Y-m-d') : old('visit_date')) : (old('visit_date') ?? date('Y-m-d')) }}" required />
                </div>
                <div class="col-md-4">
                    <label for="visit_time" class="form-label">Visit Time</label>
                    <input type="time" class="form-control" id="visit_time" name="visit_time" 
                           value="{{ isset($visit) ? ($visit->visit_time ? $visit->visit_time->format('H:i') : old('visit_time')) : (old('visit_time') ?? date('H:i')) }}" required />
                </div>
                <div class="col-md-4">
                    <label for="visit_type" class="form-label">Visit Type</label>
                    <select class="form-select" id="visit_type" name="visit_type" required>
                        <option value="">Select Type</option>
                        <option value="appointment" {{ (isset($visit) && $visit->visit_type == 'appointment') || old('visit_type') == 'appointment' ? 'selected' : '' }}>Appointment</option>
                        <option value="walk-in" {{ (isset($visit) && $visit->visit_type == 'walk-in') || old('visit_type') == 'walk-in' ? 'selected' : '' }}>Walk-in</option>
                    </select>
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label for="practitioner" class="form-label">Practitioner</label>
                    <select class="form-select" id="practitioner" name="practitioner" required>
                        <option value="">Select Practitioner</option>
                        <option value="dr-smith" {{ (isset($visit) && $visit->practitioner == 'dr-smith') || old('practitioner') == 'dr-smith' ? 'selected' : '' }}>Dr. Smith</option>
                        <option value="dr-johnson" {{ (isset($visit) && $visit->practitioner == 'dr-johnson') || old('practitioner') == 'dr-johnson' ? 'selected' : '' }}>Dr. Johnson</option>
                        <option value="dr-williams" {{ (isset($visit) && $visit->practitioner == 'dr-williams') || old('practitioner') == 'dr-williams' ? 'selected' : '' }}>Dr. Williams</option>
                        <option value="therapist-brown" {{ (isset($visit) && $visit->practitioner == 'therapist-brown') || old('practitioner') == 'therapist-brown' ? 'selected' : '' }}>Therapist Brown</option>
                        <option value="therapist-davis" {{ (isset($visit) && $visit->practitioner == 'therapist-davis') || old('practitioner') == 'therapist-davis' ? 'selected' : '' }}>Therapist Davis</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="department" class="form-label">Department</label>
                    <select class="form-select" id="department" name="department">
                        <option value="">Select Department</option>
                        <option value="general" {{ (isset($visit) && $visit->department == 'general') || old('department') == 'general' ? 'selected' : '' }}>General</option>
                        <option value="physiotherapy" {{ (isset($visit) && $visit->department == 'physiotherapy') || old('department') == 'physiotherapy' ? 'selected' : '' }}>Physiotherapy</option>
                        <option value="consultation" {{ (isset($visit) && $visit->department == 'consultation') || old('department') == 'consultation' ? 'selected' : '' }}>Consultation</option>
                        <option value="emergency" {{ (isset($visit) && $visit->department == 'emergency') || old('department') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Package & Service Selection -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="fas fa-shopping-cart me-2"></i>Package & Service Selection</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="package_selection" class="form-label">Select Package (Optional)</label>
                    <select class="form-select" id="package_selection" name="package_id">
                        <option value="">No Package - Individual Services</option>
                        @if(isset($packages))
                            @foreach($packages as $package)
                                <option value="{{ $package->id }}" 
                                        data-price="{{ $package->total_cost }}"
                                        data-name="{{ $package->package_name }}"
                                        @if(isset($visit) && $visit->package_id == $package->id) selected @endif>
                                    {{ $package->package_name }} - GH₵{{ number_format($package->total_cost, 2) }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <small class="text-muted">Select a package for bundled services</small>
                </div>
                <div class="col-md-6">
                    <label for="service_selection" class="form-label">Add Individual Services</label>
                    <div class="input-group">
                        <select class="form-select" id="service_selection">
                            <option value="">Select Service</option>
                            @if(isset($services))
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" data-price="{{ $service->price }}" data-name="{{ $service->service_name }}">
                                        {{ $service->service_name }} - GH₵{{ number_format($service->price, 2) }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <button type="button" class="btn btn-primary" onclick="addService()">
                            <i class="fas fa-plus"></i> Add
                        </button>
                    </div>
                    <small class="text-muted">Add individual services to this visit</small>
                </div>
            </div>
            
            <!-- Selected Services List -->
            <div class="row mt-3">
                <div class="col-12">
                    <label class="form-label">Selected Services</label>
                    <div id="selected_services" class="border rounded p-3 bg-light">
                        <div class="text-muted text-center" id="no_services_message">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>No services selected</p>
                        </div>
                        <div id="services_list" style="display: none;">
                            <!-- Services will be added here dynamically -->
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pricing Summary -->
            <div class="row mt-3">
                <div class="col-md-8">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Subtotal:</span>
                        <span id="subtotal">GH₵0.00</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Discount:</span>
                        <span id="discount">GH₵0.00</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Total:</span>
                        <span id="total_amount" class="fs-5 text-primary">GH₵0.00</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>Total cost will be added to patient's account</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vital Signs -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="fas fa-heartbeat me-2"></i>Vital Signs</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label for="blood_pressure" class="form-label">Blood Pressure</label>
                    <input type="text" class="form-control" id="blood_pressure" name="blood_pressure"
                        placeholder="120/80" value="{{ isset($visit) ? $visit->blood_pressure : old('blood_pressure') }}" 
                        pattern="\d{2,3}\/\d{2,3}" title="Please enter blood pressure in format 120/80" />
                    <small class="text-muted">Format: 120/80 (systolic/diastolic)</small>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="temperature" class="form-label">Temperature (°C)</label>
                        <input type="number" step="0.1" class="form-control" id="temperature" name="temperature"
                            placeholder="37.0" value="{{ isset($visit) ? $visit->temperature : old('temperature') }}" />
                        <!-- <small class="text-muted">No range restrictions</small> -->
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="weight" class="form-label">Weight (kg)</label>
                        <input type="number" step="0.1" class="form-control" id="weight" name="weight"
                            placeholder="70.0" value="{{ isset($visit) ? $visit->weight : old('weight') }}" />
                        <!-- <small class="text-muted">No range restrictions</small> -->
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="respiratory_rate" class="form-label">Respiratory Rate</label>
                        <input type="number" class="form-control" id="respiratory_rate" name="respiratory_rate"
                            placeholder="16" value="{{ isset($visit) ? $visit->respiratory_rate : old('respiratory_rate') }}" />
                        <!-- <small class="text-muted">No range restrictions</small> -->
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="oxygen_saturation" class="form-label">O₂ Saturation (%)</label>
                        <input type="number" class="form-control" id="oxygen_saturation" name="oxygen_saturation"
                            placeholder="98" value="{{ isset($visit) ? $visit->oxygen_saturation : old('oxygen_saturation') }}" />
                        <!-- <small class="text-muted">No range restrictions</small> -->
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="heart_rate" class="form-label">Heart Rate</label>
                        <input type="number" class="form-control" id="heart_rate" name="heart_rate"
                            placeholder="72" value="{{ isset($visit) ? $visit->heart_rate : old('heart_rate') }}" />
                        <!-- <small class="text-muted">No range restrictions</small> -->
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="pulse_rate" class="form-label">Pulse Rate</label>
                        <input type="number" class="form-control" id="pulse_rate" name="pulse_rate"
                            placeholder="72" value="{{ isset($visit) ? $visit->pulse_rate : old('pulse_rate') }}" />
                        <!-- <small class="text-muted">No range restrictions</small> -->
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="bmi" class="form-label">BMI</label>
                        <input type="number" step="0.1" class="form-control" id="bmi" name="bmi" readonly 
                               value="{{ isset($visit) ? $visit->bmi : old('bmi') }}" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Visit Notes -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="fas fa-notes-medical me-2"></i>Visit Notes</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12">
                    <label for="chief_complaint" class="form-label">Chief Complaint</label>
                    <textarea class="form-control" id="chief_complaint" name="chief_complaint" rows="2"
                              placeholder="Describe the main reason for visit...">{{ isset($visit) ? $visit->chief_complaint : old('chief_complaint') }}</textarea>
                </div>
                <div class="col-12">
                    <label for="reason_for_visit" class="form-label">Reason for Visit</label>
                    <textarea class="form-control" id="reason_for_visit" name="reason_for_visit" rows="3"
                              placeholder="Detailed reason for visit...">{{ isset($visit) ? $visit->reason_for_visit : old('reason_for_visit') }}</textarea>
                </div>
                <div class="col-12">
                    <label for="history_present_illness" class="form-label">History of Present Illness</label>
                    <textarea class="form-control" id="history_present_illness" name="history_present_illness" rows="4"
                              placeholder="Patient's medical history related to current illness...">{{ isset($visit) ? $visit->history_present_illness : old('history_present_illness') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label for="assessment" class="form-label">Assessment</label>
                    <textarea class="form-control" id="assessment" name="assessment" rows="4"
                              placeholder="Clinical assessment and diagnosis...">{{ isset($visit) ? $visit->assessment : old('assessment') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label for="treatment_plan" class="form-label">Treatment Plan</label>
                    <textarea class="form-control" id="treatment_plan" name="treatment_plan" rows="4"
                              placeholder="Planned treatment and medications...">{{ isset($visit) ? $visit->treatment_plan : old('treatment_plan') }}</textarea>
                </div>
                <div class="col-12">
                    <label for="notes" class="form-label">Additional Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"
                              placeholder="Any additional notes or observations...">{{ isset($visit) ? $visit->notes : old('notes') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden fields for services and packages -->
    <input type="hidden" id="selected_services_data" name="selected_services" value="">
    <input type="hidden" id="selected_package_data" name="selected_package" value="">
    <input type="hidden" id="total_amount_data" name="total_amount" value="">

    <!-- Form Actions -->
    <div class="card">
        <div class="card-body">
            <div class="form-actions">
                <button type="reset" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>
                    Clear Form
                </button>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1" onclick="console.log('DEBUG: Submit button clicked'); console.log('Form action:', document.getElementById('visit_form').action); console.log('Form method:', document.getElementById('visit_form').method);">
                        <i class="fas fa-save me-2"></i>
                        {{ isset($visit) ? 'Save Changes' : 'Record Visit' }}
                    </button>
                    @if(!isset($visit))
                    <button type="button" class="btn btn-outline-secondary" onclick="saveAsDraft()">
                        <i class="fas fa-save me-2"></i>
                        Save Draft
                    </button>
                    <button type="button" class="btn btn-outline-warning" onclick="clearDraft()">
                        <i class="fas fa-trash me-2"></i>
                        Clear Draft
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.getElementById("visit_form").addEventListener("submit", function(e) {
    e.preventDefault();
    
    // Append seconds to visit_time to match H:i:s format
    const visitTimeInput = document.getElementById('visit_time');
    if (visitTimeInput && visitTimeInput.value) {
        // If time is in HH:MM format, append :00 seconds
        if (visitTimeInput.value.match(/^\d{2}:\d{2}$/)) {
            visitTimeInput.value = visitTimeInput.value + ':00';
        }
    }
    
    // Populate hidden fields with selected services and package data
    document.getElementById('selected_services_data').value = JSON.stringify(selectedServices);
    document.getElementById('selected_package_data').value = selectedPackage ? JSON.stringify(selectedPackage) : '';
    
    // Calculate total amount
    let total = 0;
    if (selectedPackage) {
        total += selectedPackage.price;
    }
    selectedServices.forEach(service => {
        total += service.price;
    });
    document.getElementById('total_amount_data').value = total;
    
    console.log("FORM SUBMIT EVENT FIRED");
    console.log("Form action:", this.action);
    console.log("Form method:", this.method);
    console.log("Selected services:", selectedServices);
    console.log("Selected package:", selectedPackage);
    console.log("Total amount:", total);
    
    const formData = new FormData(this);
    console.log("Form data:");
    for (let [key, value] of formData.entries()) {
        console.log(key + ':', value);
    }
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
    submitBtn.disabled = true;
    
    // Submit via AJAX
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers.get('content-type'));
        
        if (response.status === 422) {
            // Validation errors - expect JSON
            return response.json().then(data => {
                let errorMessage = 'Please fix the following errors:\n\n';
                
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        data.errors[field].forEach(error => {
                            errorMessage += `• ${error}\n`;
                        });
                    });
                } else if (data.message) {
                    errorMessage = data.message;
                }
                
                // Show SweetAlert with validation errors
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: errorMessage,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
                
                // Don't submit the form - stop here
                throw new Error('Validation failed');
            }).catch(jsonError => {
                console.error('JSON parsing error:', jsonError);
                // Fallback if JSON parsing fails
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please check all required fields and try again.',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
                // Don't submit the form - stop here
                throw new Error('Validation failed');
            });
        }
        
        if (response.status === 200 || response.status === 302) {
            // Success - check if it's a redirect
            if (response.redirected) {
                // Laravel redirected successfully - go to the new page
                window.location.href = response.url;
                return;
            }
            
            // Try to parse as JSON for success message
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                // Non-JSON success - redirect to visits list
                window.location.href = '/visits';
                return;
            }
        }
        
        // Other status codes - try to parse error
        return response.text().then(text => {
            try {
                return JSON.parse(text);
            } catch {
                return { success: false, message: 'Unexpected response format' };
            }
        });
    })
    .then(data => {
        if (data && data.success) {
            // Success - show SweetAlert and redirect
            const isEdit = {{ isset($visit) ? 'true' : 'false' }};
            Swal.fire({
                icon: 'success',
                title: isEdit ? 'Visit Updated!' : 'Visit Recorded!',
                text: isEdit ? 'Patient visit has been updated successfully.' : 'Patient visit has been recorded successfully.',
                timer: 2000,
                showConfirmButton: false,
                timerProgressBar: true
            }).then(() => {
                window.location.href = '/visits';
            });
        } else {
            // Error from backend
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: (data && data.message) || 'An error occurred while saving the visit.',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        
        // Don't show SweetAlert for validation errors (they're already handled)
        if (error.message === 'Validation failed') {
            // Validation errors are already displayed, just restore button
            return;
        }
        
        // Show SweetAlert for other errors only
        Swal.fire({
            icon: 'error',
            title: 'Network Error',
            text: 'A network error occurred. Please check your connection and try again.',
            confirmButtonColor: '#d33',
            confirmButtonText: 'OK'
        });
    })
    .finally(() => {
        // Restore button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

// Package & Service Selection JavaScript
let selectedServices = [];
let selectedPackage = null;

// Initialize form with existing data (for edit mode)
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing form...');
    
    // Check if we're in edit mode and have existing data
    @if(isset($visit))
        console.log('Edit mode detected');
        
        // Load existing package from backend data first
        @if(isset($selectedPackage))
            console.log('Loading package from backend:', @json($selectedPackage));
            selectedPackage = @json($selectedPackage);
            
            // Set the package dropdown
            const packageSelect = document.getElementById('package_selection');
            if (packageSelect && selectedPackage && selectedPackage.id) {
                packageSelect.value = selectedPackage.id;
                console.log('Set package dropdown to:', selectedPackage.id);
            }
        @endif
        
        // Load existing services from backend data
        @if(isset($selectedServices))
            console.log('Loading services from backend:', @json($selectedServices));
            selectedServices = @json($selectedServices);
        @endif
        
        console.log('Before UI update - Package:', selectedPackage, 'Services:', selectedServices);
        
        // Update UI after loading data
        updateServicesList();
        updatePricing();
        
        console.log('After UI update');
    @else
        console.log('Add mode - no existing data to load');
    @endif
});

// Add service to the list
function addService() {
    const serviceSelect = document.getElementById('service_selection');
    const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
    
    if (!selectedOption.value) {
        alert('Please select a service first');
        return;
    }
    
    const serviceId = selectedOption.value;
    const serviceName = selectedOption.getAttribute('data-name');
    const servicePrice = parseFloat(selectedOption.getAttribute('data-price'));
    
    // Check if service already selected
    if (selectedServices.find(s => s.id === serviceId)) {
        alert('Service already selected');
        return;
    }
    
    // Add to selected services
    selectedServices.push({
        id: serviceId,
        name: serviceName,
        price: servicePrice
    });
    
    updateServicesList();
    updatePricing();
    
    // Reset selection
    serviceSelect.selectedIndex = 0;
}

// Remove service from the list
function removeService(serviceId) {
    selectedServices = selectedServices.filter(s => s.id !== serviceId);
    updateServicesList();
    updatePricing();
}

// Update services list display
function updateServicesList() {
    const servicesList = document.getElementById('services_list');
    const noServicesMessage = document.getElementById('no_services_message');
    
    if (!servicesList || !noServicesMessage) {
        console.error('Services list elements not found');
        return;
    }
    
    if (selectedServices.length === 0 && !selectedPackage) {
        servicesList.style.display = 'none';
        noServicesMessage.style.display = 'block';
    } else {
        servicesList.style.display = 'block';
        noServicesMessage.style.display = 'none';
        
        let html = '';
        
        // Show selected package
        if (selectedPackage) {
            html += `
                <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-white rounded">
                    <div>
                        <strong>${selectedPackage.name || 'Package'}</strong>
                        <span class="badge bg-primary ms-2">Package</span>
                    </div>
                    <div>
                        <span class="fw-bold">GH₵${(selectedPackage.price || 0).toFixed(2)}</span>
                        <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="removePackage()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
        }
        
        // Show selected services
        selectedServices.forEach(service => {
            html += `
                <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-white rounded">
                    <div>
                        <strong>${service.name || 'Service'}</strong>
                        <span class="badge bg-info ms-2">Service</span>
                    </div>
                    <div>
                        <span class="fw-bold">GH₵${(service.price || 0).toFixed(2)}</span>
                        <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="removeService('${service.id}')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
        });
        
        servicesList.innerHTML = html;
    }
}

// Remove package selection
function removePackage() {
    selectedPackage = null;
    document.getElementById('package_selection').selectedIndex = 0;
    updateServicesList();
    updatePricing();
}

// Update pricing display
function updatePricing() {
    let subtotal = 0;
    
    // Add package price
    if (selectedPackage) {
        subtotal += parseFloat(selectedPackage.price) || 0;
    }
    
    // Add services price
    selectedServices.forEach(service => {
        subtotal += parseFloat(service.price) || 0;
    });
    
    // Calculate discount (you can add discount logic here)
    let discount = 0;
    
    // Calculate total
    let total = subtotal - discount;
    
    // Update display
    const subtotalEl = document.getElementById('subtotal');
    const discountEl = document.getElementById('discount');
    const totalEl = document.getElementById('total_amount');
    
    if (subtotalEl) subtotalEl.textContent = `GH₵${subtotal.toFixed(2)}`;
    if (discountEl) discountEl.textContent = `GH₵${discount.toFixed(2)}`;
    if (totalEl) totalEl.textContent = `GH₵${total.toFixed(2)}`;
}

// Package selection handler
document.addEventListener('DOMContentLoaded', function() {
    const packageSelect = document.getElementById('package_selection');
    if (packageSelect) {
        packageSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            
            if (selectedOption.value) {
                // Clear individual services when package is selected
                selectedServices = [];
                selectedPackage = {
                    id: selectedOption.value,
                    name: selectedOption.text.split(' - ')[0],
                    price: parseFloat(selectedOption.getAttribute('data-price'))
                };
            } else {
                selectedPackage = null;
            }
            
            updateServicesList();
            updatePricing();
        });
    }
});

// Helper function for number formatting
function number_format(number, decimals) {
    return number.toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

// Patient Search Functionality
document.addEventListener('DOMContentLoaded', function() {
    const patientSearchInput = document.getElementById('patient_search');
    const patientResultsDiv = document.getElementById('patient_results');
    const patientIdInput = document.getElementById('patient_id');
    const patientNameInput = document.getElementById('patient_name');
    
    // BMI Calculation
    const weightInput = document.getElementById('weight');
    const bmiInput = document.getElementById('bmi');
    let currentPatientHeight = null; // Store current patient's height
    
    function calculateBMI() {
        const weight = parseFloat(weightInput.value);
        
        console.log('BMI Calculation - Weight:', weight, 'Patient Height:', currentPatientHeight);
        
        if (weight > 0 && currentPatientHeight > 0) {
            // Convert height from cm to meters
            const heightInMeters = currentPatientHeight / 100;
            console.log('BMI Calculation - Height in meters:', heightInMeters);
            
            // Validate reasonable ranges
            if (weight > 500 || weight < 1) {
                console.log('Invalid weight value:', weight);
                bmiInput.value = '';
                return;
            }
            
            if (currentPatientHeight > 300 || currentPatientHeight < 50) {
                console.log('Invalid height value:', currentPatientHeight);
                bmiInput.value = '';
                return;
            }
            
            const bmi = weight / (heightInMeters * heightInMeters);
            console.log('BMI Calculation - Calculated BMI:', bmi);
            
            // Validate BMI range (should be between 5 and 100)
            if (bmi > 100 || bmi < 5) {
                console.log('BMI out of reasonable range:', bmi);
                bmiInput.value = '';
                return;
            }
            
            bmiInput.value = bmi.toFixed(1);
        } else {
            bmiInput.value = '';
        }
    }
    
    // Fetch patient data including height
    function fetchPatientData(patientId) {
        fetch(`/patients/${patientId}/json`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentPatientHeight = data.patient.height;
                    console.log('Patient height loaded:', currentPatientHeight);
                    
                    // Trigger BMI calculation if weight is already entered
                    if (weightInput.value) {
                        calculateBMI();
                    }
                } else {
                    console.error('Failed to fetch patient data:', data.message);
                    currentPatientHeight = null;
                }
            })
            .catch(error => {
                console.error('Error fetching patient data:', error);
                currentPatientHeight = null;
            });
    }
    
    // Add event listener for weight input only (height comes from patient record)
    if (weightInput && bmiInput) {
        weightInput.addEventListener('input', calculateBMI);
    }
    
    // Auto-load patient height for edit visits
    @if(isset($visit) && isset($visit->patient_id))
        // When editing a visit, automatically load the patient's height
        fetchPatientData({{ $visit->patient_id }});
    @endif
    
    if (patientSearchInput) {
        let searchTimeout;
        
        patientSearchInput.addEventListener('input', function() {
            const searchTerm = this.value.trim();
            
            // Clear previous timeout
            clearTimeout(searchTimeout);
            
            if (searchTerm.length < 2) {
                patientResultsDiv.innerHTML = '';
                return;
            }
            
            // Set timeout for debouncing
            searchTimeout = setTimeout(() => {
                searchPatients(searchTerm);
            }, 300);
        });
        
        // Hide results when clicking outside
        document.addEventListener('click', function(e) {
            if (!patientSearchInput.contains(e.target) && !patientResultsDiv.contains(e.target)) {
                patientResultsDiv.innerHTML = '';
            }
        });
    }
    
    function searchPatients(searchTerm) {
        fetch(`/api/patients/search?q=${encodeURIComponent(searchTerm)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.patients.length > 0) {
                    displayPatientResults(data.patients);
                } else {
                    patientResultsDiv.innerHTML = '<div class="list-group"><div class="list-group-item text-muted">No patients found</div></div>';
                }
            })
            .catch(error => {
                console.error('Search error:', error);
                patientResultsDiv.innerHTML = '<div class="list-group"><div class="list-group-item text-danger">Search failed</div></div>';
            });
    }
    
    function displayPatientResults(patients) {
        let html = '<div class="list-group">';
        
        patients.forEach(patient => {
            html += `
                <div class="list-group-item list-group-item-action patient-result" 
                     style="cursor: pointer;"
                     data-patient-id="${patient.id}"
                     data-patient-name="${patient.first_name} ${patient.last_name}"
                     data-patient-code="${patient.patient_code}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">${patient.first_name} ${patient.last_name}</h6>
                            <small class="text-muted">${patient.patient_code}</small>
                        </div>
                        <div>
                            <small class="text-muted">${patient.phone || 'No phone'}</small>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        patientResultsDiv.innerHTML = html;
        
        // Add click handlers to patient results
        document.querySelectorAll('.patient-result').forEach(item => {
            item.addEventListener('click', function() {
                const patientId = this.getAttribute('data-patient-id');
                const patientName = this.getAttribute('data-patient-name');
                const patientCode = this.getAttribute('data-patient-code');
                
                // Update form fields
                patientIdInput.value = patientId;
                patientNameInput.value = patientName;
                patientSearchInput.value = `${patientName} (${patientCode})`;
                
                // Fetch patient data including height
                fetchPatientData(patientId);
                
                // Clear results
                patientResultsDiv.innerHTML = '';
                
                // Enable form submission
                console.log('Patient selected:', patientId, patientName);
            });
        });
    }
});
</script>
