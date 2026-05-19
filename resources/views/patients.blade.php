@extends('layouts.app')
@section('title', 'Patients')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
      <div>
        <h1>Patients</h1>
        <p class="text-muted">Manage patient records and information</p>
      </div>
      <div>
        <a href="{{ route('patients.add') }}" class="btn btn-primary">
          <i class="fas fa-user-plus me-2"></i>
          Add New Patient
        </a>
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
      <div class="card-body">
        <form action="{{ route('patients') }}" method="GET">
          <div class="row g-3">
            <div class="col-md-4">
              <label for="patient_search" class="form-label">Search Patients</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="fas fa-search"></i>
                </span>
                <input
                  type="text"
                  name="search"
                  class="form-control"
                  id="patient_search"
                  value="{{ request('search') }}"
                  placeholder="Search by name, code, or phone..."
                />
              </div>
            </div>
            <div class="col-md-2">
              <label for="gender_filter" class="form-label">Gender</label>
              <select class="form-select" name="gender" id="gender_filter">
                <option value="">All</option>
                <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Female</option>
              </select>
            </div>
            <div class="col-md-2">
              <label for="age_range" class="form-label">Age Range</label>
              <select class="form-select" name="age_range" id="age_range">
                <option value="">All Ages</option>
                <option value="0-18" {{ request('age_range') === '0-18' ? 'selected' : '' }}>0-18</option>
                <option value="19-35" {{ request('age_range') === '19-35' ? 'selected' : '' }}>19-35</option>
                <option value="36-50" {{ request('age_range') === '36-50' ? 'selected' : '' }}>36-50</option>
                <option value="51+" {{ request('age_range') === '51+' ? 'selected' : '' }}>51+</option>
              </select>
            </div>
            <div class="col-md-2">
              <label for="sort_by" class="form-label">Sort By</label>
              <select class="form-select" name="sort" id="sort_by">
                <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Latest</option>
                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest</option>
                <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                <option value="age_asc" {{ request('sort') === 'age_asc' ? 'selected' : '' }}>Age (Low-High)</option>
                <option value="age_desc" {{ request('sort') === 'age_desc' ? 'selected' : '' }}>Age (High-Low)</option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">&nbsp;</label>
              <button type="submit" class="btn btn-outline-primary w-100">
                <i class="fas fa-filter me-2"></i>
                Apply Filters
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Patients Table -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Patient Records</h5>
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
          <table class="table table-hover" id="patientsTable">
            <thead>
              <tr>
                <th>Patient Code</th>
                <th>Name</th>
                <th>Gender</th>
                <th>Age</th>
                <th>Phone</th>
                <th>Registration Date</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($patients as $patient)
                <tr>
                  <td><span class="badge bg-primary">{{ $patient->patient_code }}</span></td>
                  <td>
                    <div class="d-flex align-items-center" style="cursor: pointer" onclick="viewPatient('{{ $patient->patient_code }}')">
                      <div class="me-2">
                        @if($patient->photo_path)
                          <img src="{{ asset('storage-public/' . $patient->photo_path) }}" class="rounded-circle" style="width: 48px; height: 48px; object-fit: cover;">
                        @else
                          <i class="fas fa-user"></i>
                        @endif
                      </div>
                      <div>
                        <div class="fw-bold">{{ $patient->first_name }} {{ $patient->last_name }}</div>
                        <small class="text-muted">{{ $patient->email ?? '' }}</small>
                      </div>
                    </div>
                  </td>
                  <td><i class="fas fa-{{ $patient->gender === 'male' ? 'mars text-primary' : 'venus text-danger' }}"></i> {{ ucfirst($patient->gender) }}</td>
                  <td>{{ $patient->age ?? Carbon\Carbon::parse($patient->date_of_birth)->age }}</td>
                  <td>{{ $patient->phone }}</td>
                  <td>{{ \Carbon\Carbon::parse($patient->registered_at ?? $patient->created_at)->format('M j, Y') }}</td>
                  <td><span class="badge bg-success">Active</span></td>
                  <td>
                    <div class="btn-group btn-group-sm">
                      <button
                        type="button"
                        class="btn btn-outline-primary"
                        title="View Clinical Records"
                        onclick="openPatientContext('{{ $patient->patient_code }}')"
                      >
                        <i class="fas fa-user-injured"></i>
                      </button>
                      <button
                        type="button"
                        class="btn btn-outline-warning"
                        title="Edit"
                        onclick="editPatient('{{ $patient->patient_code }}')"
                      >
                        <i class="fas fa-edit"></i>
                      </button>
                      <button
                        type="button"
                        class="btn btn-outline-success"
                        title="Add Visit"
                        onclick="addVisit('{{ $patient->patient_code }}')"
                      >
                        <i class="fas fa-plus"></i>
                      </button>
                      <button
                        type="button"
                        class="btn btn-outline-info"
                        title="Billing"
                        onclick="viewBilling('{{ $patient->patient_code }}')"
                      >
                        <i class="fas fa-file-invoice"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="text-center">
                    <div class="empty-state">
                      <div class="empty-state-icon">
                        <i class="fas fa-users"></i>
                      </div>
                      <h5>No Patients Found</h5>
                      <p>No patients have been registered yet.</p>
                      <a href="{{ route('patients.add') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus me-2"></i>Add First Patient
                      </a>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4 d-flex justify-content-center">
            {{ $patients->links() }}
        </div>
      </div>
    </div>
  </div>

