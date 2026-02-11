@extends('layouts.app')

@section('title', 'Add Patient')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1>Add New Patient</h1>
            <p class="text-muted">Register a new patient in the system</p>
        </div>
        <div>
            <a href="{{ route('patients') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Patients
            </a>
        </div>
    </div>

    <!-- Patient Registration Form -->
    <div class="row">
        <div class="col-lg-8">
            <form id="patient_form" method="POST" action="{{ route('patients.store') }}" enctype="multipart/form-data">
                @csrf
                <!-- Personal Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-user me-2"></i>Personal Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">First Name *</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name') }}" required />
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Last Name *</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name') }}" required />
                            </div>
                            <div class="col-md-4">
                                <label for="gender" class="form-label">Gender *</label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="male" @selected(old('gender') === 'male')>Male</option>
                                    <option value="female" @selected(old('gender') === 'female')>Female</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="date_of_birth" class="form-label">Date of Birth *</label>
                                <input type="date" class="form-control date-picker" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required />
                            </div>
                            <div class="col-md-4">
                                <label for="age" class="form-label">Age</label>
                                <input type="number" class="form-control" id="age" name="age" readonly />
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="Any phone number format" value="{{ old('phone') }}" />
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="patient@email.com" value="{{ old('email') }}" />
                            </div>
                            <div class="col-12">
                                <label for="address" class="form-label">Residential Address</label>
                                <textarea class="form-control" id="address" name="address" rows="2">{{ old('address') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="occupation" class="form-label">Occupation</label>
                                <input type="text" class="form-control" id="occupation" name="occupation" value="{{ old('occupation') }}" />
                            </div>
                            <div class="col-md-6">
                                <label for="height" class="form-label">Height (cm)</label>
                                <input type="number" step="0.1" class="form-control" id="height" name="height" placeholder="175.5" value="{{ old('height') }}" />
                                <small class="text-muted">Patient's height in centimeters</small>
                            </div>
                            <div class="col-md-6">
                                <label for="marital_status" class="form-label">Marital Status</label>
                                <select class="form-select" id="marital_status" name="marital_status">
                                    <option value="">Select Status</option>
                                    <option value="single" @selected(old('marital_status') === 'single')>Single</option>
                                    <option value="married" @selected(old('marital_status') === 'married')>Married</option>
                                    <option value="divorced" @selected(old('marital_status') === 'divorced')>Divorced</option>
                                    <option value="widowed" @selected(old('marital_status') === 'widowed')>Widowed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Patient Photo -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-camera me-2"></i>Patient Photo</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <div class="patient-avatar mx-auto mb-3" style="width: 120px; height: 120px; border: 2px dashed #dee2e6; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-user" style="font-size: 3rem; color: #6c757d;"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <label for="patient_photo" class="form-label">Upload Patient Photo</label>
                                <input type="file" class="form-control" id="patient_photo" name="patient_photo" accept="image/jpeg,image/jpg,image/png" onchange="document.getElementById('selected_file_name').textContent = this.files[0]?.name || ''" />
                                <small class="d-block text-muted mt-2" id="selected_file_name">Upload patient photo (JPG/PNG, max 2MB)</small>
                                <small class="d-block text-muted">Optional: Upload a clear photo of the patient for identification purposes</small>
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
                                    @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $group)
                                        <option value="{{ $group }}" @selected(old('blood_group') === $group)>{{ $group }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="sickle_cell_status" class="form-label">Sickle Cell Status</label>
                                <select class="form-select" id="sickle_cell_status" name="sickle_cell_status">
                                    <option value="">Select Status</option>
                                    @foreach(['AA','AS','SS','Unknown'] as $status)
                                        <option value="{{ $status }}" @selected(old('sickle_cell_status') === $status)>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="allergies" class="form-label">Allergies</label>
                                <textarea class="form-control" id="allergies" name="allergies" rows="2" placeholder="List any known allergies...">{{ old('allergies') }}</textarea>
                            </div>
                            <div class="col-12">
                                <label for="chronic_conditions" class="form-label">Chronic Conditions</label>
                                <textarea class="form-control" id="chronic_conditions" name="chronic_conditions" rows="2" placeholder="List any chronic conditions...">{{ old('chronic_conditions') }}</textarea>
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
                                <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" />
                            </div>
                            <div class="col-md-6">
                                <label for="emergency_contact_phone" class="form-label">Emergency Contact Phone</label>
                                <input type="tel" class="form-control" id="emergency_contact_phone" name="emergency_contact_phone" placeholder="02012345678" value="{{ old('emergency_contact_phone') }}" />
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
                                <button type="button" class="btn btn-outline-primary" onclick="saveAsDraft()">
                                    <i class="fas fa-save me-2"></i>
                                    Save as Draft
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-user-plus me-2"></i>
                                    Register Patient
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Stats -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-chart-line me-2"></i>Registration Stats</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Patients Today</span>
                        <span class="badge bg-primary">5</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>This Week</span>
                        <span class="badge bg-success">23</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>This Month</span>
                        <span class="badge bg-info">87</span>
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
                            Fields marked with * are required
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Age is calculated automatically from date of birth
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Patient photos help with identification
                        </li>
                        <li>
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Emergency contact is important for patient safety
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    // Real-time form validation
    document.addEventListener("DOMContentLoaded", function () {
        const requiredFields = ["first_name", "last_name", "gender", "date_of_birth", "phone"];

        requiredFields.forEach((fieldId) => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.addEventListener("blur", function () {
                    validateField(this);
                });
                field.addEventListener("input", function () {
                    if (this.classList.contains("is-invalid") || this.classList.contains("is-valid")) {
                        validateField(this);
                    }
                });
            }
        });

        const emailField = document.getElementById("email");
        if (emailField) {
            emailField.addEventListener("blur", function () {
                validateEmail(this);
            });
        }

        const phoneField = document.getElementById("phone");
        if (phoneField) {
            phoneField.addEventListener("blur", function () {
                validatePhone(this);
            });
        }
    });

    function validateField(field) {
        const value = field.value.trim();
        const feedbackElement = field.parentNode.querySelector(".invalid-feedback, .valid-feedback");

        if (feedbackElement) {
            feedbackElement.remove();
        }

        if (value === "") {
            field.classList.remove("is-valid");
            field.classList.add("is-invalid");
            const feedback = document.createElement("div");
            feedback.className = "invalid-feedback";
            feedback.textContent = "This field is required";
            field.parentNode.appendChild(feedback);
        } else {
            field.classList.remove("is-invalid");
            field.classList.add("is-valid");
            const feedback = document.createElement("div");
            feedback.className = "valid-feedback";
            feedback.textContent = "Looks good!";
            field.parentNode.appendChild(feedback);
        }
    }

    function validateEmail(field) {
        const value = field.value.trim();
        const feedbackElement = field.parentNode.querySelector(".invalid-feedback, .valid-feedback");
        if (feedbackElement) {
            feedbackElement.remove();
        }
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (value !== "" && !emailRegex.test(value)) {
            field.classList.remove("is-valid");
            field.classList.add("is-invalid");
            const feedback = document.createElement("div");
            feedback.className = "invalid-feedback";
            feedback.textContent = "Please enter a valid email address";
            field.parentNode.appendChild(feedback);
        } else if (value !== "") {
            field.classList.remove("is-invalid");
            field.classList.add("is-valid");
            const feedback = document.createElement("div");
            feedback.className = "valid-feedback";
            feedback.textContent = "Valid email address";
            field.parentNode.appendChild(feedback);
        }
    }

    function validatePhone(field) {
        // Commented out phone validation - allowing any phone format
        const value = field.value.trim();
        const feedbackElement = field.parentNode.querySelector(".invalid-feedback, .valid-feedback");
        if (feedbackElement) {
            feedbackElement.remove();
        }
        
        // const phoneRegex = /^0[0-9]{9}$/; // Commented out - no longer enforcing Ghanaian format
        // if (value !== "" && !phoneRegex.test(value)) { // Commented out - no format restriction
        //     field.classList.remove("is-valid");
        //     field.classList.add("is-invalid");
        //     const feedback = document.createElement("div");
        //     feedback.className = "invalid-feedback";
        //     feedback.textContent = "Please enter a valid Ghanaian phone number (e.g., 0201234567)";
        //     field.parentNode.appendChild(feedback);
        // } else if (value !== "") { // Commented out - always valid if not empty
        //     field.classList.remove("is-invalid");
        //     field.classList.add("is-valid");
        //     const feedback = document.createElement("div");
        //     feedback.className = "valid-feedback";
        //     feedback.textContent = "Valid phone number";
        //     field.parentNode.appendChild(feedback);
        // }
        
        // New simplified validation - just check if it's not empty (optional field)
        if (value !== "") {
            field.classList.remove("is-invalid");
            field.classList.add("is-valid");
            const feedback = document.createElement("div");
            feedback.className = "valid-feedback";
            feedback.textContent = "Phone number accepted";
            field.parentNode.appendChild(feedback);
        } else {
            field.classList.remove("is-invalid", "is-valid");
        }
    }

    document.getElementById("date_of_birth").addEventListener("change", function () {
        const dob = this.value;
        if (dob) {
            const age = Math.floor((new Date() - new Date(dob).getTime()) / 3.15576e10);
            document.getElementById("age").value = age;
        } else {
            document.getElementById("age").value = "";
        }
    });

    document.getElementById("patient_form").addEventListener("submit", function (e) {
        e.preventDefault(); // Prevent normal form submission
        console.log('AJAX form submission started');
        
        const formData = new FormData(this);
        const patientData = Object.fromEntries(formData);
        console.log('Form data:', patientData);

        // Only validate required fields (phone is optional)
        if (!patientData.first_name || !patientData.last_name || !patientData.gender || !patientData.date_of_birth) {
            console.log('Validation failed - missing required fields');
            window.clinicSystem.showAlert("Please fill in all required fields (Name, Gender, Date of Birth)", "danger");
            return;
        }
        
        console.log('Validation passed - submitting via AJAX');
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Registering...';
        submitBtn.disabled = true;
        
        // Submit via AJAX
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Response:', data);
            
            if (data.success) {
                // Success - show success message then redirect
                Swal.fire({
                    icon: 'success',
                    title: 'Patient Registered!',
                    text: data.message || 'Patient has been created successfully.',
                    showConfirmButton: true,
                    timer: 2000,
                    timerProgressBar: true
                }).then(() => {
                    // Redirect after success message
                    window.location.href = '/patients';
                });
            } else {
                // Error - show error message
                Swal.fire({
                    icon: 'error',
                    title: 'Registration Failed',
                    text: data.message || 'An error occurred while creating the patient.',
                    showConfirmButton: true,
                });
            }
        })
        .catch(error => {
            console.error('AJAX Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Network Error',
                text: 'Could not connect to the server. Please try again.',
                showConfirmButton: true,
            });
        })
        .finally(() => {
            // Restore button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });

    function saveAsDraft() {
        const formData = new FormData(document.getElementById("patient_form"));
        const patientData = Object.fromEntries(formData);
        localStorage.setItem("patientDraft", JSON.stringify(patientData));
        window.clinicSystem.showAlert("Draft saved successfully", "info");
    }

    window.addEventListener("load", function () {
        const draft = localStorage.getItem("patientDraft");
        if (draft) {
            const patientData = JSON.parse(draft);
            Object.keys(patientData).forEach((key) => {
                const field = document.querySelector(`[name="${key}"]`);
                if (field) {
                    field.value = patientData[key];
                }
            });
        }
    });

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

<!-- Alert Container -->
<div id="alert-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1050"></div>
@endsection
