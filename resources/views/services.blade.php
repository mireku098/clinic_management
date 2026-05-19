@extends('layouts.app')
@section('title', 'Services')

@section('content')
<div class="container-fluid">
    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="mb-0">Services</h2>
        <p class="text-muted mb-0">Manage clinic services and pricing</p>
      </div>
      <div class="btn-group">
        <div class="btn-group me-2" role="group">
          <button type="button" class="btn btn-outline-secondary {{ request('view') !== 'trash' ? 'active' : '' }}" onclick="window.location.href='{{ route('services') }}'">
            <i class="fas fa-list me-2"></i>Active
          </button>
          <button type="button" class="btn btn-outline-secondary {{ request('view') === 'trash' ? 'active' : '' }}" onclick="window.location.href='{{ route('services', ['view' => 'trash']) }}'">
            <i class="fas fa-trash-alt me-2"></i>Deleted
          </button>
        </div>
        <a href="{{ route('services.add') }}" class="btn btn-primary">
          <i class="fas fa-plus me-2"></i>Add Service
        </a>
      </div>
    </div>

    <!-- Services Stats -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-primary">
            <i class="fas fa-stethoscope"></i>
          </div>
          <div class="stat-details">
            <h3 id="stat-total">{{ $stats['total'] }}</h3>
            <p>Total Services</p>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-success">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="stat-details">
            <h3 id="stat-active">{{ $stats['active'] }}</h3>
            <p>Active Services</p>
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
            <p>Deleted Services</p>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-info">
            <i class="fas fa-money-bill-wave"></i>
          </div>
          <div class="stat-details">
            <h3 id="stat-avg-price">GH₵{{ number_format($stats['avg_price'], 0) }}</h3>
            <p>Avg. Price</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label for="service_search" class="form-label">Search Services</label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="fas fa-search"></i>
              </span>
              <input type="text" class="form-control" id="service_search" placeholder="Search by name or description..." />
            </div>
          </div>
          <div class="col-md-2">
            <label for="category_filter" class="form-label">Category</label>
            <select class="form-select" id="category_filter">
              <option value="">All Categories</option>
              <option value="consultation">Consultation</option>
              <option value="therapy">Therapy</option>
              <option value="diagnostic">Diagnostic</option>
              <option value="treatment">Treatment</option>
            </select>
          </div>
          <div class="col-md-2">
            <label for="status_filter" class="form-label">Status</label>
            <select class="form-select" id="status_filter">
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
          <div class="col-md-2">
            <label for="price_range" class="form-label">Price Range</label>
            <select class="form-select" id="price_range">
              <option value="">All Prices</option>
              <option value="0-5000">GH₵0 - GH₵5,000</option>
              <option value="5000-10000">GH₵5,000 - GH₵10,000</option>
              <option value="10000+">GH₵10,000+</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">&nbsp;</label>
            <button type="button" class="btn btn-outline-primary w-100">
              <i class="fas fa-filter me-2"></i>
              Apply Filters
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Services Grid -->
    <div id="service-grid-container">
      @include('services.partials.service-grid')
    </div>
    
    <!-- Pagination -->
    @if ($services->hasPages())
      <div id="pagination-container" class="d-flex justify-content-center mt-4">
        {{ $services->links() }}
      </div>
    @endif
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
    const searchInput = document.getElementById('service_search');
    const categoryFilter = document.getElementById('category_filter');
    const statusFilter = document.getElementById('status_filter');
    const priceRangeFilter = document.getElementById('price_range');
    const gridContainer = document.getElementById('service-grid-container');
    
    // Check if grid container exists
    if (!gridContainer) {
      console.error('Service grid container not found');
      return;
    }
    
    const formData = new FormData();
    
    // Get search term
    if (searchInput && searchInput.value) {
      formData.append('search', searchInput.value);
    }
    
    // Get category filter
    if (categoryFilter && categoryFilter.value) {
      formData.append('category', categoryFilter.value);
    }
    
    // Get status filter
    if (statusFilter && statusFilter.value) {
      formData.append('status', statusFilter.value);
    }
    
    // Get price range filter
    if (priceRangeFilter && priceRangeFilter.value) {
      formData.append('price_range', priceRangeFilter.value);
    }
    
    // Show loading state
    gridContainer.innerHTML = `
      <div class="col-12 text-center py-5">
        <i class="fas fa-spinner fa-spin fa-2x text-muted mb-3"></i>
        <p class="text-muted">Loading services...</p>
      </div>
    `;
    
    // Build URL with query parameters
    const params = new URLSearchParams();
    if (searchInput && searchInput.value) params.append('search', searchInput.value);
    if (categoryFilter && categoryFilter.value) params.append('category', categoryFilter.value);
    if (statusFilter && statusFilter.value) params.append('status', statusFilter.value);
    if (priceRangeFilter && priceRangeFilter.value) params.append('price_range', priceRangeFilter.value);
    
    // Add view parameter if it exists
    const view = new URLSearchParams(window.location.search).get('view');
    if (view) params.append('view', view);
    
    // Make AJAX request
    fetch(`{{ route("services") }}?${params.toString()}`, {
      method: 'GET',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    })
    .then(response => response.json())
    .then(data => {
      // Update service grid
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
      
      // Update stats
      updateServiceStats(data.stats);
      
      // Re-attach event listeners for dynamic content
      if (typeof attachActionListeners === 'function') {
        attachActionListeners();
      }
    })
    .catch(error => {
      console.error('Error:', error);
      gridContainer.innerHTML = `
        <div class="col-12 text-center py-5">
          <i class="fas fa-exclamation-triangle fa-2x text-danger mb-3"></i>
          <p class="text-danger">Error loading services. Please try again.</p>
        </div>
      `;
    });
  }, 300); // Debounce search
}