<!-- View Patient Modal -->
<div class="modal fade" id="viewPatientModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Patient Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-4 text-center">
            <div class="patient-avatar-large mb-3" id="modalPatientAvatar">
              <i class="fas fa-user" style="font-size: 4rem"></i>
            </div>
            <h5 id="modalPatientName">John Doe</h5>
            <p class="text-muted" id="modalPatientCode">P001</p>
            <span class="badge bg-success" id="modalPatientStatus">Active</span>
          </div>
          <div class="col-md-8">
            <h6>Personal Information</h6>
            <table class="table table-sm">
              <tr>
                <td><strong>Age:</strong></td>
                <td id="modalPatientAge">35</td>
                <td><strong>Gender:</strong></td>
                <td id="modalPatientGender">Male</td>
              </tr>
              <tr>
                <td><strong>Phone:</strong></td>
                <td id="modalPatientPhone">08012345678</td>
                <td><strong>Email:</strong></td>
                <td id="modalPatientEmail">johndoe@email.com</td>
              </tr>
              <tr>
                <td><strong>Registration:</strong></td>
                <td id="modalPatientRegDate">2024-01-15</td>
                <td><strong>Last Visit:</strong></td>
                <td id="modalPatientLastVisit">2024-01-20</td>
              </tr>
            </table>
          </div>
        </div>

        <hr />

        <h6>Recent Visits</h6>
        <div class="timeline">
          <div class="timeline-item">
            <div class="timeline-marker bg-success"></div>
            <div class="timeline-content">
              <strong>Regular Checkup</strong> - 2024-01-20<br />
              <small class="text-muted">BP: 120/80, Weight: 70kg</small>
            </div>
          </div>
          <div class="timeline-item">
            <div class="timeline-marker bg-primary"></div>
            <div class="timeline-content">
              <strong>Initial Consultation</strong> - 2024-01-15<br />
              <small class="text-muted">Patient registration and first visit</small>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          Close
        </button>
        <button type="button" class="btn btn-warning" onclick="editCurrentPatient()">
          <i class="fas fa-edit me-2"></i>Edit Patient
        </button>
        <button type="button" class="btn btn-success" onclick="addVisitForCurrentPatient()">
          <i class="fas fa-plus me-2"></i>Add Visit
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Alert Container -->
<div id="alert_container" class="position-fixed top-0 end-0 p-3" style="z-index: 1050"></div>
@endsection

