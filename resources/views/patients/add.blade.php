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
                                <label for="phone" class="form-label">Phone Number *</label>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="e.g. 0201234567" value="{{ old('phone') }}" required />
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
                                <label for="emergency_contact_name" class="form-label">Emergency Contact Name *</label>
                                <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" required />
                            </div>
                            <div class="col-md-6">
                                <label for="emergency_contact_phone" class="form-label">Emergency Contact Phone *</label>
                                <input type="tel" class="form-control" id="emergency_contact_phone" name="emergency_contact_phone" placeholder="02012345678" value="{{ old('emergency_contact_phone') }}" required />
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
                        <span class="badge bg-primary">{{ $stats['today'] ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>This Week</span>
                        <span class="badge bg-success">{{ $stats['this_week'] ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>This Month</span>
                        <span class="badge bg-info">{{ $stats['this_month'] ?? 0 }}</span>
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
        const requiredFields = [
            "first_name", 
            "last_name", 
            "gender", 
            "date_of_birth", 
            "phone",
            "emergency_contact_name",
            "emergency_contact_phone"
        ];

        requiredFields.forEach((fieldId) => {
            const field = document.getElementById(fieldId);
            if (field) {
                // Validate on blur
                field.addEventListener("blur", function () {
                    validateField(this);
                });
                // Real-time feedback after initial attempt
                field.addEventListener("input", function () {
                    if (this.classList.contains("is-invalid") || this.classList.contains("is-valid")) {
                        validateField(this);
                    }
                });
            }
        });

        // Email validation
        const emailField = document.getElementById("email");
        if (emailField) {
            emailField.addEventListener("blur", function () {
                validateEmail(this);
            });
            emailField.addEventListener("input", function () {
                if (this.classList.contains("is-invalid") || this.classList.contains("is-valid")) {
                    validateEmail(this);
                }
            });
        }
    });

    function getFieldLabel(field) {
        const label = document.querySelector(`label[for="${field.id}"]`);
        return label ? label.textContent.replace('*', '').trim() : field.name;
    }

    function validateField(field) {
        const value = field.value.trim();
        const feedbackElement = field.parentNode.querySelector(".invalid-feedback");
        
        if (feedbackElement) feedbackElement.remove();

        if (value === "") {
            field.classList.remove("is-valid");
            field.classList.add("is-invalid");
            
            const fieldLabel = getFieldLabel(field);
            const feedback = document.createElement("div");
            feedback.className = "invalid-feedback";
            
            // Custom messages as requested
            if (field.id === 'first_name') feedback.textContent = "Please enter patient's first name";
            else if (field.id === 'last_name') feedback.textContent = "Please enter patient's last name";
            else if (field.id === 'date_of_birth') feedback.textContent = "Date of birth is required";
            else feedback.textContent = `${fieldLabel} is required`;
            
            field.parentNode.appendChild(feedback);
            return false;
        } else {
            field.classList.remove("is-invalid");
            field.classList.add("is-valid");
            return true;
        }
    }

    function validateEmail(field) {
        const value = field.value.trim();
        if (value === "") {
            field.classList.remove("is-invalid", "is-valid");
            const feedback = field.parentNode.querySelector(".invalid-feedback");
            if (feedback) feedback.remove();
            return true;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const feedbackElement = field.parentNode.querySelector(".invalid-feedback");
        if (feedbackElement) feedbackElement.remove();

        if (!emailRegex.test(value)) {
            field.classList.remove("is-valid");
            field.classList.add("is-invalid");
            const feedback = document.createElement("div");
            feedback.className = "invalid-feedback";
            feedback.textContent = "Please enter a valid email address";
            field.parentNode.appendChild(feedback);
            return false;
        } else {
            field.classList.remove("is-invalid");
            field.classList.add("is-valid");
            return true;
        }
    }

    // Age calculation
    document.getElementById("date_of_birth").addEventListener("change", function () {
        const dob = this.value;
        if (dob) {
            const birthDate = new Date(dob);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            document.getElementById("age").value = age;
        } else {
            document.getElementById("age").value = "";
        }
    });

    // Form Submission with AJAX
    document.getElementById("patient_form").addEventListener("submit", function (e) {
        e.preventDefault();
        
        const requiredFields = [
            "first_name", "last_name", "gender", "date_of_birth", 
            "phone", "emergency_contact_name", "emergency_contact_phone"
        ];
        
        let isValid = true;
        let missingFields = [];

        requiredFields.forEach(id => {
            const field = document.getElementById(id);
            if (!validateField(field)) {
                isValid = false;
                missingFields.push(getFieldLabel(field));
            }
        });

        const emailField = document.getElementById("email");
        if (!validateEmail(emailField)) {
            isValid = false;
            missingFields.push("Valid Email Address");
        }

        if (!isValid) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Information',
                html: `Please fill in the following required fields:<br><br><ul class="text-start"><li>${missingFields.join('</li><li>')}</li></ul>`,
                confirmButtonColor: '#3085d6'
            });
            // Scroll to first error
            const firstError = document.querySelector(".is-invalid");
            if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Registering...';
        submitBtn.disabled = true;
        
        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(async response => {
            const data = await response.json();
            if (response.status === 422) {
                // Server-side validation errors
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                
                // Clear existing errors
                document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
                document.querySelectorAll(".invalid-feedback").forEach(el => el.remove());

                // Display server errors
                Object.keys(data.errors).forEach(key => {
                    const field = document.getElementById(key) || document.getElementsByName(key)[0];
                    if (field) {
                        field.classList.add("is-invalid");
                        const feedback = document.createElement("div");
                        feedback.className = "invalid-feedback";
                        feedback.textContent = data.errors[key][0];
                        field.parentNode.appendChild(feedback);
                    }
                });

                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please correct the highlighted errors.',
                    confirmButtonColor: '#d33'
                });
            } else if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Patient Registered!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = "{{ route('patients') }}";
                });
            } else {
                throw new Error(data.message || 'Something went wrong');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'An unexpected error occurred.'
            });
        });
    });

    // Image preview
    document.getElementById("patient_photo").addEventListener("change", function (e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire('File too large', 'Maximum size is 2MB', 'error');
                this.value = '';
                return;
            }
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
