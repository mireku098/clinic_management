@extends('layouts.app')

@section('title', 'Service Results')

@section('content')
<div class="container-fluid">
    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="mb-0">Service Results</h2>
        <p class="text-muted mb-0">Manage test results and documentation</p>
      </div>
      <div>
        <a href="{{ route('service-results.create') }}" class="btn btn-primary">
          <i class="fas fa-plus me-2"></i>Add Result
        </a>
      </div>
    </div>

    <!-- Results Stats -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-primary">
            <i class="fas fa-clipboard-list"></i>
          </div>
          <div class="stat-details">
            <h3>{{ $results->total() }}</h3>
            <p>Total Results</p>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-success">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="stat-details">
            <h3>{{ $results->where('status', 'approved')->count() }}</h3>
            <p>Approved</p>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-warning">
            <i class="fas fa-clock"></i>
          </div>
          <div class="stat-details">
            <h3>{{ $results->where('status', 'pending_approval')->count() }}</h3>
            <p>Pending Approval</p>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-info">
            <i class="fas fa-edit"></i>
          </div>
          <div class="stat-details">
            <h3>{{ $results->whereIn('status', ['draft', 'rejected'])->count() }}</h3>
            <p>Draft/Rejected</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3">
            <label for="search_results" class="form-label">Search Results</label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="fas fa-search"></i>
              </span>
              <input type="text" class="form-control" id="search_results" placeholder="Search by patient or service..." />
            </div>
          </div>
          <div class="col-md-2">
            <label for="patient_filter" class="form-label">Patient</label>
            <select class="form-select" id="patient_filter">
              <option value="">All Patients</option>
              @foreach(App\Models\Patient::orderBy('first_name')->get() as $patient)
                <option value="{{ $patient->id }}">{{ $patient->first_name }} {{ $patient->last_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <label for="service_filter" class="form-label">Service</label>
            <select class="form-select" id="service_filter">
              <option value="">All Services</option>
              @foreach(App\Models\Service::where('status', 'active')->orderBy('service_name')->get() as $service)
                <option value="{{ $service->id }}">{{ $service->service_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <label for="package_filter" class="form-label">Package</label>
            <select class="form-select" id="package_filter">
              <option value="">All Packages</option>
              @foreach(App\Models\Package::where('status', 'active')->orderBy('package_name')->get() as $package)
                <option value="{{ $package->id }}">{{ $package->package_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <label for="status_filter" class="form-label">Status</label>
            <select class="form-select" id="status_filter">
              <option value="">All Status</option>
              <option value="draft">Draft</option>
              <option value="pending_approval">Pending Approval</option>
              <option value="approved">Approved</option>
              <option value="rejected">Rejected</option>
            </select>
          </div>
          <div class="col-md-2">
            <label for="result_type_filter" class="form-label">Result Type</label>
            <select class="form-select" id="result_type_filter">
              <option value="">All Types</option>
              <option value="text">Text</option>
              <option value="numeric">Numeric</option>
              <option value="file">File</option>
            </select>
          </div>
          <div class="col-md-1">
            <label class="form-label">&nbsp;</label>
            <button type="button" class="btn btn-outline-primary w-100">
              <i class="fas fa-filter me-2"></i>Filter
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Results Grid -->
    <div id="results-grid-container">
      @include('service-results.partials.results-grid')
    </div>
    
    <!-- Pagination -->
    @if ($results->hasPages())
      <div id="pagination-container" class="d-flex justify-content-center mt-4">
        {{ $results->links() }}
      </div>
    @endif
  </div>
</div>

<!-- Alert Container -->
<div id="alert-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1050"></div>
@endsection

@section('js')
<script>
// Search and filter functionality
let searchTimeout;

function performSearch() {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    // Get elements with null checks
    const searchInput = document.getElementById('search_results');
    const patientFilter = document.getElementById('patient_filter');
    const serviceFilter = document.getElementById('service_filter');
    const statusFilter = document.getElementById('status_filter');
    const resultTypeFilter = document.getElementById('result_type_filter');
    const gridContainer = document.getElementById('results-grid-container');
    
    // Check if grid container exists
    if (!gridContainer) {
      console.error('Results grid container not found');
      return;
    }
    
    // Build URL with query parameters
    const params = new URLSearchParams();
    if (searchInput && searchInput.value) params.append('search', searchInput.value);
    if (patientFilter && patientFilter.value) params.append('patient_id', patientFilter.value);
    if (serviceFilter && serviceFilter.value) params.append('service_id', serviceFilter.value);
    if (statusFilter && statusFilter.value) params.append('status', statusFilter.value);
    if (resultTypeFilter && resultTypeFilter.value) params.append('result_type', resultTypeFilter.value);
    
    // Show loading state
    gridContainer.innerHTML = `
      <div class="col-12 text-center py-5">
        <i class="fas fa-spinner fa-spin fa-2x text-muted mb-3"></i>
        <p class="text-muted">Loading results...</p>
      </div>
    `;
    
    // Make AJAX request
    fetch(`{{ route("service-results.index") }}?${params.toString()}`, {
      method: 'GET',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    })
    .then(response => response.json())
    .then(data => {
      // Update results grid
      gridContainer.innerHTML = data.html;
      
      // Update pagination
      const paginationContainer = document.getElementById('pagination-container');
      if (paginationContainer) {
        if (data.pagination) {
          paginationContainer.innerHTML = data.pagination;
        } else {
          paginationContainer.innerHTML = '';
        }
      }
    })
    .catch(error => {
      console.error('Error:', error);
      gridContainer.innerHTML = `
        <div class="col-12 text-center py-5">
          <i class="fas fa-exclamation-triangle fa-2x text-danger mb-3"></i>
          <p class="text-danger">Error loading results. Please try again.</p>
        </div>
      `;
    });
  }, 300); // Debounce search
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
  // Search input
  const searchInput = document.getElementById('search_results');
  if (searchInput) {
    searchInput.addEventListener('input', performSearch);
  }
  
  // Filter dropdowns
  const patientFilter = document.getElementById('patient_filter');
  if (patientFilter) {
    patientFilter.addEventListener('change', performSearch);
  }
  
  const serviceFilter = document.getElementById('service_filter');
  if (serviceFilter) {
    serviceFilter.addEventListener('change', performSearch);
  }
  
  const statusFilter = document.getElementById('status_filter');
  if (statusFilter) {
    statusFilter.addEventListener('change', performSearch);
  }
  
  const resultTypeFilter = document.getElementById('result_type_filter');
  if (resultTypeFilter) {
    resultTypeFilter.addEventListener('change', performSearch);
  }
  
  // Apply filters button
  const filterBtn = document.querySelector('button:contains("Filter")');
  if (filterBtn) {
    filterBtn.addEventListener('click', performSearch);
  }
});
</script>
@endsection

@section('css')
<style>
  .result-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .result-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }

  .status-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
  }

  .result-type-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
  }

  .result-type-text { background-color: #e3f2fd; color: #1976d2; }
  .result-type-numeric { background-color: #e8f5e8; color: #2e7d32; }
  .result-type-file { background-color: #fff3e0; color: #f57c00; }
</style>
@endsection
