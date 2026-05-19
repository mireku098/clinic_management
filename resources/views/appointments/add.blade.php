@extends('layouts.app')

@section('title', 'Schedule Appointment')

@section('content')
<div class="container-fluid">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1>Schedule Appointment</h1>
            <p class="text-muted">Creates a scheduled visit — check in on the day to record vitals and services</p>
        </div>
        <div>
            <a href="{{ route('appointments') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Appointments
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form id="appointment_form" method="POST" action="{{ route('appointments.store') }}">
                @csrf
                <input type="hidden" name="visit_type" value="appointment" />
                <input type="hidden" id="patient_id" name="patient_id" value="{{ old('patient_id') }}" />

                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-user me-2"></i>Patient Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="patient_search" class="form-label">Search Patient <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" id="patient_search"
                                        placeholder="Search by name, code, or phone..." autocomplete="off" />
                                </div>
                                <div id="patient_results"></div>
                                @error('patient_id')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="patient_name" class="form-label">Selected Patient</label>
                                <input type="text" class="form-control" id="patient_name" readonly
                                    value="{{ old('patient_name') }}" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-calendar me-2"></i>Appointment Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="visit_date" class="form-label">Appointment Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control date-picker" id="visit_date" name="visit_date"
                                    value="{{ old('visit_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required />
                                @error('visit_date')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="visit_time" class="form-label">Appointment Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="visit_time" name="visit_time"
                                    value="{{ old('visit_time', '09:00') }}" required />
                                @error('visit_time')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="department" class="form-label">Department</label>
                                <select class="form-select" id="department" name="department[]" multiple>
                                    @foreach(config('clinic.departments', []) as $value => $label)
                                    <option value="{{ $value }}" {{ collect(old('department', []))->contains($value) ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label for="practitioner" class="form-label">Practitioner(s) <span class="text-danger">*</span></label>
                                <select class="form-select" id="practitioner" name="practitioner[]" multiple required>
                                    @foreach(config('clinic.practitioners', []) as $value => $label)
                                    <option value="{{ $value }}" {{ collect(old('practitioner', []))->contains($value) ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('practitioner')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label for="reason_for_visit" class="form-label">Purpose / Service (optional)</label>
                                <input type="text" class="form-control" id="reason_for_visit" name="reason_for_visit"
                                    value="{{ old('reason_for_visit') }}"
                                    placeholder="e.g. Consultation, Physiotherapy follow-up..." list="service_suggestions" />
                                <datalist id="service_suggestions">
                                    @foreach($services as $service)
                                    <option value="{{ $service->name }}">
                                    @endforeach
                                </datalist>
                            </div>
                            <div class="col-12">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"
                                    placeholder="Special requirements or internal notes...">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="form-actions d-flex justify-content-between flex-wrap gap-2">
                            <a href="{{ route('appointments') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-info" onclick="checkAvailability()">
                                    <i class="fas fa-clock me-2"></i>Check Availability
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-calendar-plus me-2"></i>Schedule Appointment
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header"><h5><i class="fas fa-info-circle me-2"></i>How it works</h5></div>
                <div class="card-body small">
                    <ol class="ps-3 mb-0">
                        <li class="mb-2">Scheduling saves a <strong>scheduled</strong> visit (no vitals or billing yet).</li>
                        <li class="mb-2">On the appointment day, use <strong>Check-in</strong> from the appointments list.</li>
                        <li>You'll complete vitals, services, and payment on the regular visit form — same as walk-ins.</li>
                    </ol>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5><i class="fas fa-chart-line me-2"></i>Today's Schedule</h5></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Total</span><span class="badge bg-primary">{{ $todayStats['total'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Scheduled</span><span class="badge bg-warning text-dark">{{ $todayStats['scheduled'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Checked in</span><span class="badge bg-success">{{ $todayStats['checked_in'] }}</span>
                    </div>
                </div>
            </div>

            @if($recentPatients->isNotEmpty())
            <div class="card mb-4">
                <div class="card-header"><h5><i class="fas fa-users me-2"></i>Recent Patients</h5></div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($recentPatients as $patient)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-0">{{ $patient->first_name }} {{ $patient->last_name }}</h6>
                                <small class="text-muted">{{ $patient->patient_code }}</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary"
                                onclick="selectQuickPatient({{ $patient->id }}, '{{ addslashes($patient->first_name . ' ' . $patient->last_name) }}', '{{ $patient->patient_code }}')">
                                Select
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const patientSearchInput = document.getElementById("patient_search");
    const patientResultsDiv = document.getElementById("patient_results");
    const patientIdInput = document.getElementById("patient_id");
    const patientNameInput = document.getElementById("patient_name");
    let searchTimeout;

    if (patientSearchInput) {
        patientSearchInput.addEventListener("input", function () {
            clearTimeout(searchTimeout);
            const term = this.value.trim();
            if (term.length < 2) {
                patientResultsDiv.innerHTML = "";
                return;
            }
            searchTimeout = setTimeout(() => {
                fetch(`/api/patients/search?q=${encodeURIComponent(term)}`)
                    .then((r) => r.json())
                    .then((data) => {
                        if (!data.success || !data.patients.length) {
                            patientResultsDiv.innerHTML = '<div class="list-group"><div class="list-group-item text-muted">No patients found</div></div>';
                            return;
                        }
                        let html = '<div class="list-group mt-1">';
                        data.patients.forEach((p) => {
                            html += `<button type="button" class="list-group-item list-group-item-action text-start patient-result"
                                data-id="${p.id}" data-name="${p.first_name} ${p.last_name}" data-code="${p.patient_code}">
                                <strong>${p.first_name} ${p.last_name}</strong>
                                <small class="text-muted d-block">${p.patient_code}</small>
                            </button>`;
                        });
                        html += "</div>";
                        patientResultsDiv.innerHTML = html;
                        patientResultsDiv.querySelectorAll(".patient-result").forEach((btn) => {
                            btn.addEventListener("click", function () {
                                patientIdInput.value = this.dataset.id;
                                patientNameInput.value = this.dataset.name;
                                patientSearchInput.value = `${this.dataset.name} (${this.dataset.code})`;
                                patientResultsDiv.innerHTML = "";
                            });
                        });
                    });
            }, 300);
        });
    }
});

function selectQuickPatient(id, name, code) {
    document.getElementById("patient_id").value = id;
    document.getElementById("patient_name").value = name;
    document.getElementById("patient_search").value = `${name} (${code})`;
    document.getElementById("patient_results").innerHTML = "";
}

function checkAvailability() {
    const date = document.getElementById("visit_date").value;
    const time = document.getElementById("visit_time").value;
    const practitioner = Array.from(document.getElementById("practitioner").selectedOptions).map((o) => o.value);

    if (!date || !time || !practitioner.length) {
        window.clinicSystem?.showAlert?.("Select date, time, and at least one practitioner.", "warning");
        return;
    }

    const params = new URLSearchParams({ visit_date: date, visit_time: time });
    practitioner.forEach((p) => params.append("practitioner[]", p));

    fetch(`{{ route('appointments.availability') }}?${params}`)
        .then((r) => r.json())
        .then((data) => {
            window.clinicSystem?.showAlert?.(data.message, data.available ? "success" : "warning");
        });
}
</script>
@endsection
