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
                    <h3>{{ isset($packages) ? $packages->count() : 0 }}</h3>
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
                    <h3>{{ isset($packages) ? $packages->where('status', 'active')->count() : 0 }}</h3>
                    <p>Active Packages</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-info">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-details">
                    <h3>{{ isset($packages) ? $packages->where('status', 'inactive')->count() : 0 }}</h3>
                    <p>Inactive Packages</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-details">
                    <h3>{{ isset($packages) ? $packages->sum('duration_weeks') : 0 }}</h3>
                    <p>Total Weeks</p>
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
        @if(!isset($packages))
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No packages found. <a href="{{ route('packages.add') }}" class="alert-link">Create your first package</a>.
                </div>
            </div>
        @else
            @foreach($packages as $package)
                <div class="col-md-6 col-lg-4 mb-4 package-card" data-package="{{ $package->package_code }}" data-status="{{ $package->status }}">
                    <div class="card h-100 package-item">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title mb-1">{{ $package->package_name }}</h5>
                                    <p class="text-muted small mb-0">{{ $package->duration_weeks }}-week program</p>
                                </div>
                                <span class="badge bg-{{ $package->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($package->status) }}
                                </span>
                            </div>

                            <div class="price-section mb-3">
                                <h3 class="text-primary mb-0">GH₵{{ number_format($package->total_cost, 2) }}</h3>
                                <small class="text-muted">{{ $package->duration_weeks }} weeks program</small>
                            </div>

                            @if($package->description)
                                <div class="description mb-3">
                                    <p class="small text-muted">{{ Str::limit($package->description, 100) }}</p>
                                </div>
                            @endif

                            <div class="features mb-3">
                                <h6 class="mb-2">Includes:</h6>
                                <ul class="list-unstyled small">
                                    @if(isset($package->services) && $package->services->count() > 0)
                                        @foreach($package->services as $service)
                                            <li>
                                                <i class="fas fa-check text-success me-2"></i>
                                                @if(isset($service->service) && $service->service)
                                                    {{ $service->service->service_name }}
                                                @else
                                                    Service #{{ $service->service_id }}
                                                @endif
                                                @if($service->frequency_type && $service->frequency_value)
                                                    @if($service->frequency_type === 'once')
                                                        (One-time)
                                                    @elseif($service->frequency_type === 'per_week')
                                                        ({{ $service->frequency_value }}x per week)
                                                    @elseif($service->frequency_type === 'per_month')
                                                        ({{ $service->frequency_value }}x per month)
                                                    @endif
                                                @endif
                                            </li>
                                        @endforeach
                                    @else
                                        <li><i class="fas fa-info-circle text-info me-2"></i>No services added yet</li>
                                    @endif
                                </ul>
                            </div>

                            <div class="text-center mb-3">
                                <small class="text-muted">Services: <strong>{{ isset($package->services) ? $package->services->count() : 0 }}</strong></small>
                            </div>

                            <div class="d-grid gap-2">
                                <button class="btn btn-sm btn-outline-primary" onclick="editPackage('{{ $package->id }}')">
                                    <i class="fas fa-edit me-2"></i>
                                    Edit
                                </button>
                                <button class="btn btn-sm btn-{{ $package->status === 'active' ? 'outline-danger' : 'outline-success' }}" onclick="togglePackageStatus('{{ $package->id }}', '{{ $package->status }}')">
                                    <i class="fas fa-{{ $package->status === 'active' ? 'times' : 'check' }} me-2"></i>
                                    {{ $package->status === 'active' ? 'Deactivate' : 'Activate' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
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

    .description {
        background: #f8f9fa;
        padding: 0.75rem;
        border-radius: 0.25rem;
        border-left: 3px solid #3498db;
    }
</style>
@endsection

@section('js')
<script>
    // Simple alert function
    function showAlert(message, type = 'info') {
        let alertContainer = document.getElementById('alert-container');
        if (!alertContainer) {
            alertContainer = document.createElement('div');
            alertContainer.id = 'alert-container';
            alertContainer.className = 'position-fixed top-0 end-0 p-3';
            alertContainer.style.zIndex = '1050';
            document.body.appendChild(alertContainer);
        }
        
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show mb-2`;
        alert.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                <div class="flex-grow-1">${message}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        alertContainer.appendChild(alert);
        
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);
    }

    // Search packages
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

    // Filter by status
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

    // Reset filters
    function resetFilters() {
        document.getElementById("packageSearch").value = "";
        document.getElementById("statusFilter").value = "";
        const packages = document.querySelectorAll(".package-card");
        packages.forEach((pkg) => {
            pkg.style.display = "block";
        });
    }

    // Edit package
    function editPackage(packageId) {
        window.location.href = `/packages/${packageId}/edit`;
    }

    // Toggle package status
    function togglePackageStatus(packageId, currentStatus) {
        const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
        
        fetch(`/packages/${packageId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showAlert(data.message || 'Failed to update package status', 'danger');
            }
        })
        .catch(error => {
            console.error('Error updating package status:', error);
            showAlert('Error updating package status', 'danger');
        });
    }
</script>
@endsection
