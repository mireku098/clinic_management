@extends('layouts.app')
@section('title', 'Visits & Attendance')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
      <div>
        <h1>Visits & Attendance</h1>
        <p class="text-muted">Track patient visits and attendance records</p>
      </div>
      <div>
        <a href="{{ route('visits.add') }}" class="btn btn-primary">
          <i class="fas fa-plus me-2"></i>
          Record New Visit
        </a>
      </div>
    </div>

    <!-- Today's Stats -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-success">
            <i class="fas fa-calendar-check"></i>
          </div>
          <div class="stat-details">
            <h3>{{ \App\Models\PatientVisit::whereDate('visit_date', today())->count() }}</h3>
            <p>Today's Visits</p>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-primary">
            <i class="fas fa-users"></i>
          </div>
          <div class="stat-details">
            <h3>{{ \App\Models\PatientVisit::whereDate('visit_date', today())->distinct('patient_id')->count('patient_id') }}</h3>
            <p>Unique Patients</p>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-warning">
            <i class="fas fa-clock"></i>
          </div>
          <div class="stat-details">
            <h3>{{ \App\Models\PatientVisit::whereNull('attended_by')->count() }}</h3>
            <p>Pending</p>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-info">
            <i class="fas fa-chart-line"></i>
          </div>
          <div class="stat-details">
            <h3>{{ \App\Models\PatientVisit::count() }}</h3>
            <p>Total Visits</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
      <div class="card-body">
        <form method="GET" action="{{ route('visits') }}">
          <div class="row g-3">
            <div class="col-md-3">
              <label for="patient_search" class="form-label">Search Patient</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="fas fa-search"></i>
                </span>
                <input
                  type="text"
                  class="form-control"
                  id="patient_search"
                  name="patient_search"
                  placeholder="Search by name or code..."
                  value="{{ request('patient_search') }}"
                />
              </div>
            </div>
            <div class="col-md-2">
              <label for="visit_date" class="form-label">Visit Date</label>
              <input type="date" class="form-control date-picker" id="visit_date" name="visit_date" 
                     value="{{ request('visit_date') }}" />
            </div>
            <div class="col-md-2">
              <label for="visit_type" class="form-label">Visit Type</label>
              <select class="form-select" id="visit_type" name="visit_type">
                <option value="">All Types</option>
                <option value="appointment" {{ request('visit_type') == 'appointment' ? 'selected' : '' }}>Appointment</option>
                <option value="walk-in" {{ request('visit_type') == 'walk-in' ? 'selected' : '' }}>Walk-in</option>
              </select>
            </div>
            <div class="col-md-2">
              <label for="status_filter" class="form-label">Status</label>
              <select class="form-select" id="status_filter" name="status_filter">
                <option value="">All Status</option>
                <option value="completed" {{ request('status_filter') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="pending" {{ request('status_filter') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="cancelled" {{ request('status_filter') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">&nbsp;</label>
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary flex-grow-1">
                  <i class="fas fa-filter me-2"></i>
                  Apply Filters
                </button>
                <a href="{{ route('visits') }}" class="btn btn-outline-secondary">
                  <i class="fas fa-times me-2"></i>
                  Clear
                </a>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Visits Table -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Visit Records</h5>
        <div>
          <button type="button" class="btn btn-sm btn-outline-success me-2">
            <i class="fas fa-file-excel me-1"></i>
            Export Excel
          </button>
          <button type="button" class="btn btn-sm btn-outline-danger">
            <i class="fas fa-file-pdf me-1"></i>
            Export PDF
          </button>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover data-table">
            <thead>
              <tr>
                <th>Visit ID</th>
                <th>Patient</th>
                <th>Date</th>
                <th>Time</th>
                <th>Type</th>
                <th>Services</th>
                <th>Vital Signs</th>
                <th>Attended By</th>
                <!-- <th>Status</th> -->
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @if($visits->count() > 0)
                @foreach($visits as $visit)
                <tr>
                  <td>#{{ $visit->id }}</td>
                  <td>
                    @if($visit->patient)
                      <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                          <i class="fas fa-user text-white"></i>
                        </div>
                        <div>
                          <div class="fw-medium">{{ $visit->patient->first_name }} {{ $visit->patient->last_name }}</div>
                          <small class="text-muted">{{ $visit->patient->patient_code }}</small>
                        </div>
                      </div>
                    @else
                      <span class="text-muted">Unknown Patient</span>
                    @endif
                  </td>
                  <td>{{ \Carbon\Carbon::parse($visit->visit_date)->format('M d, Y') }}</td>
                  <td>{{ \Carbon\Carbon::parse($visit->visit_time)->format('h:i A') }}</td>
                  <td>
                    <span class="badge bg-{{ $visit->visit_type == 'appointment' ? 'primary' : 'warning' }}">
                      {{ ucfirst($visit->visit_type) }}
                    </span>
                  </td>
                  <td>
                    @if($visit->services && $visit->services->count() > 0)
                      <div class="d-flex flex-wrap gap-1">
                        @foreach($visit->services as $patientService)
                          @if($patientService->service)
                          <span class="badge bg-secondary">{{ $patientService->service->service_name }}</span>
                          @endif
                        @endforeach
                      </div>
                    @else
                      <span class="text-muted">No services</span>
                    @endif
                  </td>
                  <td>
                    <div class="small">
                      @if($visit->blood_pressure)
                        <div><i class="fas fa-heart text-danger me-1"></i> {{ $visit->blood_pressure }}</div>
                      @endif
                      @if($visit->temperature)
                        <div><i class="fas fa-thermometer-half text-info me-1"></i> {{ $visit->temperature }}°C</div>
                      @endif
                      @if($visit->heart_rate)
                        <div><i class="fas fa-heartbeat text-danger me-1"></i> {{ $visit->heart_rate }}</div>
                      @endif
                    </div>
                  </td>
                  <td>
                    @if($visit->attendingUser)
                      {{ $visit->attendingUser->name }}
                    @else
                      <span class="text-muted">Not assigned</span>
                    @endif
                  </td>
                  <!-- <td>
                    <span class="badge bg-success">{{ $visit->status }}</span>
                  </td> -->
                  <td>
                    <div class="btn-group btn-group-sm">
                      <a href="{{ route('visits.show', $visit->id) }}" class="btn btn-outline-primary" title="View Details">
                        <i class="fas fa-eye"></i>
                      </a>
                      <a href="{{ route('visits.edit', $visit->id) }}" class="btn btn-outline-secondary" title="Edit">
                        <i class="fas fa-edit"></i>
                      </a>
                      @if($visit->services && $visit->services->count() > 0)
                        <a href="{{ route('service-results.patient-timeline', ['patient' => $visit->patient->id]) }}" 
                           class="btn btn-outline-info" title="Service Results">
                          <i class="fas fa-flask"></i>
                        </a>
                      @endif
                      <button type="button" onclick="deleteVisit({{ $visit->id }})" class="btn btn-outline-danger" title="Delete">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                @endforeach
              @else
                <tr>
                  <td colspan="10" class="text-center py-4">
                    <div class="text-muted">
                      <i class="fas fa-calendar-times fa-2x mb-2"></i>
                      <p>No visit records found</p>
                      <a href="{{ route('visits.add') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Record First Visit
                      </a>
                    </div>
                  </td>
                </tr>
              @endif
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <nav aria-label="Visits pagination">
          <ul class="pagination justify-content-center">
            {{ $visits->links() }}
          </ul>
        </nav>
      </div>
    </div>
  </div>
@endsection

@section('js')
@if(session('swal'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: '{{ session('swal.icon') }}',
        title: '{{ session('swal.title') }}',
        text: '{{ session('swal.text') }}',
        showConfirmButton: {{ session('swal.showConfirmButton', 'true') }}
    });
});
</script>
@endif

<script>
// Delete visit
function deleteVisit(visitId) {
    Swal.fire({
        title: 'Delete Visit?',
        text: 'Are you sure you want to delete this visit record? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Deleting...',
                text: 'Please wait while we delete the visit.',
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false
            });
            
            // Make AJAX request to delete
            fetch(`/visits/${visitId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
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
                        confirmButtonColor: '#3085d6'
                    }).then(() => {
                        // Reload page to show updated list
                        window.location.href = '{{ route('visits') }}';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to delete visit.',
                        confirmButtonColor: '#3085d6'
                    });
                }
            })
            .catch(error => {
                console.error('Delete error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An unexpected error occurred. Please try again.',
                    confirmButtonColor: '#3085d6'
                });
            });
        }
    });
}
</script>
@endsection
