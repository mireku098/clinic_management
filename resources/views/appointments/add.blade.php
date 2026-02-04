@extends('layouts.app')

@section('title', 'Schedule Appointment')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1>Schedule Appointment</h1>
            <p class="text-muted">Book new patient appointment</p>
        </div>
        <div>
            <a href="{{ route('appointments') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Appointments
            </a>
        </div>
    </div>

    <!-- Appointment Form -->
    <div class="row">
        <div class="col-lg-8">
            <form id="appointment_form">
                <!-- Patient Selection -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-user me-2"></i>Patient Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="patient_search" class="form-label">Search Patient *</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" class="form-control" id="patient_search"
                                        placeholder="Search by name, code, or phone..." required />
                                </div>
                                <div id="patient_results"></div>
                            </div>
                            <div class="col-md-3">
                                <label for="patient_id" class="form-label">Patient ID</label>
                                <input type="text" class="form-control" id="patient_id" readonly />
                            </div>
                            <div class="col-md-3">
                                <label for="patient_name" class="form-label">Patient Name</label>
                                <input type="text" class="form-control" id="patient_name" readonly />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Appointment Details -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-calendar me-2"></i>Appointment Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="appointment_date" class="form-label">Appointment Date *</label>
                                <input type="date" class="form-control date-picker" id="appointment_date"
                                    name="appointment_date" required />
                            </div>
                            <div class="col-md-4">
                                <label for="appointment_time" class="form-label">Appointment Time *</label>
                                <input type="time" class="form-control" id="appointment_time" name="appointment_time"
                                    required />
                            </div>
                            <div class="col-md-4">
                                <label for="appointment_duration" class="form-label">Duration (minutes)</label>
                                <select class="form-select" id="appointment_duration" name="appointment_duration">
                                    <option value="30">30 minutes</option>
                                    <option value="45">45 minutes</option>
                                    <option value="60" selected>60 minutes</option>
                                    <option value="90">90 minutes</option>
                                    <option value="120">120 minutes</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="service_type" class="form-label">Service Type *</label>
                                <select class="form-select" id="service_type" name="service_type" required>
                                    <option value="">Select Service</option>
                                    <option value="consultation">Consultation</option>
                                    <option value="physiotherapy">Physiotherapy</option>
                                    <option value="massage">Massage Therapy</option>
                                    <option value="ultrasound">Ultrasound</option>
                                    <option value="blood_test">Blood Test</option>
                                    <option value="ecg">ECG</option>
                                    <option value="package">Package Session</option>
                                    <option value="follow-up">Follow-up</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="practitioner" class="form-label">Practitioner *</label>
                                <select class="form-select" id="practitioner" name="practitioner" required>
                                    <option value="">Select Practitioner</option>
                                    <option value="dr-smith">Dr. Smith</option>
                                    <option value="dr-johnson">Dr. Johnson</option>
                                    <option value="dr-williams">Dr. Williams</option>
                                    <option value="therapist-brown">Therapist Brown</option>
                                    <option value="therapist-davis">Therapist Davis</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="appointment_notes" class="form-label">Appointment Notes</label>
                                <textarea class="form-control" id="appointment_notes" name="appointment_notes" rows="3"
                                    placeholder="Special requirements or notes for the appointment..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reminder Settings -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-bell me-2"></i>Reminder Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Send Reminders</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="sms_reminder" name="reminders[]"
                                        value="sms" checked />
                                    <label class="form-check-label" for="sms_reminder">
                                        <i class="fas fa-sms me-2"></i>SMS Reminder
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="email_reminder"
                                        name="reminders[]" value="email" />
                                    <label class="form-check-label" for="email_reminder">
                                        <i class="fas fa-envelope me-2"></i>Email Reminder
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="reminder_time" class="form-label">Reminder Time</label>
                                <select class="form-select" id="reminder_time" name="reminder_time">
                                    <option value="60">1 hour before</option>
                                    <option value="120">2 hours before</option>
                                    <option value="1440" selected>1 day before</option>
                                    <option value="2880">2 days before</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="form-actions">
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>
                                Clear Form
                            </button>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-info" onclick="checkAvailability()">
                                    <i class="fas fa-clock me-2"></i>
                                    Check Availability
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-calendar-plus me-2"></i>
                                    Schedule Appointment
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Availability Calendar -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-calendar-alt me-2"></i>Availability Calendar</h5>
                </div>
                <div class="card-body">
                    <div id="availability_calendar"></div>
                </div>
            </div>

            <!-- Time Slots -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-clock me-2"></i>Available Time Slots</h5>
                </div>
                <div class="card-body">
                    <div class="time-slots">
                        <div class="time-slot available" data-time="09:00">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>09:00 AM</span>
                                <span class="badge bg-success">Available</span>
                            </div>
                        </div>
                        <div class="time-slot available" data-time="09:30">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>09:30 AM</span>
                                <span class="badge bg-success">Available</span>
                            </div>
                        </div>
                        <div class="time-slot booked" data-time="10:00">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>10:00 AM</span>
                                <span class="badge bg-warning">Booked</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-chart-line me-2"></i>Today's Schedule</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Total Appointments</span>
                        <span class="badge bg-primary">24</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Confirmed</span>
                        <span class="badge bg-success">18</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Pending</span>
                        <span class="badge bg-warning">4</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Available Slots</span>
                        <span class="badge bg-info">8</span>
                    </div>
                </div>
            </div>

            <!-- Recent Patients -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-users me-2"></i>Recent Patients</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-0">John Doe</h6>
                                <small class="text-muted">P001</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary"
                                onclick="selectQuickPatient('P001', 'John Doe')">
                                Select
                            </button>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-0">Jane Smith</h6>
                                <small class="text-muted">P002</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary"
                                onclick="selectQuickPatient('P002', 'Jane Smith')">
                                Select
                            </button>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-0">Michael Johnson</h6>
                                <small class="text-muted">P003</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary"
                                onclick="selectQuickPatient('P003', 'Michael Johnson')">
                                Select
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Help -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-question-circle me-2"></i>Help</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled small">
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Search patient or use quick select
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Check availability before scheduling
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Set reminders for patient notifications
                        </li>
                        <li>
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Double-check date and time
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
.time-slot {
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.time-slot:hover {
    background: #f8f9fa;
}

.time-slot.available {
    border-color: #48bb78;
}

.time-slot.booked {
    border-color: #ed8936;
    background: #fef5e7;
}

.time-slot.selected {
    background: #e6fffa;
    border-color: #38a169;
}

.flatpickr-day.today {
    background: var(--primary-color) !important;
    color: white !important;
}

.flatpickr-day.limited-availability {
    opacity: 0.7;
}

.flatpickr-day.limited-availability:hover {
    opacity: 1;
}
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const today = new Date().toISOString().split("T")[0];
    document.getElementById("appointment_date").value = today;
    initializeCalendar();
});

