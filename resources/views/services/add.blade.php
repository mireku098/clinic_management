@extends('layouts.app')

@section('title', 'Add Service')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1>Add New Service</h1>
            <p class="text-muted">Add a new service to the clinic</p>
        </div>
        <div>
            <a href="{{ route('services') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Services
            </a>
        </div>
    </div>

    <!-- Service Form -->
    <div class="row">
        <div class="col-lg-8">
            <form id="add_service_form" method="POST" action="{{ route('services.store') }}">
                @csrf
                <!-- Basic Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="service_name" class="form-label">Service Name *</label>
                                <input type="text" class="form-control" id="service_name" name="service_name" required />
                            </div>
                            <div class="col-md-6">
                                <label for="service_category" class="form-label">Category *</label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="consultation">Consultation</option>
                                    <option value="therapy">Therapy</option>
                                    <option value="diagnostic">Diagnostic</option>
                                    <option value="treatment">Treatment</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="service_price" class="form-label">Price (GH₵) *</label>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required />
                            </div>
                            <div class="col-md-6">
                                <label for="result_type" class="form-label">Result Type *</label>
                                <select class="form-select" id="result_type" name="result_type" required>
                                    <option value="">Select Result Type</option>
                                    <option value="text">Text</option>
                                    <option value="numeric">Numeric</option>
                                    <option value="file">File</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="service_description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
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
                                <label for="service_status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active" selected>Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
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
                                    Add Service
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
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-chart-bar me-2"></i>Service Stats</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Total Services</span>
                        <span class="badge bg-primary">12</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Active</span>
                        <span class="badge bg-success">10</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Inactive</span>
                        <span class="badge bg-warning">2</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Avg. Price</span>
                        <span class="badge bg-info">GH₵6,500</span>
                    </div>
                </div>
            </div>

            <!-- Recent Services -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-history me-2"></i>Recent Services</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-0">Consultation</h6>
                                <small class="text-muted">GH₵5,000</small>
                            </div>
                            <span class="badge bg-success">Active</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-0">Physiotherapy</h6>
                                <small class="text-muted">GH₵8,000</small>
                            </div>
                            <span class="badge bg-success">Active</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-0">Massage Therapy</h6>
                                <small class="text-muted">GH₵6,000</small>
                            </div>
                            <span class="badge bg-success">Active</span>
                        </div>
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
                            Fill in all required fields marked with *
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Choose appropriate category for better organization
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Set duration to help with scheduling
                        </li>
                        <li>
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Use color theme for visual identification
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
document.getElementById("add_service_form").addEventListener("submit", function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
    
    fetch('{{ route("services.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        // Check if response is HTML (validation error page)
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('text/html')) {
            return response.text().then(html => {
                // Parse validation errors from HTML if possible
                throw new Error('Validation failed. Please check all required fields.');
            });
        }
        
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Service created successfully!',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                // Redirect to services page
                window.location.href = '{{ route("services") }}';
            });
        } else {
            // Handle validation errors
            let errorMessage = data.message || 'Something went wrong. Please try again.';
            
            if (data.errors) {
                // Show specific validation errors
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
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

function saveAsDraft() {
    const formData = new FormData(document.getElementById("add_service_form"));
    const serviceData = Object.fromEntries(formData);
    localStorage.setItem("serviceDraft", JSON.stringify(serviceData));
    
    Swal.fire({
        icon: 'info',
        title: 'Draft Saved',
        text: 'Your form data has been saved as a draft.',
        timer: 1500,
        showConfirmButton: false
    });
}

window.addEventListener("load", function() {
    const draft = localStorage.getItem("serviceDraft");
    if (draft) {
        const serviceData = JSON.parse(draft);
        Object.keys(serviceData).forEach((key) => {
            const field = document.querySelector(`[name="${key}"]`);
            if (field) {
                field.value = serviceData[key];
            }
        });
    }
});
</script>
@endsection