@section('js')
<script>
  let currentPatientId = null;

  // Open patient context
  function openPatientContext(patientCode) {
    window.location.href = `{{ route('patients.context') }}?patient=${patientCode}`;
  }

  // Edit patient
  function editPatient(patientCode) {
    window.location.href = `{{ route('patients.edit') }}?code=${patientCode}`;
  }

  // Add visit for patient
  function addVisit(patientCode) {
    window.location.href = `{{ route('visits.add') }}?patient=${patientCode}`;
  }

  // View billing for patient
  function viewBilling(patientCode) {
    window.location.href = `{{ route('billing.user') }}?patient=${patientCode}`;
  }

  // View patient details
  function viewPatient(patientCode) {
    currentPatientId = patientCode;
    
    // Find patient in database data
    const patientRow = document.querySelector(`tr:has([onclick="viewPatient('${patientCode}')"])`);
    if (!patientRow) return;
    
    const cells = patientRow.querySelectorAll('td');
    const patientCodeElement = cells[0].textContent.trim();
    const nameElement = cells[1].querySelector('.fw-bold');
    const emailElement = cells[1].querySelector('.text-muted');
    const genderElement = cells[2];
    const ageElement = cells[3];
    const phoneElement = cells[4];
    const regDateElement = cells[5];
    const statusElement = cells[6].querySelector('.badge');
    
    // Get patient photo from the table row
    const patientImage = cells[1].querySelector('img');
    const modalAvatar = document.getElementById("modalPatientAvatar");
    
    if (patientImage) {
      modalAvatar.innerHTML = `<img src="${patientImage.src}" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">`;
    } else {
      modalAvatar.innerHTML = '<i class="fas fa-user" style="font-size: 4rem"></i>';
    }
    
    if (nameElement) {
      document.getElementById("modalPatientName").textContent = nameElement.textContent.trim();
    }
    document.getElementById("modalPatientCode").textContent = patientCodeElement;
    document.getElementById("modalPatientAge").textContent = ageElement.textContent.trim();
    document.getElementById("modalPatientGender").textContent = genderElement.textContent.trim();
    document.getElementById("modalPatientPhone").textContent = phoneElement.textContent.trim();
    document.getElementById("modalPatientEmail").textContent = emailElement ? emailElement.textContent.trim() : '';
    document.getElementById("modalPatientRegDate").textContent = regDateElement.textContent.trim();
    document.getElementById("modalPatientLastVisit").textContent = 'No visits yet';
    
    const statusBadge = document.getElementById("modalPatientStatus");
    statusBadge.textContent = statusElement.textContent.trim();
    statusBadge.className = `badge ${statusElement.className}`;
    
    const modal = new bootstrap.Modal(document.getElementById('viewPatientModal'));
    modal.show();
  }

  // Edit current patient from modal
  function editCurrentPatient() {
    if (currentPatientId) {
      editPatient(currentPatientId);
    }
  }

  // Add visit for current patient from modal
  function addVisitForCurrentPatient() {
    if (currentPatientId) {
      addVisit(currentPatientId);
    }
  }

  // Export functionality
  document.addEventListener("DOMContentLoaded", function () {
    const exportButtons = document.querySelectorAll("button");
    exportButtons.forEach((btn) => {
      if (btn.textContent.includes("Export Excel")) {
        btn.addEventListener("click", function () {
          Swal.fire({
            title: "Exporting to Excel...",
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            },
          });

          setTimeout(() => {
            Swal.close();
            Swal.fire({
              icon: "success",
              title: "Export Complete",
              text: "Patient data exported to Excel successfully",
              timer: 2000,
              showConfirmButton: false,
            });
          }, 1500);
        });
      }

      if (btn.textContent.includes("Export PDF")) {
        btn.addEventListener("click", function () {
          Swal.fire({
            title: "Exporting to PDF...",
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            },
          });

          setTimeout(() => {
            Swal.close();
            Swal.fire({
              icon: "success",
              title: "Export Complete",
              text: "Patient data exported to PDF successfully",
              timer: 2000,
              showConfirmButton: false,
            });
          }, 1500);
        });
      }
    });
  });
</script>
@endsection

<style>
  .timeline {
    position: relative;
    padding-left: 30px;
  }

  .timeline::before {
    content: "";
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e2e8f0;
  }

  .timeline-item {
    position: relative;
    margin-bottom: 1.5rem;
  }

  .timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 0 0 2px #e2e8f0;
  }

  .timeline-content {
    background: #f8f9fa;
    padding: 0.75rem;
    border-radius: 0.5rem;
  }

  .patient-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
  }

  .patient-avatar-large {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
  }
</style>
