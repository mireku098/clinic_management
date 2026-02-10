@extends('layouts.app')

@section('title', 'Add Package')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1>Add New Package</h1>
            <p class="text-muted">Create a new treatment package</p>
        </div>
        <div>
            <a href="{{ route('packages') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Packages
            </a>
        </div>
    </div>

    <!-- Package Form -->
    <div class="row">
        <div class="col-lg-8">
            <form id="addPackageForm" method="POST" action="{{ route('packages.store') }}">
                @csrf
                <!-- Basic Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="packageName" class="form-label">Package Name *</label>
                                <input type="text" class="form-control" id="packageName" name="packageName" required />
                            </div>
                            <div class="col-12">
                                <label for="packageDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="packageDescription" name="packageDescription" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Package Duration -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-clock me-2"></i>Package Duration</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="durationValue" class="form-label">Duration Value *</label>
                                <input type="number" class="form-control" id="durationValue" name="durationValue" min="1" required />
                            </div>
                            <div class="col-md-4">
                                <label for="durationType" class="form-label">Duration Type *</label>
                                <select class="form-select" id="durationType" name="durationType" required>
                                    <option value="weeks">Weeks</option>
                                    <option value="months">Months</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="totalWeeks" class="form-label">Total Weeks (Calculated)</label>
                                <input type="number" class="form-control" id="totalWeeks" name="totalWeeks" readonly />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Package Services -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><i class="fas fa-concierge-bell me-2"></i>Package Services</h5>
                        <button type="button" class="btn btn-primary btn-sm" onclick="addService()">
                            <i class="fas fa-plus me-1"></i>Add Service
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="servicesContainer">
                            <!-- Services will be added here dynamically -->
                        </div>
                        <div class="mt-3" id="noServicesMessage">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                No services added yet. Click "Add Service" to begin building your package.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Package Cost Summary -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-calculator me-2"></i>Cost Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Subtotal (Services)</label>
                                <div class="input-group">
                                    <span class="input-group-text">GH₵</span>
                                    <input type="text" class="form-control" id="subtotalServices" readonly />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Total Package Cost</label>
                                <div class="input-group">
                                    <span class="input-group-text">GH₵</span>
                                    <input type="text" class="form-control" id="totalPackageCost" readonly />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Total Sessions</label>
                                <input type="text" class="form-control" id="totalSessions" readonly />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Package Duration</label>
                                <input type="text" class="form-control" id="packageDuration" readonly />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Settings -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-cog me-2"></i>Additional Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="packageCategory" class="form-label">Category</label>
                                <select class="form-select" id="packageCategory" name="packageCategory">
                                    <option value="">Select Category</option>
                                    <option value="physiotherapy">Physiotherapy</option>
                                    <option value="wellness">Wellness</option>
                                    <option value="rehabilitation">Rehabilitation</option>
                                    <option value="specialized">Specialized</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="packageStatus" class="form-label">Status</label>
                                <select class="form-select" id="packageStatus" name="packageStatus">
                                    <option value="active" selected>Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label for="packageCode" class="form-label">Package Code (Auto-generated)</label>
                                <input type="text" class="form-control" id="packageCode" name="packageCode" value="Will be generated automatically" readonly />
                                <small class="text-muted">Package codes are automatically generated in the format PKG-XXXXXX</small>
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
                                Clear Form
                            </button>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-info" onclick="saveAsDraft()">
                                    <i class="fas fa-save me-2"></i>
                                    Save as Draft
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-check me-2"></i>
                                    Create Package
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Stats -->
            <!-- <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-chart-bar me-2"></i>Package Stats</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Total Packages</span>
                        <span class="badge bg-primary">6</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Active</span>
                        <span class="badge bg-success">4</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Inactive</span>
                        <span class="badge bg-warning">2</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Active Subscriptions</span>
                        <span class="badge bg-info">145</span>
                    </div>
                </div>
            </div> -->

            <!-- Recent Packages -->
            <!-- <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-history me-2"></i>Recent Packages</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-0">Basic Physiotherapy</h6>
                                <small class="text-muted">GH₵15,000 • 4 weeks</small>
                            </div>
                            <span class="badge bg-success">Active</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-0">Comprehensive Wellness</h6>
                                <small class="text-muted">GH₵25,000 • 8 weeks</small>
                            </div>
                            <span class="badge bg-success">Active</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-0">Female Health</h6>
                                <small class="text-muted">GH₵20,000 • 6 weeks</small>
                            </div>
                            <span class="badge bg-success">Active</span>
                        </div>
                    </div>
                </div>
            </div> -->

            <!-- Help -->
            <!-- <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-question-circle me-2"></i>Help</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled small">
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Fill in all required fields marked with *
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            List features one per line for better formatting
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Set realistic duration and session limits
                        </li>
                        <li>
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Use color theme for visual identification
                        </li>
                    </ul>
                </div>
            </div> -->
        </div>
    </div>
