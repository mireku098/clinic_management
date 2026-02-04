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
            <form id="addPackageForm">
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
                            <div class="col-md-6">
                                <label for="packagePrice" class="form-label">Price (GH₵) *</label>
                                <input type="number" class="form-control" id="packagePrice" name="packagePrice" required />
                            </div>
                            <div class="col-md-6">
                                <label for="packageDuration" class="form-label">Duration (weeks) *</label>
                                <input type="number" class="form-control" id="packageDuration" name="packageDuration" required />
                            </div>
                            <div class="col-12">
                                <label for="packageDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="packageDescription" name="packageDescription" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Package Features -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-list me-2"></i>Package Features</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="packageFeatures" class="form-label">Features (one per line)</label>
                                <textarea class="form-control" id="packageFeatures" name="packageFeatures" rows="5" 
                                    placeholder="&#10;Twice weekly sessions&#10;Initial assessment&#10;Progress tracking&#10;Home exercise plan"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="maxSessions" class="form-label">Maximum Sessions</label>
                                <input type="number" class="form-control" id="maxSessions" name="maxSessions" min="1" />
                            </div>
                            <div class="col-md-6">
                                <label for="sessionDuration" class="form-label">Session Duration (minutes)</label>
                                <input type="number" class="form-control" id="sessionDuration" name="sessionDuration" min="15" step="15" />
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
                            <div class="col-md-6">
                                <label for="packageColor" class="form-label">Color Theme</label>
                                <input type="color" class="form-control" id="packageColor" name="packageColor" value="#3498db" />
                            </div>
                            <div class="col-md-6">
                                <label for="packageCode" class="form-label">Package Code</label>
                                <input type="text" class="form-control" id="packageCode" name="packageCode" placeholder="e.g., PKG001" />
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
            <div class="card mb-4">
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
            </div>

            <!-- Recent Packages -->
            <div class="card mb-4">
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
            </div>
        </div>
    </div>
</div>

<!-- Alert Container -->
<div id="alert-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1050"></div>
@endsection

@section('js')
<script>
document.getElementById("addPackageForm").addEventListener("submit", function(e) {
    e.preventDefault();
    
    const packageData = {
        name: document.getElementById("packageName").value,
        price: document.getElementById("packagePrice").value,
        duration: document.getElementById("packageDuration").value,
        description: document.getElementById("packageDescription").value,
        features: document.getElementById("packageFeatures").value,
        maxSessions: document.getElementById("maxSessions").value,
        sessionDuration: document.getElementById("sessionDuration").value,
        category: document.getElementById("packageCategory").value,
        status: document.getElementById("packageStatus").value,
        color: document.getElementById("packageColor").value,
        code: document.getElementById("packageCode").value,
    };
    
    // Validate required fields
    if (!packageData.name || !packageData.price || !packageData.duration) {
        window.clinicSystem.showAlert("Please fill in all required fields", "danger");
        return;
    }
    
    console.log("Adding package:", packageData);
    window.clinicSystem.showAlert("Package created successfully!", "success");
    
    setTimeout(() => {
        window.location.href = "{{ route('packages') }}";
    }, 2000);
});

function saveAsDraft() {
    const packageData = {
        name: document.getElementById("packageName").value,
        price: document.getElementById("packagePrice").value,
        duration: document.getElementById("packageDuration").value,
        description: document.getElementById("packageDescription").value,
        features: document.getElementById("packageFeatures").value,
        maxSessions: document.getElementById("maxSessions").value,
        sessionDuration: document.getElementById("sessionDuration").value,
        category: document.getElementById("packageCategory").value,
        status: document.getElementById("packageStatus").value,
        color: document.getElementById("packageColor").value,
        code: document.getElementById("packageCode").value,
    };
    localStorage.setItem("packageDraft", JSON.stringify(packageData));
    window.clinicSystem.showAlert("Draft saved successfully", "info");
}

window.addEventListener("load", function() {
    const draft = localStorage.getItem("packageDraft");
    if (draft) {
        const packageData = JSON.parse(draft);
        Object.keys(packageData).forEach((key) => {
            const field = document.getElementById(key);
            if (field) {
                field.value = packageData[key];
            }
        });
    }
});
</script>
@endsection