function updateServiceStats(stats) {
  if (!stats) return;
  
  const totalElement = document.getElementById('stat-total');
  const activeElement = document.getElementById('stat-active');
  const deletedElement = document.getElementById('stat-deleted');
  const avgPriceElement = document.getElementById('stat-avg-price');
  
  if (totalElement) totalElement.textContent = stats.total;
  if (activeElement) activeElement.textContent = stats.active;
  if (deletedElement) deletedElement.textContent = stats.deleted;
  if (avgPriceElement) avgPriceElement.textContent = 'GH₵' + parseFloat(stats.avg_price).toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0});
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
  // Global Event Delegation for buttons
  document.addEventListener('click', function(e) {
    // Restore button
    const restoreBtn = e.target.closest('.restore-btn');
    if (restoreBtn) {
      e.preventDefault();
      const url = restoreBtn.getAttribute('data-url');
      if (url) handleRestore(url);
      return;
    }

    // Force delete button
    const forceDeleteBtn = e.target.closest('.force-delete-btn');
    if (forceDeleteBtn) {
      e.preventDefault();
      const url = forceDeleteBtn.getAttribute('data-url');
      if (url) handleForceDelete(url);
      return;
    }
  });

  // Soft delete form delegation
  document.addEventListener('submit', function(e) {
    if (e.target.classList.contains('soft-delete-form')) {
      e.preventDefault();
      
      Swal.fire({
        title: 'Delete Service?',
        text: 'Are you sure you want to delete this service?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          handleSoftDelete(e.target);
        }
      });
    }
  });

  // Search input
  const searchInput = document.getElementById('service_search');
  if (searchInput) {
    searchInput.addEventListener('input', performSearch);
  }
  
  // Filter dropdowns
  const categoryFilter = document.getElementById('category_filter');
  if (categoryFilter) {
    categoryFilter.addEventListener('change', performSearch);
  }
  
  const statusFilter = document.getElementById('status_filter');
  if (statusFilter) {
    statusFilter.addEventListener('change', performSearch);
  }
  
  // Price range filter
  const priceRangeFilter = document.getElementById('price_range');
  if (priceRangeFilter) {
    priceRangeFilter.addEventListener('change', performSearch);
  }
  
  // Apply filters button
  const applyFiltersBtn = document.querySelector('button:contains("Apply Filters")');
  if (applyFiltersBtn) {
    applyFiltersBtn.addEventListener('click', performSearch);
  }
});

function attachActionListeners() {
  // This function is now empty as we use event delegation
}

function handleSoftDelete(form) {
  const submitBtn = form.querySelector('button[type="submit"]');
  const originalText = submitBtn.innerHTML;
  
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
  
  fetch(form.action, {
    method: 'POST',
    body: new FormData(form),
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
            window.location.href = '{{ route("services") }}';
          });
        } else {
      Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: data.message
      });
    }
  })
  .catch(error => {
    console.error('Error:', error);
    Swal.fire({
      icon: 'error',
      title: 'Error!',
      text: 'Network error. Please try again.'
    });
  })
  .finally(() => {
    submitBtn.disabled = false;
    submitBtn.innerHTML = originalText;
  });
}

function handleRestore(url) {
  Swal.fire({
    title: 'Restore Service?',
    text: 'This service will be moved back to the active list.',
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
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
            window.location.href = '{{ route("services") }}';
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: data.message
      });
        }
      })
      .catch(error => {
        console.error('Error:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: 'Network error. Please try again.'
        });
      });
    }
  });
}

function handleForceDelete(url) {
  Swal.fire({
    title: 'Permanently Delete?',
    text: 'This action cannot be undone! This service will be removed forever.',
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
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
            window.location.href = '{{ route("services") }}';
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: data.message
          });
        }
      })
      .catch(error => {
        console.error('Error:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: 'Network error. Please try again.'
        });
      });
    }
  });
}

function handleStatusToggle(form) {
  const formData = new FormData(form);
  const submitBtn = form.querySelector('button[type="submit"]');
  const originalText = submitBtn.innerHTML;
  
  // Show loading state
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
  
  fetch(form.action, {
    method: 'POST',
    body: formData,
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Show success message
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: data.message,
        timer: 1500,
        showConfirmButton: false
      }).then(() => {
        // Refresh the service grid to show updated status
        performSearch();
      });
    } else {
      // Show error message
      Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: data.message || 'Something went wrong. Please try again.',
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
}
</script>
@endsection

@section('css')
<style>
  .service-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
  }

  .service-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
  }
</style>
