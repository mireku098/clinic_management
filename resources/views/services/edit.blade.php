@extends('layouts.app')

@section('title', 'Edit Service')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1>Edit Service</h1>
            <p class="text-muted">Update service information</p>
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
            <form id="edit_service_form" method="POST" action="{{ route('services.update', $service->id) }}">
                @csrf
                @method('PUT')
                <!-- Basic Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="service_name" class="form-label">Service Name *</label>
                                <input type="text" class="form-control" id="service_name" name="service_name" value="{{ $service->service_name }}" required />
                            </div>
                            <div class="col-md-6">
                                <label for="service_category" class="form-label">Category *</label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="consultation" {{ $service->category === 'consultation' ? 'selected' : '' }}>Consultation</option>
                                    <option value="therapy" {{ $service->category === 'therapy' ? 'selected' : '' }}>Therapy</option>
                                    <option value="diagnostic" {{ $service->category === 'diagnostic' ? 'selected' : '' }}>Diagnostic</option>
                                    <option value="treatment" {{ $service->category === 'treatment' ? 'selected' : '' }}>Treatment</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="service_price" class="form-label">Price (GH₵) *</label>
                                <input type="number" class="form-control" id="price" name="price" value="{{ $service->price }}" step="0.01" min="0" required />
                            </div>
                            <div class="col-md-6">
                                <label for="service_code" class="form-label">Service Code</label>
                                <input type="text" class="form-control" id="service_code" name="service_code" value="{{ $service->service_code }}" readonly />
                                <small class="text-muted">Service codes cannot be changed</small>
                            </div>
                            <div class="col-md-6">
                                <label for="result_type" class="form-label">Result Type *</label>
                                <select class="form-select" id="result_type" name="result_type" required>
                                    <option value="">Select Result Type</option>
                                    <option value="text" {{ $service->result_type === 'text' ? 'selected' : '' }}>Text</option>
                                    <option value="numeric" {{ $service->result_type === 'numeric' ? 'selected' : '' }}>Numeric</option>
                                    <option value="file" {{ $service->result_type === 'file' ? 'selected' : '' }}>File</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="service_description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3">{{ $service->description ?? '' }}</textarea>
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
                                    <option value="active" {{ $service->status === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $service->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                                Reset Form
                            </button>
                            <div class="d-flex gap-2">
                                <a href="{{ route('services') }}" class="btn btn-outline-info">
                                    <i class="fas fa-times me-2"></i>
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    Update Service
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Service Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle me-2"></i>Service Information</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Service Code</span>
                        <span class="badge bg-primary">{{ $service->service_code }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Status</span>
                        <span class="badge bg-{{ $service->status === 'active' ? 'success' : 'warning' }}">
                            {{ ucfirst($service->status) }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Created</span>
                        <span class="text-muted">{{ $service->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Last Updated</span>
                        <span class="text-muted">{{ $service->updated_at->format('M d, Y') }}</span>
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
                            Service codes cannot be changed after creation
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Result type determines how service results are stored
                        </li>
                        <li>
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Inactive services won't appear in service selection
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
document.getElementById("edit_service_form").addEventListener("submit", function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
    
    fetch('{{ route("services.update", $service->id) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('text/html')) {
            return response.text().then(html => {
                throw new Error('Validation failed. Please check all required fields.');
            });
        }
        
        return response.json();
    })
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Service updated successfully!',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = '{{ route("services") }}';
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
</script>
@endsection
