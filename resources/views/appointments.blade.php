@extends('layouts.app')
@section('title', 'Appointments')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
      <div>
        <h1>Appointments</h1>
        <p class="text-muted">Manage patient appointments and scheduling</p>
      </div>
      <div>
        <a href="{{ route('appointments.add') }}" class="btn btn-primary">
          <i class="fas fa-plus me-2"></i>
          Schedule Appointment
        </a>
      </div>
    </div>

    <!-- Calendar View Toggle -->
    <div class="card mb-4">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-md-8">
            <div class="btn-group" role="group">
              <button type="button" class="btn btn-outline-primary active" onclick="showView('list')">
                <i class="fas fa-list me-2"></i>List View
              </button>
              <button type="button" class="btn btn-outline-primary" onclick="showView('calendar')">
                <i class="fas fa-calendar me-2"></i>Calendar View
              </button>
              <button type="button" class="btn btn-outline-primary" onclick="showView('timeline')">
                <i class="fas fa-clock me-2"></i>Timeline
              </button>
            </div>
          </div>
          <div class="col-md-4">
            <div class="input-group">
              <span class="input-group-text">
                <i class="fas fa-search"></i>
              </span>
              <input type="text" class="form-control" id="appointment_search" placeholder="Search appointments..." />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-primary">
            <i class="fas fa-calendar-alt"></i>
          </div>
          <div class="stat-details">
            <h3>24</h3>
            <p>Today's Appointments</p>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-success">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="stat-details">
            <h3>18</h3>
            <p>Confirmed</p>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-warning">
            <i class="fas fa-clock"></i>
          </div>
          <div class="stat-details">
            <h3>4</h3>
            <p>Pending</p>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-danger">
            <i class="fas fa-times-circle"></i>
          </div>
          <div class="stat-details">
            <h3>2</h3>
            <p>Cancelled</p>
          </div>
        </div>
      </div>
    </div>

    <!-- List View -->
    <div id="list_view" class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Appointment Schedule</h5>
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
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Patient</th>
                <th>Service</th>
                <th>Practitioner</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>2024-01-26</td>
                <td>09:00 AM</td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="patient-avatar me-2">
                      <i class="fas fa-user"></i>
                    </div>
                    <div>
                      <div class="fw-bold">John Doe</div>
                      <small class="text-muted">P001</small>
                    </div>
                  </div>
                </td>
                <td>Consultation</td>
                <td>Dr. Smith</td>
                <td><span class="badge bg-success">Confirmed</span></td>
                <td>
                  <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-primary" title="View">
                      <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-outline-warning" title="Reschedule">
                      <i class="fas fa-calendar-alt"></i>
                    </button>
                    <button type="button" class="btn btn-outline-success" title="Check-in">
                      <i class="fas fa-check"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger" title="Cancel">
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <tr>
                <td>2024-01-26</td>
                <td>10:30 AM</td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="patient-avatar me-2">
                      <i class="fas fa-user"></i>
                    </div>
                    <div>
                      <div class="fw-bold">Jane Smith</div>
                      <small class="text-muted">P002</small>
                    </div>
                  </div>
                </td>
                <td>Physiotherapy</td>
                <td>Dr. Johnson</td>
                <td><span class="badge bg-warning">Pending</span></td>
                <td>
                  <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-primary" title="View">
                      <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-outline-warning" title="Reschedule">
                      <i class="fas fa-calendar-alt"></i>
                    </button>
                    <button type="button" class="btn btn-outline-success" title="Check-in">
                      <i class="fas fa-check"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger" title="Cancel">
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <tr>
                <td>2024-01-26</td>
                <td>02:00 PM</td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="patient-avatar me-2">
                      <i class="fas fa-user"></i>
                    </div>
                    <div>
                      <div class="fw-bold">Michael Johnson</div>
                      <small class="text-muted">P003</small>
                    </div>
                  </div>
                </td>
                <td>Massage Therapy</td>
                <td>Therapist Brown</td>
                <td><span class="badge bg-success">Confirmed</span></td>
                <td>
                  <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-primary" title="View">
                      <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-outline-warning" title="Reschedule">
                      <i class="fas fa-calendar-alt"></i>
                    </button>
                    <button type="button" class="btn btn-outline-success" title="Check-in">
                      <i class="fas fa-check"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger" title="Cancel">
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <tr>
                <td>2024-01-26</td>
                <td>03:30 PM</td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="patient-avatar me-2">
                      <i class="fas fa-user"></i>
                    </div>
                    <div>
                      <div class="fw-bold">Sarah Williams</div>
                      <small class="text-muted">P004</small>
                    </div>
                  </div>
                </td>
                <td>Ultrasound</td>
                <td>Dr. Williams</td>
                <td><span class="badge bg-danger">Cancelled</span></td>
                <td>
                  <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-primary" title="View">
                      <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-outline-success" title="Reschedule">
                      <i class="fas fa-redo"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Calendar View (Hidden by default) -->
    <div id="calendar_view" class="card" style="display: none">
      <div class="card-header">
        <h5>Calendar View</h5>
      </div>
      <div class="card-body">
        <div id="appointment_calendar"></div>
      </div>
    </div>

    <!-- Timeline View (Hidden by default) -->
    <div id="timeline_view" class="card" style="display: none">
      <div class="card-header">
        <h5>Timeline View</h5>
      </div>
      <div class="card-body">
        <div class="timeline">
          <div class="timeline-item">
            <div class="timeline-time">09:00 AM</div>
            <div class="timeline-content">
              <h6>John Doe - Consultation</h6>
              <p class="mb-1">Dr. Smith</p>
              <span class="badge bg-success">Confirmed</span>
            </div>
          </div>
          <div class="timeline-item">
            <div class="timeline-time">10:30 AM</div>
            <div class="timeline-content">
              <h6>Jane Smith - Physiotherapy</h6>
              <p class="mb-1">Dr. Johnson</p>
              <span class="badge bg-warning">Pending</span>
            </div>
          </div>
          <div class="timeline-item">
            <div class="timeline-time">02:00 PM</div>
            <div class="timeline-content">
              <h6>Michael Johnson - Massage Therapy</h6>
              <p class="mb-1">Therapist Brown</p>
              <span class="badge bg-success">Confirmed</span>
            </div>
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
  // View switching
  function showView(viewType) {
    // Hide all views
    document.getElementById("list_view").style.display = "none";
    document.getElementById("calendar_view").style.display = "none";
    document.getElementById("timeline_view").style.display = "none";

    // Remove active class from all buttons
    document.querySelectorAll(".btn-group .btn").forEach((btn) => {
      btn.classList.remove("active");
    });

    // Show selected view and activate button
    switch (viewType) {
      case "list":
        document.getElementById("list_view").style.display = "block";
        document.querySelectorAll(".btn-group .btn")[0].classList.add("active");
        break;
      case "calendar":
        document.getElementById("calendar_view").style.display = "block";
        document.querySelectorAll(".btn-group .btn")[1].classList.add("active");
        initializeCalendar();
        break;
      case "timeline":
        document.getElementById("timeline_view").style.display = "block";
        document.querySelectorAll(".btn-group .btn")[2].classList.add("active");
            "2024-01-27": 3,
            "2024-01-28": 5,
          };

          if (appointments[dStr]) {
            dayElem.innerHTML += `<span class="appointment-count">${appointments[dStr]}</span>`;
          }
        },
      });
    }
  }

  // Handle appointment search
  document
    .getElementById("appointment_search")
    .addEventListener("input", function () {
      const query = this.value.toLowerCase();
      const rows = document.querySelectorAll("#list_view tbody tr");

      rows.forEach(function (row) {
        const text = row.textContent.toLowerCase();
        if (text.includes(query)) {
          row.style.display = "";
        } else {
          row.style.display = "none";
        }
      });
    });

  // Handle appointment actions
  document.querySelectorAll(".btn-group button").forEach(function (button) {
    button.addEventListener("click", function () {
      const action = this.getAttribute("title");
      const patientName =
        this.closest("tr").querySelector(".fw-bold").textContent;

      switch (action) {
        case "View":
          window.clinicSystem.showAlert(
            `Viewing appointment for ${patientName}`,
            "info",
          );
          break;
        case "Reschedule":
          window.clinicSystem.showAlert(
            `Rescheduling appointment for ${patientName}`,
            "info",
          );
          break;
        case "Check-in":
          window.clinicSystem.showAlert(
            `Checking in ${patientName}`,
            "success",
          );
          break;
        case "Cancel":
          if (confirm(`Cancel appointment for ${patientName}?`)) {
            window.clinicSystem.showAlert(
              `Appointment cancelled for ${patientName}`,
              "warning",
            );
          }
          break;
      }
    });
  });
</script>
@endsection

@section('css')
<style>
  .timeline {
    position: relative;
    padding-left: 100px;
  }

  .timeline::before {
    content: "";
    position: absolute;
    left: 80px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e2e8f0;
  }

  .timeline-item {
    position: relative;
    margin-bottom: 2rem;
  }

  .timeline-time {
    position: absolute;
    left: -80px;
    width: 70px;
    text-align: right;
    font-weight: 600;
    color: var(--primary-color);
  }

  .timeline-content {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-left: 20px;
    position: relative;
  }

  .timeline-content::before {
    content: "";
    position: absolute;
    left: -10px;
    top: 1rem;
    width: 0;
    height: 0;
    border-top: 10px solid transparent;
    border-bottom: 10px solid transparent;
    border-right: 10px solid #e2e8f0;
  }

  .appointment-count {
    display: block;
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    line-height: 20px;
    text-align: center;
    font-size: 0.7rem;
    margin-top: 2px;
  }
</style>
@endsection

