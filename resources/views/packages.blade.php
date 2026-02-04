@extends('layouts.app')

@section('title', 'Treatment Packages')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1>Treatment Packages</h1>
            <p class="text-muted">Manage clinic treatment packages and pricing</p>
        </div>
        <div>
            <a href="{{ route('packages.add') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Add New Package
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-details">
                    <h3>6</h3>
                    <p>Total Packages</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-details">
                    <h3>145</h3>
                    <p>Active Subscriptions</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-info">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-details">
                    <h3>4</h3>
                    <p>Active Packages</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-pause-circle"></i>
                </div>
                <div class="stat-details">
                    <h3>2</h3>
                    <p>Inactive Packages</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" class="form-control" id="packageSearch" placeholder="Search packages by name..." onkeyup="searchPackages()" />
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="statusFilter" onchange="filterByStatus()">
                        <option value="">All Packages</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-outline-secondary" onclick="resetFilters()">
                        <i class="fas fa-redo me-2"></i>
                        Reset Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Packages Grid -->
    <div class="row" id="packagesContainer">
        <!-- Basic Physiotherapy Package -->
        <div class="col-md-6 col-lg-4 mb-4 package-card">
            <div class="card h-100 package-item" data-package="basic-physiotherapy" data-status="active">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="card-title mb-1">Basic Physiotherapy</h5>
                            <p class="text-muted small mb-0">4-week program</p>
                        </div>
                        <span class="badge bg-success">Active</span>
                    </div>

                    <div class="price-section mb-3">
                        <h3 class="text-primary mb-0">GH₵15,000</h3>
                        <small class="text-muted">4 weeks program</small>
                    </div>

                    <div class="features mb-3">
                        <h6 class="mb-2">Includes:</h6>
                        <ul class="list-unstyled small">
                            <li><i class="fas fa-check text-success me-2"></i>Twice weekly sessions</li>
                            <li><i class="fas fa-check text-success me-2"></i>Initial assessment</li>
                            <li><i class="fas fa-check text-success me-2"></i>Progress tracking</li>
                            <li><i class="fas fa-check text-success me-2"></i>Home exercise plan</li>
                        </ul>
                    </div>

                    <div class="text-center mb-3">
                        <small class="text-muted">Active Subscriptions: <strong>32</strong></small>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPackageModal">
                            <i class="fas fa-edit me-2"></i>
                            Edit
                        </button>
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-times me-2"></i>
                            Deactivate
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comprehensive Wellness Package -->
        <div class="col-md-6 col-lg-4 mb-4 package-card">
            <div class="card h-100 package-item" data-package="comprehensive-wellness" data-status="active">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="card-title mb-1">Comprehensive Wellness</h5>
                            <p class="text-muted small mb-0">8-week program</p>
                        </div>
                        <span class="badge bg-success">Active</span>
                    </div>

                    <div class="price-section mb-3">
                        <h3 class="text-primary mb-0">GH₵25,000</h3>
                        <small class="text-muted">8 weeks program</small>
                    </div>

                    <div class="features mb-3">
                        <h6 class="mb-2">Includes:</h6>
                        <ul class="list-unstyled small">
                            <li><i class="fas fa-check text-success me-2"></i>Thrice weekly sessions</li>
                            <li><i class="fas fa-check text-success me-2"></i>Nutritional counseling</li>
                            <li><i class="fas fa-check text-success me-2"></i>Fitness assessment</li>
                            <li><i class="fas fa-check text-success me-2"></i>Wellness plan</li>
                        </ul>
                    </div>

                    <div class="text-center mb-3">
                        <small class="text-muted">Active Subscriptions: <strong>28</strong></small>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPackageModal">
                            <i class="fas fa-edit me-2"></i>
                            Edit
                        </button>
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-times me-2"></i>
                            Deactivate
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Female Health Package -->
        <div class="col-md-6 col-lg-4 mb-4 package-card">
            <div class="card h-100 package-item" data-package="female-health" data-status="active">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="card-title mb-1">Female Health</h5>
                            <p class="text-muted small mb-0">6-week program</p>
                        </div>
                        <span class="badge bg-success">Active</span>
                    </div>

                    <div class="price-section mb-3">
                        <h3 class="text-primary mb-0">GH₵20,000</h3>
                        <small class="text-muted">6 weeks program</small>
                    </div>

                    <div class="features mb-3">
                        <h6 class="mb-2">Includes:</h6>
                        <ul class="list-unstyled small">
                            <li><i class="fas fa-check text-success me-2"></i>Specialized assessment</li>
                            <li><i class="fas fa-check text-success me-2"></i>Pelvic floor therapy</li>
                            <li><i class="fas fa-check text-success me-2"></i>Wellness sessions</li>
                            <li><i class="fas fa-check text-success me-2"></i>Follow-up support</li>
                        </ul>
                    </div>

                    <div class="text-center mb-3">
                        <small class="text-muted">Active Subscriptions: <strong>35</strong></small>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPackageModal">
                            <i class="fas fa-edit me-2"></i>
                            Edit
                        </button>
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-times me-2"></i>
                            Deactivate
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Premium Rehab Package -->
        <div class="col-md-6 col-lg-4 mb-4 package-card">
            <div class="card h-100 package-item" data-package="premium-rehab" data-status="active">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="card-title mb-1">Premium Rehab</h5>
                            <p class="text-muted small mb-0">12-week program</p>
                        </div>
                        <span class="badge bg-success">Active</span>
                    </div>

                    <div class="price-section mb-3">
                        <h3 class="text-primary mb-0">GH₵30,000</h3>
                        <small class="text-muted">12 weeks program</small>
                    </div>

                    <div class="features mb-3">
                        <h6 class="mb-2">Includes:</h6>
                        <ul class="list-unstyled small">
                            <li><i class="fas fa-check text-success me-2"></i>4x weekly sessions</li>
                            <li><i class="fas fa-check text-success me-2"></i>One-on-one coaching</li>
                            <li><i class="fas fa-check text-success me-2"></i>Advanced modalities</li>
                            <li><i class="fas fa-check text-success me-2"></i>Return to function</li>
                        </ul>
                    </div>

                    <div class="text-center mb-3">
                        <small class="text-muted">Active Subscriptions: <strong>25</strong></small>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPackageModal">
                            <i class="fas fa-edit me-2"></i>
                            Edit
                        </button>
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-times me-2"></i>
                            Deactivate
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sports Recovery Package -->
        <div class="col-md-6 col-lg-4 mb-4 package-card">
            <div class="card h-100 package-item" data-package="sports-recovery" data-status="inactive">
                <div class="card-body opacity-75">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="card-title mb-1">Sports Recovery</h5>
                            <p class="text-muted small mb-0">6-week program</p>
                        </div>
                        <span class="badge bg-secondary">Inactive</span>
                    </div>

                    <div class="price-section mb-3">
                        <h3 class="text-primary mb-0">GH₵22,000</h3>
                        <small class="text-muted">6 weeks program</small>
                    </div>

                    <div class="features mb-3">
                        <h6 class="mb-2">Includes:</h6>
                        <ul class="list-unstyled small">
                            <li><i class="fas fa-check text-success me-2"></i>Sport-specific training</li>
                            <li><i class="fas fa-check text-success me-2"></i>Injury prevention</li>
                            <li><i class="fas fa-check text-success me-2"></i>Performance coaching</li>
                            <li><i class="fas fa-check text-success me-2"></i>Return to sport</li>
                        </ul>
                    </div>

                    <div class="text-center mb-3">
                        <small class="text-muted">Active Subscriptions: <strong>18</strong></small>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPackageModal">
                            <i class="fas fa-edit me-2"></i>
                            Edit
                        </button>
                        <button class="btn btn-sm btn-outline-success">
                            <i class="fas fa-check me-2"></i>
                            Activate
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Elderly Care Package -->
        <div class="col-md-6 col-lg-4 mb-4 package-card">
            <div class="card h-100 package-item" data-package="elderly-care" data-status="inactive">
                <div class="card-body opacity-75">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="card-title mb-1">Elderly Care</h5>
                            <p class="text-muted small mb-0">8-week program</p>
                        </div>
                        <span class="badge bg-secondary">Inactive</span>
                    </div>

                    <div class="price-section mb-3">
                        <h3 class="text-primary mb-0">GH₵18,000</h3>
                        <small class="text-muted">8 weeks program</small>
                    </div>

                    <div class="features mb-3">
                        <h6 class="mb-2">Includes:</h6>
                        <ul class="list-unstyled small">
                            <li><i class="fas fa-check text-success me-2"></i>Balance & mobility</li>
                            <li><i class="fas fa-check text-success me-2"></i>Fall prevention</li>
                            <li><i class="fas fa-check text-success me-2"></i>Strength training</li>
                            <li><i class="fas fa-check text-success me-2"></i>Independence support</li>
                        </ul>
                    </div>

                    <div class="text-center mb-3">
                        <small class="text-muted">Active Subscriptions: <strong>7</strong></small>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPackageModal">
                            <i class="fas fa-edit me-2"></i>
                            Edit
                        </button>
                        <button class="btn btn-sm btn-outline-success">
                            <i class="fas fa-check me-2"></i>
                            Activate
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Package Modal -->
<div class="modal fade" id="addPackageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Package</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addPackageForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="packageName" class="form-label">Package Name *</label>
                            <input type="text" class="form-control" id="packageName" required />
                        </div>
                        <div class="col-md-6">
                            <label for="packagePrice" class="form-label">Price (GH₵) *</label>
                            <input type="number" class="form-control" id="packagePrice" required />
                        </div>
                        <div class="col-md-6">
                            <label for="packageDuration" class="form-label">Duration (weeks) *</label>
                            <input type="number" class="form-control" id="packageDuration" required />
                        </div>
                        <div class="col-12">
                            <label for="packageDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="packageDescription" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Package</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Package Modal -->
