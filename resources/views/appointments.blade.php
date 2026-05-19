@extends('layouts.app')
@section('title', 'Appointments')

@section('content')
<div class="container-fluid">
    <div class="page-header d-flex justify-content-between align-items-center">
      <div>
        <h1>Appointments</h1>
        <p class="text-muted">Manage scheduled visits — each appointment becomes a visit when the patient checks in</p>
      </div>
      <div>
        <a href="{{ route('appointments.add') }}" class="btn btn-primary">
          <i class="fas fa-plus me-2"></i>
          Schedule Appointment
        </a>
      </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card mb-4">
      <div class="card-body">
        <form method="GET" action="{{ route('appointments') }}" class="row align-items-center g-3">
          <div class="col-md-4">
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
          <div class="col-md-3">
            <input type="date" class="form-control date-picker" name="visit_date" value="{{ request('visit_date') }}" placeholder="Filter by date" />
          </div>
          <div class="col-md-2">
            <select class="form-select" name="status_filter">
              <option value="">All statuses</option>
              <option value="scheduled" {{ request('status_filter') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
              <option value="pending" {{ request('status_filter') === 'pending' ? 'selected' : '' }}>Checked in</option>
              <option value="completed" {{ request('status_filter') === 'completed' ? 'selected' : '' }}>Completed</option>
              <option value="cancelled" {{ request('status_filter') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
          </div>
          <div class="col-md-3">
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-search"></i></span>
              <input type="text" class="form-control" name="patient_search" id="appointment_search"
                placeholder="Search patient..." value="{{ request('patient_search') }}" />
              <button type="submit" class="btn btn-outline-primary">Filter</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="row mb-4">
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-primary"><i class="fas fa-calendar-alt"></i></div>
          <div class="stat-details">
            <h3>{{ $stats['today'] }}</h3>
            <p>Today's Appointments</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-success"><i class="fas fa-calendar-check"></i></div>
          <div class="stat-details">
            <h3>{{ $stats['scheduled'] }}</h3>
            <p>Upcoming Scheduled</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-warning"><i class="fas fa-user-check"></i></div>
          <div class="stat-details">
            <h3>{{ $stats['pending_today'] }}</h3>
            <p>Checked In Today</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-danger"><i class="fas fa-times-circle"></i></div>
          <div class="stat-details">
            <h3>{{ $stats['cancelled'] }}</h3>
            <p>Cancelled</p>
          </div>
        </div>
      </div>
    </div>

    <div id="list_view" class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Appointment Schedule</h5>
        <a href="{{ route('appointments.add') }}" class="btn btn-sm btn-primary">
          <i class="fas fa-plus me-1"></i> New
        </a>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Patient</th>
                <th>Purpose</th>
                <th>Practitioner</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($appointments as $appointment)
              <tr>
                <td>{{ $appointment->visit_date?->format('Y-m-d') }}</td>
                <td>{{ $appointment->visit_time ? \Carbon\Carbon::parse($appointment->visit_time)->format('h:i A') : '—' }}</td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="patient-avatar me-2"><i class="fas fa-user"></i></div>
                    <div>
                      <div class="fw-bold">{{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}</div>
                      <small class="text-muted">{{ $appointment->patient->patient_code }}</small>
                    </div>
                  </div>
                </td>
                <td>{{ $appointment->reason_for_visit ?: ($appointment->department_display !== 'Not assigned' ? $appointment->department_display : '—') }}</td>
                <td>{{ $appointment->practitioner_display }}</td>
                <td>
                  @php
                    $statusClass = match($appointment->status) {
                        'scheduled' => 'bg-primary',
                        'pending' => 'bg-warning text-dark',
                        'completed' => 'bg-success',
                        'cancelled' => 'bg-danger',
                        default => 'bg-secondary',
                    };
                    $statusLabel = match($appointment->status) {
                        'scheduled' => 'Scheduled',
                        'pending' => 'Checked In',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        default => ucfirst($appointment->status),
                    };
                  @endphp
                  <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                </td>
                <td>
                  <div class="btn-group btn-group-sm">
                    <a href="{{ route('visits.show', $appointment->id) }}" class="btn btn-outline-primary" title="View">
                      <i class="fas fa-eye"></i>
                    </a>
                    @if($appointment->status === 'scheduled')
                    <form action="{{ route('appointments.check-in', $appointment->id) }}" method="POST" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-outline-success" title="Check-in"
                        onclick="return confirm('Check in this patient and open the visit form?')">
                        <i class="fas fa-check"></i>
                      </button>
                    </form>
                    <form action="{{ route('appointments.cancel', $appointment->id) }}" method="POST" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-outline-danger" title="Cancel"
                        onclick="return confirm('Cancel this appointment?')">
                        <i class="fas fa-times"></i>
                      </button>
                    </form>
                    @elseif($appointment->status === 'pending')
                    <a href="{{ route('visits.edit', $appointment->id) }}" class="btn btn-outline-warning" title="Continue visit">
                      <i class="fas fa-edit"></i>
                    </a>
                    @endif
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-4">
                  No appointments found.
                  <a href="{{ route('appointments.add') }}">Schedule one</a>.
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        {{ $appointments->links() }}
      </div>
    </div>

    <div id="calendar_view" class="card" style="display: none">
      <div class="card-header"><h5>Calendar View</h5></div>
      <div class="card-body">
        <div id="appointment_calendar"></div>
      </div>
    </div>

    <div id="timeline_view" class="card" style="display: none">
      <div class="card-header"><h5>Today's Timeline</h5></div>
      <div class="card-body">
        <div class="timeline">
          @forelse($todayTimeline as $item)
          <div class="timeline-item">
            <div class="timeline-time">
              {{ $item->visit_time ? \Carbon\Carbon::parse($item->visit_time)->format('h:i A') : 'TBD' }}
            </div>
            <div class="timeline-content">
              <h6>{{ $item->patient->first_name }} {{ $item->patient->last_name }}</h6>
              <p class="mb-1">{{ $item->practitioner_display }} · {{ $item->reason_for_visit ?: 'Appointment' }}</p>
              <span class="badge bg-{{ $item->status === 'scheduled' ? 'primary' : ($item->status === 'pending' ? 'warning text-dark' : 'success') }}">
                {{ ucfirst($item->status) }}
              </span>
            </div>
          </div>
          @empty
          <p class="text-muted mb-0">No appointments scheduled for today.</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>

<div id="alert-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1050"></div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
  const appointmentCalendarCounts = @json($calendarCounts);
  let calendarInstance = null;

  function showView(viewType) {
    document.getElementById("list_view").style.display = "none";
    document.getElementById("calendar_view").style.display = "none";
    document.getElementById("timeline_view").style.display = "none";

    document.querySelectorAll(".btn-group .btn").forEach((btn) => btn.classList.remove("active"));

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
        break;
    }
  }

  function initializeCalendar() {
    const calendarEl = document.getElementById("appointment_calendar");
    if (!calendarEl || calendarInstance) return;

    calendarInstance = flatpickr(calendarEl, {
      inline: true,
      dateFormat: "Y-m-d",
      onDayCreate: function (_dObj, dStr, _fp, dayElem) {
        if (appointmentCalendarCounts[dStr]) {
          dayElem.innerHTML += `<span class="appointment-count">${appointmentCalendarCounts[dStr]}</span>`;
        }
      },
      onChange: function (_selectedDates, dateStr) {
        const url = new URL(window.location.href);
        url.searchParams.set("visit_date", dateStr);
        window.location.href = url.toString();
      },
    });
  }

  document.getElementById("appointment_search")?.addEventListener("input", function () {
    const query = this.value.toLowerCase();
    document.querySelectorAll("#list_view tbody tr").forEach(function (row) {
      row.style.display = row.textContent.toLowerCase().includes(query) ? "" : "none";
    });
  });
</script>
@endsection

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
  .timeline { position: relative; padding-left: 100px; }
  .timeline::before {
    content: ""; position: absolute; left: 80px; top: 0; bottom: 0; width: 2px; background: #e2e8f0;
  }
  .timeline-item { position: relative; margin-bottom: 2rem; }
  .timeline-time {
    position: absolute; left: -80px; width: 70px; text-align: right;
    font-weight: 600; color: var(--primary-color);
  }
  .timeline-content {
    background: white; border: 1px solid #e2e8f0; border-radius: 0.5rem;
    padding: 1rem; margin-left: 20px; position: relative;
  }
  .appointment-count {
    display: block; background: var(--primary-color); color: white; border-radius: 50%;
    width: 20px; height: 20px; line-height: 20px; text-align: center; font-size: 0.7rem; margin-top: 2px;
  }
</style>
@endsection
