@extends('layouts.app')

@section('title', 'Edit Patient')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1>Edit Patient</h1>
            <p class="text-muted">Update patient information and medical records</p>
        </div>
        <div>
            <a href="{{ route('patients') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Patients
            </a>
        </div>
    </div>

    <!-- Patient Info Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-2 text-center">
                    <div class="patient-avatar mx-auto mb-2" style="width: 80px; height: 80px">
                        @if($patient->photo_path)
                            <img src="{{ asset('storage/' . $patient->photo_path) }}" class="rounded-circle" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <i class="fas fa-user" style="font-size: 2rem"></i>
                        @endif
                    </div>
                    <h6 class="mb-0">{{ $patient->first_name }} {{ $patient->last_name }}</h6>
                    <small class="text-muted">{{ $patient->patient_code }}</small>
                </div>
                <div class="col-md-10">
                    <div class="row">
                        <div class="col-md-3">
                            <small class="text-muted">Phone</small>
                            <p class="mb-1 fw-bold">{{ $patient->phone }}</p>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Email</small>
                            <p class="mb-1 fw-bold">{{ $patient->email ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Age</small>
                            <p class="mb-1 fw-bold">{{ $patient->age ?? Carbon\Carbon::parse($patient->date_of_birth)->age }} years</p>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Gender</small>
                            <p class="mb-1 fw-bold">{{ ucfirst($patient->gender) }}</p>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">
                            <small class="text-muted">Blood Group</small>
                            <p class="mb-1 fw-bold">{{ $patient->blood_group ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Registration Date</small>
                            <p class="mb-1 fw-bold">{{ \Carbon\Carbon::parse($patient->registered_at ?? $patient->created_at)->format('M j, Y') }}</p>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Last Visit</small>
                            <p class="mb-1 fw-bold">No visits yet</p>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Status</small>
                            <p class="mb-1"><span class="badge bg-success">Active</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Patient Form -->
    <div class="row">
        <div class="col-lg-8">
            <form id="edit_patient_form" method="POST" action="{{ route('patients.update', $patient->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="patient_id" name="patient_id" value="{{ $patient->id }}" />

                <!-- Patient Photo Upload - Moved to top -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-camera me-2"></i>Patient Photo</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="patient-avatar mx-auto mb-3" style="width: 120px; height: 120px">
                            @if($patient->photo_path)
                                <img src="{{ asset('storage/' . $patient->photo_path) }}" class="rounded-circle" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <i class="fas fa-user" style="font-size: 3rem"></i>
                            @endif
                        </div>
                        <input type="file" class="form-control" id="patient_photo" name="patient_photo" accept="image/jpeg,image/jpg,image/png" onchange="console.log('File selected:', this.files[0]); document.getElementById('selected_file_name').textContent = this.files[0]?.name || ''" />
                        <small class="d-block text-muted mt-2" id="selected_file_name">Update patient photo (JPG/PNG, max 2MB)</small>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-user me-2"></i>Personal Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">First Name *</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="{{ $patient->first_name }}" required />
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Last Name *</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $patient->last_name }}" required />
                            </div>
                            <div class="col-md-4">
                                <label for="gender" class="form-label">Gender *</label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ $patient->gender === 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ $patient->gender === 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ $patient->gender === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="date_of_birth" class="form-label">Date of Birth *</label>
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ $patient->date_of_birth }}" required />
                            </div>
                            <div class="col-md-4">
                                <label for="age" class="form-label">Age</label>
                                <input type="number" class="form-control" id="age" name="age" value="{{ $patient->age ?? Carbon\Carbon::parse($patient->date_of_birth)->age }}" readonly />
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number *</label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="{{ $patient->phone }}" required />
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ $patient->email }}" />
                            </div>
                            <div class="col-12">
                                <label for="address" class="form-label">Residential Address</label>
                                <textarea class="form-control" id="address" name="address" rows="2">{{ $patient->address }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="occupation" class="form-label">Occupation</label>
                                <input type="text" class="form-control" id="occupation" name="occupation" value="{{ $patient->occupation }}" />
                            </div>
                            <div class="col-md-6">
                                <label for="marital_status" class="form-label">Marital Status</label>
                                <select class="form-select" id="marital_status" name="marital_status">
                                    <option value="">Select Status</option>
                                    <option value="single" {{ $patient->marital_status === 'single' ? 'selected' : '' }}>Single</option>
                                    <option value="married" {{ $patient->marital_status === 'married' ? 'selected' : '' }}>Married</option>
                                    <option value="divorced" {{ $patient->marital_status === 'divorced' ? 'selected' : '' }}>Divorced</option>
                                    <option value="widowed" {{ $patient->marital_status === 'widowed' ? 'selected' : '' }}>Widowed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Medical Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-heartbeat me-2"></i>Medical Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="blood_group" class="form-label">Blood Group</label>
                                <select class="form-select" id="blood_group" name="blood_group">
                                    <option value="">Select Blood Group</option>
                                    <option value="A+" {{ $patient->blood_group === 'A+' ? 'selected' : '' }}>A+</option>
                                    <option value="A-" {{ $patient->blood_group === 'A-' ? 'selected' : '' }}>A-</option>
                                    <option value="B+" {{ $patient->blood_group === 'B+' ? 'selected' : '' }}>B+</option>
                                    <option value="B-" {{ $patient->blood_group === 'B-' ? 'selected' : '' }}>B-</option>
                                    <option value="AB+" {{ $patient->blood_group === 'AB+' ? 'selected' : '' }}>AB+</option>
                                    <option value="AB-" {{ $patient->blood_group === 'AB-' ? 'selected' : '' }}>AB-</option>
                                    <option value="O+" {{ $patient->blood_group === 'O+' ? 'selected' : '' }}>O+</option>
                                    <option value="O-" {{ $patient->blood_group === 'O-' ? 'selected' : '' }}>O-</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="sickle_cell_status" class="form-label">Sickle Cell Status</label>
                                <select class="form-select" id="sickle_cell_status" name="sickle_cell_status">
                                    <option value="">Select Status</option>
                                    <option value="AA" {{ $patient->sickle_cell_status === 'AA' ? 'selected' : '' }}>AA</option>
                                    <option value="AS" {{ $patient->sickle_cell_status === 'AS' ? 'selected' : '' }}>AS</option>
                                    <option value="SS" {{ $patient->sickle_cell_status === 'SS' ? 'selected' : '' }}>SS</option>
                                    <option value="Unknown" {{ $patient->sickle_cell_status === 'Unknown' ? 'selected' : '' }}>Unknown</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="allergies" class="form-label">Allergies</label>
                                <textarea class="form-control" id="allergies" name="allergies" rows="2" placeholder="List any known allergies...">{{ $patient->allergies }}</textarea>
                            </div>
                            <div class="col-12">
                                <label for="chronic_conditions" class="form-label">Chronic Conditions</label>
                                <textarea class="form-control" id="chronic_conditions" name="chronic_conditions" rows="2" placeholder="List any chronic conditions...">{{ $patient->chronic_conditions }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-phone-alt me-2"></i>Emergency Contact</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="emergency_contact_name" class="form-label">Emergency Contact Name</label>
                                <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name" value="{{ $patient->emergency_contact_name }}" />
                            </div>
                            <div class="col-md-6">
                                <label for="emergency_contact_phone" class="form-label">Emergency Contact Phone</label>
                                <input type="tel" class="form-control" id="emergency_contact_phone" name="emergency_contact_phone" value="{{ $patient->emergency_contact_phone }}" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-danger" onclick="archivePatient()">
                                <i class="fas fa-archive me-2"></i>
                                Archive Patient
                            </button>
                            <div>
                                <a href="{{ route('patients') }}" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-times me-2"></i>
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    Update Patient
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary">
                            <i class="fas fa-stethoscope me-2"></i>
                            Schedule Appointment
                        </button>
                        <button type="button" class="btn btn-outline-success">
                            <i class="fas fa-file-medical me-2"></i>
                            Add Visit Record
                        </button>
                        <button type="button" class="btn btn-outline-info">
                            <i class="fas fa-flask me-2"></i>
                            Order Lab Test
                        </button>
                        <button type="button" class="btn btn-outline-warning">
                            <i class="fas fa-pills me-2"></i>
                            Prescribe Medication
                        </button>
                    </div>
                </div>
            </div>

            <!-- Recent Visits -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-history me-2"></i>Recent Visits</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Regular Checkup</h6>
                                <small class="text-muted">2024-01-25 - Dr. Smith</small>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Initial Assessment</h6>
                                <small class="text-muted">2024-01-15 - Dr. Williams</small>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('visits') }}" class="btn btn-sm btn-outline-primary">
                            View All Visits
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
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
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById("date_of_birth").addEventListener("change", function () {
        const dob = this.value;
        if (dob) {
            const age = Math.floor((new Date() - new Date(dob).getTime()) / 3.15576e10);
            document.getElementById("age").value = age;
        } else {
            document.getElementById("age").value = "";
        }
    });

    document.getElementById("edit_patient_form").addEventListener("submit", function (e) {
        // Let the form submit naturally - no JavaScript interference
        console.log('Form submitting naturally...');
    });

    function archivePatient() {
        Swal.fire({
            title: "Archive Patient?",
            html: `
                <p>Archiving this patient will:</p>
                <ul style="text-align: left; display: inline-block;">
                    <li>Mark patient as inactive</li>
                    <li>Remove from active patient lists</li>
                    <li>Preserve all medical records</li>
                    <li>Keep billing history intact</li>
                    <li>Maintain visit records</li>
                </ul>
                <p><strong>Patient can be reactivated later if needed.</strong></p>
            `,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#ffc107",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Archive Patient",
            cancelButtonText: "Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                window.clinicSystem.showAlert("Patient archived successfully", "success");
                setTimeout(() => {
                    window.location.href = "{{ route('patients') }}";
                }, 1500);
            }
        });
    }

    document.getElementById("patient_photo").addEventListener("change", function (e) {
        const file = e.target.files[0];
        if (file && file.type.startsWith("image/")) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const avatar = document.querySelector(".patient-avatar");
                avatar.innerHTML = `<img src="${e.target.result}" class="rounded-circle" style="width: 100%; height: 100%; object-fit: cover;">`;
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