</div>

<!-- Alert Container -->
<div id="alert-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1050"></div>
@endsection

@section('js')
<script>
let serviceCounter = 0;
let services = [];

// Services data from database
@if(isset($services))
    const servicesOptions = {!! $services->map(function($service) {
        return [
            'id' => $service->id,
            'name' => $service->service_name,
            'code' => $service->service_code,
            'price' => $service->price,
            'category' => $service->category
        ];
    })->toJson() !!};
@else
    const servicesOptions = [];
@endif

// Simple alert function to replace missing showAlert
function showAlert(message, type = 'info') {
    // Create alert container if it doesn't exist
    let alertContainer = document.getElementById('alert-container');
    if (!alertContainer) {
        alertContainer = document.createElement('div');
        alertContainer.id = 'alert-container';
        alertContainer.className = 'position-fixed top-0 end-0 p-3';
        alertContainer.style.zIndex = '1050';
        document.body.appendChild(alertContainer);
    }
    
    // Create alert element
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show mb-2`;
    alert.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
            <div class="flex-grow-1">${message}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Add alert to container
    alertContainer.appendChild(alert);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}

// Service template HTML
function getServiceTemplate(serviceId) {
    return `
        <div class="service-item border rounded p-3 mb-3" id="service-${serviceId}">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0"><i class="fas fa-concierge-bell me-2"></i>Service #${serviceId}</h6>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeService(${serviceId})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Select Service *</label>
                    <select class="form-control service-select" id="serviceSelect-${serviceId}" required>
                        <option value="">Choose a service...</option>
                        ${servicesOptions.map(service => 
                            `<option value="${service.id}" data-price="${service.price}" data-name="${service.name}">
                                ${service.name} (${service.code}) - GH₵${service.price}
                            </option>`
                        ).join('')}
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Cost per Session (GH₵)</label>
                    <input type="text" class="form-control service-cost" id="serviceCost-${serviceId}" readonly />
                </div>
                <div class="col-md-4">
                    <label class="form-label">Frequency Type *</label>
                    <select class="form-select frequency-type" id="frequencyType-${serviceId}" required>
                        <option value="">Select...</option>
                        <option value="once">Once</option>
                        <option value="per_week">Per Week</option>
                        <option value="per_month">Per Month</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Frequency Value *</label>
                    <input type="number" class="form-control frequency-value" id="frequencyValue-${serviceId}" min="1" required />
                </div>
                <div class="col-md-4">
                    <label class="form-label">Total Sessions (Calculated)</label>
                    <input type="number" class="form-control service-sessions" id="serviceSessions-${serviceId}" readonly />
                </div>
                <div class="col-md-12">
                    <label class="form-label">Service Cost (Calculated)</label>
                    <input type="text" class="form-control service-total-cost" id="serviceTotalCost-${serviceId}" readonly />
                </div>
            </div>
        </div>
    `;
}

// Add new service
function addService() {
    serviceCounter++;
    const serviceId = serviceCounter;
    
    const servicesContainer = document.getElementById('servicesContainer');
    const serviceHtml = getServiceTemplate(serviceId);
    
    // Insert at the beginning (top) instead of at the end
    servicesContainer.insertAdjacentHTML('afterbegin', serviceHtml);
    
    // Add event listeners for this service
    const serviceElement = document.getElementById(`service-${serviceId}`);
    
    serviceElement.querySelector('.service-select').addEventListener('change', () => handleServiceSelection(serviceId));
    serviceElement.querySelector('.frequency-type').addEventListener('change', () => calculateServiceCost(serviceId));
    serviceElement.querySelector('.frequency-value').addEventListener('input', () => calculateServiceCost(serviceId));
    
    // Hide no services message
    document.getElementById('noServicesMessage').style.display = 'none';
    
    // Add to services array at the beginning
    services.unshift({
        id: serviceId,
        serviceId: null,
        serviceName: '',
        costPerSession: 0,
        frequencyType: '',
        frequencyValue: 0,
        totalSessions: 0,
        totalCost: 0
    });
    
    updateTotals();
}

// Remove service
function removeService(serviceId) {
    const serviceElement = document.getElementById(`service-${serviceId}`);
    serviceElement.remove();
    
    // Remove from services array
    services = services.filter(s => s.id !== serviceId);
    
    // Show no services message if empty
    if (services.length === 0) {
        document.getElementById('noServicesMessage').style.display = 'block';
    }
    
    updateTotals();
}

// Handle service selection
function handleServiceSelection(serviceId) {
    const selectElement = document.getElementById(`serviceSelect-${serviceId}`);
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    
    if (selectedOption.value) {
        const serviceName = selectedOption.getAttribute('data-name');
        const costPerSession = parseFloat(selectedOption.getAttribute('data-price'));
        
        // Update cost field
        document.getElementById(`serviceCost-${serviceId}`).value = `GH₵${costPerSession.toFixed(2)}`;
        
        // Update services array
        const service = services.find(s => s.id === serviceId);
        if (service) {
            service.serviceId = parseInt(selectedOption.value);
            service.serviceName = serviceName;
            service.costPerSession = costPerSession;
        }
    } else {
        // Clear fields
        document.getElementById(`serviceCost-${serviceId}`).value = '';
        document.getElementById(`serviceSessions-${serviceId}`).value = '';
        document.getElementById(`serviceTotalCost-${serviceId}`).value = '';
        
        // Update services array
        const service = services.find(s => s.id === serviceId);
        if (service) {
            service.serviceId = null;
            service.serviceName = '';
            service.costPerSession = 0;
            service.totalSessions = 0;
            service.totalCost = 0;
        }
    }
    
    calculateServiceCost(serviceId);
}

// Calculate individual service cost and sessions
function calculateServiceCost(serviceId) {
    const frequencyType = document.getElementById(`frequencyType-${serviceId}`).value;
    const frequencyValue = parseInt(document.getElementById(`frequencyValue-${serviceId}`).value) || 0;
    const service = services.find(s => s.id === serviceId);
    const costPerSession = service ? service.costPerSession : 0;
    const totalWeeks = getTotalWeeks();
    
    let totalSessions = 0;
    
    if (frequencyType === 'once') {
        totalSessions = frequencyValue;
    } else if (frequencyType === 'per_week') {
        totalSessions = frequencyValue * totalWeeks;
    } else if (frequencyType === 'per_month') {
        const totalMonths = totalWeeks / 4.33; // Average weeks per month
        totalSessions = Math.floor(frequencyValue * totalMonths);
    }
    
    const totalCost = totalSessions * costPerSession;
    
    // Update display
    document.getElementById(`serviceSessions-${serviceId}`).value = totalSessions;
    document.getElementById(`serviceTotalCost-${serviceId}`).value = `GH₵${totalCost.toFixed(2)}`;
    
    // Update services array
    if (service) {
        service.frequencyType = frequencyType;
        service.frequencyValue = frequencyValue;
        service.totalSessions = totalSessions;
        service.totalCost = totalCost;
    }
    
    updateTotals();
}

// Calculate total weeks from duration
function getTotalWeeks() {
    const durationValue = parseInt(document.getElementById('durationValue').value) || 0;
    const durationType = document.getElementById('durationType').value;
    
    let totalWeeks = 0;
    
    if (durationType === 'weeks') {
        totalWeeks = durationValue;
    } else if (durationType === 'months') {
        totalWeeks = durationValue * 4.33; // Average weeks per month
    }
    
    // Round up to nearest whole number
    return Math.ceil(totalWeeks);
}

// Update all totals
function updateTotals() {
    const subtotalServices = services.reduce((sum, service) => sum + service.totalCost, 0);
    
    // Total package cost is now just the services subtotal (no additional package fee)
    const totalPackageCost = subtotalServices;
    
    const totalSessions = services.reduce((sum, service) => sum + service.totalSessions, 0);
    const durationValue = document.getElementById('durationValue').value;
    const durationType = document.getElementById('durationType').value;
    
    // Update display
    document.getElementById('subtotalServices').value = subtotalServices.toFixed(2);
    document.getElementById('totalPackageCost').value = totalPackageCost.toFixed(2);
    document.getElementById('totalSessions').value = totalSessions;
    document.getElementById('packageDuration').value = `${durationValue} ${durationType}`;
}

// Calculate total weeks when duration changes
function calculateTotalWeeks() {
    const totalWeeks = getTotalWeeks();
    document.getElementById('totalWeeks').value = totalWeeks; // Now displays as whole number
    
    // Recalculate all services
    services.forEach(service => {
        calculateServiceCost(service.id);
    });
}

// Form submission
document.getElementById("addPackageForm").addEventListener("submit", function(e) {
    e.preventDefault();
    
    const packageData = {
        name: document.getElementById("packageName").value,
        description: document.getElementById("packageDescription").value,
        durationValue: document.getElementById("durationValue").value,
        durationType: document.getElementById("durationType").value,
        totalWeeks: document.getElementById("totalWeeks").value,
        services: services.map(s => ({
            serviceId: s.serviceId,
            serviceName: s.serviceName,
            costPerSession: s.costPerSession,
            frequencyType: s.frequencyType,
            frequencyValue: s.frequencyValue,
            totalSessions: s.totalSessions,
            totalCost: s.totalCost
        })),
        totalCost: parseFloat(document.getElementById('totalPackageCost').textContent.replace('GH₵', '')),
        category: document.getElementById("packageCategory").value,
        status: document.getElementById("packageStatus").value,
    };
    
    // Client-side validation
    if (!packageData.name || !packageData.durationValue || services.length === 0) {
        showAlert("Please fill in all required fields and add at least one service", "danger");
        return;
    }
    
    // Validate services
    for (let service of packageData.services) {
        if (!service.serviceId || !service.serviceName || !service.frequencyType || !service.frequencyValue) {
            showAlert("Please complete all service details", "danger");
            return;
        }
    }
    
    // Show loading state
    const submitButton = document.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating Package...';
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                      document.querySelector('input[name="_token"]')?.value;
    
    // Send data to backend
    fetch("{{ route('packages.store') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify(packageData)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw data;
            });
        }
        return response.json();
    })
    .then(data => {
        console.log("Package created successfully:", data);
        
        if (data.success) {
            showAlert(data.message || "Package created successfully!", "success");
            
            // Clear draft on successful creation
            localStorage.removeItem("packageDraft");
            
            // Redirect after delay
            setTimeout(() => {
                window.location.href = "{{ route('packages') }}";
            }, 1500);
        } else {
            throw new Error(data.message || "Unknown error occurred");
        }
    })
    .catch(error => {
        console.error("Package creation failed:", error);
        
        // Restore button state
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
        
        // Handle different error types
        if (error.errors && typeof error.errors === 'object') {
            // Validation errors
            let errorMessages = [];
            for (let field in error.errors) {
                if (Array.isArray(error.errors[field])) {
                    errorMessages = errorMessages.concat(error.errors[field]);
                } else {
                    errorMessages.push(error.errors[field]);
                }
            }
            showAlert(errorMessages.join('<br>'), "danger");
        } else if (error.message) {
            // Single error message
            showAlert(error.message, "danger");
        } else {
            // Generic error
            showAlert("An error occurred while creating the package. Please try again.", "danger");
        }
    });
});

function saveAsDraft() {
    const packageData = {
        name: document.getElementById("packageName").value,
        description: document.getElementById("packageDescription").value,
        durationValue: document.getElementById("durationValue").value,
        durationType: document.getElementById("durationType").value,
        services: services.map(s => ({
            serviceId: s.serviceId,
            serviceName: s.serviceName,
            costPerSession: s.costPerSession,
            frequencyType: s.frequencyType,
            frequencyValue: s.frequencyValue,
            totalSessions: s.totalSessions,
            totalCost: s.totalCost
        })),
        category: document.getElementById("packageCategory").value,
        status: document.getElementById("packageStatus").value,
    };
    localStorage.setItem("packageDraft", JSON.stringify(packageData));
    showAlert("Draft saved successfully", "info");
}

window.addEventListener("load", function() {
    // Set up event listeners for duration fields
    document.getElementById('durationValue').addEventListener('input', calculateTotalWeeks);
    document.getElementById('durationType').addEventListener('change', calculateTotalWeeks);
    
    const draft = localStorage.getItem("packageDraft");
    if (draft) {
        const packageData = JSON.parse(draft);
        document.getElementById("packageName").value = packageData.name || '';
        document.getElementById("packageDescription").value = packageData.description || '';
        document.getElementById("durationValue").value = packageData.durationValue || '';
        document.getElementById("durationType").value = packageData.durationType || 'weeks';
        document.getElementById("packageCategory").value = packageData.category || '';
        document.getElementById("packageStatus").value = packageData.status || 'active';
        
        // Restore services
        if (packageData.services && packageData.services.length > 0) {
            packageData.services.forEach(serviceData => {
                addService();
                const currentService = services[services.length - 1];
                document.getElementById(`serviceSelect-${currentService.id}`).value = serviceData.serviceId || '';
                handleServiceSelection(currentService.id);
                document.getElementById(`frequencyType-${currentService.id}`).value = serviceData.frequencyType || '';
                document.getElementById(`frequencyValue-${currentService.id}`).value = serviceData.frequencyValue || '';
                calculateServiceCost(currentService.id);
            });
        }
        
        calculateTotalWeeks();
    }
});
</script>

<!-- Alert Container -->
<div id="alert-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1050"></div>

@endsection
