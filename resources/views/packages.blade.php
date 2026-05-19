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
        <div class="btn-group">
            <div class="btn-group me-2" role="group">
                <button type="button" class="btn btn-outline-secondary {{ request('view') !== 'trash' ? 'active' : '' }}" onclick="window.location.href='{{ route('packages') }}'">
                    <i class="fas fa-list me-2"></i>Active
                </button>
                <button type="button" class="btn btn-outline-secondary {{ request('view') === 'trash' ? 'active' : '' }}" onclick="window.location.href='{{ route('packages', ['view' => 'trash']) }}'">
                    <i class="fas fa-trash-alt me-2"></i>Deleted
                </button>
            </div>
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
                    <h3 id="stat-total">{{ $stats['total'] }}</h3>
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
                    <h3 id="stat-active">{{ $stats['active'] }}</h3>
                    <p>Active Packages</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-danger">
                    <i class="fas fa-trash-alt"></i>
                </div>
                <div class="stat-details">
                    <h3 id="stat-deleted">{{ $stats['deleted'] }}</h3>
                    <p>Deleted Packages</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-details">
                    <h3 id="stat-weeks">{{ $stats['total_weeks'] }}</h3>
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
                                @if($package->trashed())
                                    <button class="btn btn-sm btn-outline-success restore-btn-pkg" 
                                            data-url="{{ route('packages.restore', $package->id) }}">
                                        <i class="fas fa-undo me-2"></i>
                                        Restore
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger force-delete-btn-pkg" 
                                            data-url="{{ route('packages.force-delete', $package->id) }}">
                                        <i class="fas fa-times me-2"></i>
                                        Permanently Delete
                                    </button>
                                @else
                                    <button class="btn btn-sm btn-outline-primary" onclick="editPackage('{{ $package->id }}')">
                                        <i class="fas fa-edit me-2"></i>
                                        Edit
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger delete-btn-pkg" 
                                            data-url="{{ route('packages.destroy', $package->id) }}">
                                        <i class="fas fa-trash me-2"></i>
                                        Delete
                                    </button>
                                @endif
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

    // Global Event Delegation for packages
    document.addEventListener('click', function(e) {
        // Restore button
        const restoreBtn = e.target.closest('.restore-btn-pkg');
        if (restoreBtn) {
            e.preventDefault();
            const url = restoreBtn.getAttribute('data-url');
            if (url) restorePackage(url);
            return;
        }

        // Force delete button
        const forceDeleteBtn = e.target.closest('.force-delete-btn-pkg');
        if (forceDeleteBtn) {
            e.preventDefault();
            const url = forceDeleteBtn.getAttribute('data-url');
            if (url) forceDeletePackage(url);
            return;
        }

        // Delete button
        const deleteBtn = e.target.closest('.delete-btn-pkg');
        if (deleteBtn) {
            e.preventDefault();
            const url = deleteBtn.getAttribute('data-url');
            if (url) deletePackage(url);
            return;
        }
    });

    // Edit package
    function editPackage(packageId) {
        window.location.href = `{{ url('packages') }}/${packageId}/edit`;
    }

    // Soft delete package
    function deletePackage(url) {
        Swal.fire({
            title: 'Delete Package?',
            text: 'Are you sure you want to delete this package?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = '{{ route("packages") }}';
                        });
                    } else {
                        Swal.fire('Error!', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error!', 'An error occurred while deleting the package.', 'error');
                });
            }
        });
    }

    // Restore package
    function restorePackage(url) {
        Swal.fire({
            title: 'Restore Package?',
            text: 'This package will be moved back to the active list.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, restore it!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Restored!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = '{{ route("packages") }}';
                        });
                    } else {
                        Swal.fire('Error!', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error!', 'An error occurred while restoring the package.', 'error');
                });
            }
        });
    }

    // Permanently delete package
    function forceDeletePackage(url) {
        Swal.fire({
            title: 'Permanently Delete?',
            text: 'This action cannot be undone! This package will be removed forever.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete forever!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted Forever!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = '{{ route("packages") }}';
                        });
                    } else {
                        Swal.fire('Error!', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error!', 'An error occurred while permanently deleting the package.', 'error');
                });
            }
        });
    }

    // Toggle package status (deprecated, but keeping signature for now or removing if not used elsewhere)
    function togglePackageStatus(packageId, currentStatus) {
        // Redirect to delete for consistency if called
        deletePackage(packageId);
    }
</script>
@endsection
