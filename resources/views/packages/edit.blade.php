@extends('layouts.app')

@section('title', 'Edit Package')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1>Edit Package</h1>
            <p class="text-muted">Update package information and services</p>
        </div>
        <div>
            <a href="{{ route('packages') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Packages
            </a>
        </div>
    </div>

    <form id="packageForm" method="POST" action="{{ route('packages.update', $package->id) }}">
        @csrf
        @method('PUT')
        
        <!-- Basic Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label for="packageName" class="form-label">Package Name *</label>
                        <input type="text" class="form-control" id="packageName" name="packageName" value="{{ $package->package_name }}" required />
                    </div>
                    <div class="col-12">
                        <label for="packageDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="packageDescription" name="packageDescription" rows="3">{{ $package->description }}</textarea>
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
                        <input type="number" class="form-control" id="durationValue" name="durationValue" min="1" value="{{ $package->duration_weeks }}" required />
                    </div>
                    <div class="col-md-4">
                        <label for="durationType" class="form-label">Duration Type *</label>
                        <select class="form-select" id="durationType" name="durationType" required>
                            <option value="weeks" {{ $package->duration_weeks <= 4 ? 'selected' : '' }}>Weeks</option>
                            <option value="months" {{ $package->duration_weeks > 4 ? 'selected' : '' }}>Months</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="totalWeeks" class="form-label">Total Weeks (Calculated)</label>
                        <input type="number" class="form-control" id="totalWeeks" name="totalWeeks" value="{{ $package->duration_weeks }}" readonly />
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
                <div id="servicesList">
                    <!-- Services will be dynamically added here -->
                </div>
            </div>
        </div>

        <!-- Cost Summary -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-calculator me-2"></i>Cost Summary</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="subtotalServices" class="form-label">Subtotal (Services)</label>
                        <input type="text" class="form-control" id="subtotalServices" value="GH₵{{ number_format($package->total_cost, 2) }}" readonly />
                    </div>
                    <div class="col-md-4">
                        <label for="totalPackageCost" class="form-label">Total Package Cost</label>
                        <input type="text" class="form-control" id="totalPackageCost" value="GH₵{{ number_format($package->total_cost, 2) }}" readonly />
                    </div>
                    <div class="col-md-4">
                        <label for="totalSessions" class="form-label">Total Sessions</label>
                        <input type="text" class="form-control" id="totalSessions" value="{{ $package->services->sum('sessions') }}" readonly />
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
                            <option value="physiotherapy" {{ $package->category === 'physiotherapy' ? 'selected' : '' }}>Physiotherapy</option>
                            <option value="wellness" {{ $package->category === 'wellness' ? 'selected' : '' }}>Wellness</option>
                            <option value="rehabilitation" {{ $package->category === 'rehabilitation' ? 'selected' : '' }}>Rehabilitation</option>
                            <option value="sports" {{ $package->category === 'sports' ? 'selected' : '' }}>Sports</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="packageStatus" class="form-label">Status</label>
                        <select class="form-select" id="packageStatus" name="packageStatus">
                            <option value="active" {{ $package->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $package->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" onclick="saveAsDraft()">
                        <i class="fas fa-save me-2"></i>Save Draft
                    </button>
                    <div>
                        <a href="{{ route('packages') }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check me-2"></i>Update Package
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Alert Container -->
<div id="alert-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1050"></div>
@endsection

@section('css')
<style>
    .service-item {
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }
    
    .service-item:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .price-section {
        background: #f0f4f8;
        padding: 1rem;
        border-radius: 0.5rem;
    }
    
    .features {
        border-left: 3px solid #3498db;
        padding-left: 1rem;
    }
    
    .features ul li {
        margin-bottom: 0.5rem;
    }
</style>
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

// Existing package services
const existingServices = @json($package->services);

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
                    <label class="form-label">Service Name *</label>
                    <select class="form-select" id="serviceName-${serviceId}" onchange="handleServiceSelection(${serviceId})">
                        <option value="">Select a service</option>
                        ${servicesOptions.map(service => 
                            `<option value="${service.id}" data-price="${service.price}">${service.name} (GH₵${service.price})</option>`
                        ).join('')}
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Cost per Session (GH₵) *</label>
                    <input type="number" class="form-control" id="costPerSession-${serviceId}" min="0" step="0.01" readonly />
                </div>
                <div class="col-md-4">
                    <label class="form-label">Frequency Type *</label>
                    <select class="form-select" id="frequencyType-${serviceId}" onchange="calculateServiceCost(${serviceId})">
                        <option value="once">Once</option>
                        <option value="per_week">Per Week</option>
                        <option value="per_month">Per Month</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Frequency Value *</label>
                    <input type="number" class="form-control" id="frequencyValue-${serviceId}" min="1" value="1" onchange="calculateServiceCost(${serviceId})" />
                </div>
                <div class="col-md-4">
                    <label class="form-label">Total Sessions (Calculated)</label>
                    <input type="number" class="form-control" id="totalSessions-${serviceId}" readonly />
                </div>
                <div class="col-12">
                    <label class="form-label">Service Cost (Calculated)</label>
                    <input type="text" class="form-control" id="serviceCost-${serviceId}" readonly />
                </div>
            </div>
        </div>
    `;
}

// Add service to the form
function addService() {
    serviceCounter++;
    const serviceId = serviceCounter;
    
    // Add to services array
    services.unshift({
        id: serviceId,
        serviceId: '',
        serviceName: '',
        costPerSession: 0,
        frequencyType: 'once',
        frequencyValue: 1,
        totalSessions: 0,
        totalCost: 0
    });
    
    // Add to DOM
    const servicesList = document.getElementById('servicesList');
    servicesList.insertAdjacentHTML('afterbegin', getServiceTemplate(serviceId));
}

// Remove service from form
function removeService(serviceId) {
    // Remove from services array
    services = services.filter(s => s.id !== serviceId);
    
    // Remove from DOM
    const serviceElement = document.getElementById(`service-${serviceId}`);
    if (serviceElement) {
        serviceElement.remove();
    }
    
    // Update totals
    updateTotals();
}

// Handle service selection
function handleServiceSelection(serviceId) {
    const select = document.getElementById(`serviceName-${serviceId}`);
    const costInput = document.getElementById(`costPerSession-${serviceId}`);
    const selectedOption = select.options[select.selectedIndex];
    
    if (selectedOption.value) {
        const price = parseFloat(selectedOption.dataset.price);
        costInput.value = price;
        
        // Update service data
        const service = services.find(s => s.id === serviceId);
        if (service) {
            service.serviceId = selectedOption.value;
            service.serviceName = selectedOption.text;
            service.costPerSession = price;
        }
    } else {
        costInput.value = '';
    }
    
    calculateServiceCost(serviceId);
}

// Calculate service cost
function calculateServiceCost(serviceId) {
    const service = services.find(s => s.id === serviceId);
    if (!service) return;
    
    const frequencyType = document.getElementById(`frequencyType-${serviceId}`).value;
    const frequencyValue = parseInt(document.getElementById(`frequencyValue-${serviceId}`).value) || 1;
    const totalWeeks = parseInt(document.getElementById('totalWeeks').value) || 0;
    const costPerSession = parseFloat(document.getElementById(`costPerSession-${serviceId}`).value) || 0;
    
    let totalSessions = 0;
    
    switch (frequencyType) {
        case 'once':
            totalSessions = 1;
            break;
        case 'per_week':
            totalSessions = totalWeeks * frequencyValue;
            break;
        case 'per_month':
            totalSessions = Math.ceil(totalWeeks / 4.33) * frequencyValue;
            break;
    }
    
    const totalCost = totalSessions * costPerSession;
    
    // Update display
    document.getElementById(`totalSessions-${serviceId}`).value = totalSessions;
    document.getElementById(`serviceCost-${serviceId}`).value = `GH₵${totalCost.toFixed(2)}`;
    
    // Update service data
    service.frequencyType = frequencyType;
    service.frequencyValue = frequencyValue;
    service.totalSessions = totalSessions;
    service.totalCost = totalCost;
    
    updateTotals();
}

// Calculate total weeks
function calculateTotalWeeks() {
    const durationValue = parseInt(document.getElementById('durationValue').value) || 0;
    const durationType = document.getElementById('durationType').value;
    
    let totalWeeks = 0;
    
    if (durationType === 'weeks') {
        totalWeeks = durationValue;
    } else if (durationType === 'months') {
        totalWeeks = Math.ceil(durationValue * 4.33);
    }
    
    document.getElementById('totalWeeks').value = totalWeeks;
    
    // Recalculate all service costs
    services.forEach(service => {
        calculateServiceCost(service.id);
    });
}

// Update totals
function updateTotals() {
    const subtotalServices = services.reduce((sum, service) => sum + service.totalCost, 0);
    const totalPackageCost = subtotalServices; // Same as subtotal
    const totalSessions = services.reduce((sum, service) => sum + service.totalSessions, 0);
    
    document.getElementById('subtotalServices').value = `GH₵${subtotalServices.toFixed(2)}`;
    document.getElementById('totalPackageCost').value = `GH₵${totalPackageCost.toFixed(2)}`;
    document.getElementById('totalSessions').value = totalSessions;
}

// Form submission
document.getElementById("packageForm").addEventListener("submit", function(e) {
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
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating Package...';
    
    // Send data to backend
    fetch("{{ route('packages.update', $package->id) }}", {
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(packageData)
    })
    .then(response => {
        if (!response.ok) {
            if (response.status === 422) {
                return response.json().then(data => {
                    throw data;
                });
            }
            return response.json().then(data => {
                throw data;
            });
        }
        return response.json();
    })
    .then(data => {
        console.log("Package updated successfully:", data);
        
        if (data.success) {
            showAlert(data.message || "Package updated successfully!", "success");
            
            // Clear draft on successful update
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
        console.error("Package update failed:", error);
        
        // Restore button
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
        
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
            showAlert("An error occurred while updating the package. Please try again.", "danger");
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
    
    // Load existing services
    if (existingServices && existingServices.length > 0) {
        existingServices.forEach((existingService, index) => {
            addService();
            
            const currentService = services[0]; // Most recently added service
            
            // Find the service in options
            const serviceOption = servicesOptions.find(opt => opt.id === existingService.service_id);
            
            if (serviceOption) {
                // Set service selection
                const serviceSelect = document.getElementById(`serviceName-${currentService.id}`);
                serviceSelect.value = existingService.service_id;
                
                // Set cost per session
                document.getElementById(`costPerSession-${currentService.id}`).value = existingService.unit_price;
                
                // Set frequency
                document.getElementById(`frequencyType-${currentService.id}`).value = existingService.frequency_type;
                document.getElementById(`frequencyValue-${currentService.id}`).value = existingService.frequency_value;
                
                // Calculate and set totals
                calculateServiceCost(currentService.id);
                
                // Update service data
                currentService.serviceId = existingService.service_id;
                currentService.serviceName = serviceOption.name;
                currentService.costPerSession = existingService.unit_price;
                currentService.frequencyType = existingService.frequency_type;
                currentService.frequencyValue = existingService.frequency_value;
            }
        });
    }
    
    calculateTotalWeeks();
});
</script>
@endsection