<div class="modal fade" id="editPackageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Package</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPackageForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="editPackageName" class="form-label">Package Name *</label>
                            <input type="text" class="form-control" id="editPackageName" required />
                        </div>
                        <div class="col-md-6">
                            <label for="editPackagePrice" class="form-label">Price (GH₵) *</label>
                            <input type="number" class="form-control" id="editPackagePrice" required />
                        </div>
                        <div class="col-md-6">
                            <label for="editPackageDuration" class="form-label">Duration (weeks) *</label>
                            <input type="number" class="form-control" id="editPackageDuration" required />
                        </div>
                        <div class="col-12">
                            <label for="editPackageDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="editPackageDescription" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Alert Container -->
<div id="alert-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1050"></div>
@endsection

@section('css')
<style>
    .stat-card {
        background: white;
        border-radius: 0.5rem;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        margin-right: 1rem;
    }

    .package-item {
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
    }

    .package-item:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
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
    document.getElementById("addPackageForm").addEventListener("submit", function (e) {
        e.preventDefault();
        const packageData = {
            name: document.getElementById("packageName").value,
            price: document.getElementById("packagePrice").value,
            duration: document.getElementById("packageDuration").value,
            description: document.getElementById("packageDescription").value,
        };

        console.log("Adding package:", packageData);
        window.clinicSystem.showAlert("Package created successfully!", "success");

        setTimeout(() => {
            bootstrap.Modal.getInstance(document.getElementById("addPackageModal")).hide();
            document.getElementById("addPackageForm").reset();
            window.location.reload();
        }, 1500);
    });

    function searchPackages() {
        const searchTerm = document.getElementById("packageSearch").value.toLowerCase();
        const packages = document.querySelectorAll(".package-card");

        packages.forEach((pkg) => {
            const title = pkg.querySelector(".card-title").textContent.toLowerCase();
            if (title.includes(searchTerm)) {
                pkg.style.display = "block";
            } else {
                pkg.style.display = "none";
            }
        });
    }

    function filterByStatus() {
        const status = document.getElementById("statusFilter").value;
        const packages = document.querySelectorAll(".package-card");

        packages.forEach((pkg) => {
            const pkgStatus = pkg.querySelector(".package-item").dataset.status;
            if (status === "" || pkgStatus === status) {
                pkg.style.display = "block";
            } else {
                pkg.style.display = "none";
            }
        });
    }

    function resetFilters() {
        document.getElementById("packageSearch").value = "";
        document.getElementById("statusFilter").value = "";
        const packages = document.querySelectorAll(".package-card");
        packages.forEach((pkg) => {
            pkg.style.display = "block";
        });
    }
</script>
@endsection