function initializeCalendar() {
    const calendarEl = document.getElementById("availability_calendar");
    flatpickr(calendarEl, {
        inline: true,
        dateFormat: "Y-m-d",
        minDate: "today",
        onDayCreate: function(dObj, dStr, fp, dayElem) {
            const today = new Date();
            const selectedDate = new Date(dStr);

            if (selectedDate.getDay() === 0 || selectedDate.getDay() === 6) {
                dayElem.classList.add("limited-availability");
            }

            if (dStr === today.toISOString().split("T")[0]) {
                dayElem.classList.add("today");
            }
        },
        onChange: function(selectedDates, dateStr) {
            document.getElementById("appointment_date").value = dateStr;
            updateTimeSlots(dateStr);
        },
    });
}

function updateTimeSlots(date) {
    const slots = document.querySelectorAll(".time-slot");
    const selectedDate = new Date(date);
    const dayOfWeek = selectedDate.getDay();

    slots.forEach((slot) => {
        const random = Math.random();
        if (dayOfWeek === 0 || dayOfWeek === 6) {
            slot.classList.remove("available");
            slot.classList.add(random > 0.7 ? "available" : "booked");
        } else {
            slot.classList.remove("booked");
            slot.classList.add(random > 0.3 ? "available" : "booked");
        }
    });
}

document.querySelectorAll(".time-slot").forEach(function(slot) {
    slot.addEventListener("click", function() {
        if (this.classList.contains("available")) {
            document.querySelectorAll(".time-slot").forEach((s) => s.classList.remove("selected"));
            this.classList.add("selected");
            const time = this.dataset.time;
            document.getElementById("appointment_time").value = time;
            window.clinicSystem.showAlert(`Selected time slot: ${time}`, "info");
        } else {
            window.clinicSystem.showAlert("This time slot is not available", "warning");
        }
    });
});

function selectQuickPatient(patientId, patientName) {
    document.getElementById("patient_id").value = patientId;
    document.getElementById("patient_name").value = patientName;
    document.getElementById("patient_search").value = patientName;
    document.getElementById("patient_results").innerHTML = "";
}

function checkAvailability() {
    const date = document.getElementById("appointment_date").value;
    const time = document.getElementById("appointment_time").value;
    const practitioner = document.getElementById("practitioner").value;

    if (!date || !time || !practitioner) {
        window.clinicSystem.showAlert("Please select date, time, and practitioner", "warning");
        return;
    }

    window.clinicSystem.showAlert("Checking availability...", "info");
    setTimeout(() => {
        window.clinicSystem.showAlert("Time slot is available!", "success");
    }, 1500);
}

document.getElementById("appointment_form").addEventListener("submit", function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const appointmentData = Object.fromEntries(formData);

    if (!appointmentData.patient_id || !appointmentData.appointment_date || !appointmentData.appointment_time ||
        !appointmentData.service_type || !appointmentData.practitioner) {
        window.clinicSystem.showAlert("Please fill in all required fields", "danger");
        return;
    }

    const appointmentDate = new Date(appointmentData.appointment_date);
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    if (appointmentDate < today) {
        window.clinicSystem.showAlert("Appointment date cannot be in the past", "danger");
        return;
    }

    console.log("Scheduling appointment:", appointmentData);
    window.clinicSystem.showAlert("Appointment scheduled successfully!", "success");

    setTimeout(() => {
        window.location.href = "{{ route('appointments') }}";
    }, 2000);
});

document.getElementById("service_type").addEventListener("change", function() {
    const durations = {
        consultation: 30,
        physiotherapy: 60,
        massage: 60,
        ultrasound: 45,
        blood_test: 30,
        ecg: 30,
        package: 90,
        "follow-up": 30,
    };

    if (durations[this.value]) {
        document.getElementById("appointment_duration").value = durations[this.value];
    }
});
</script>

<!-- Alert Container -->
<div id="alert-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1050"></div>
@endsection