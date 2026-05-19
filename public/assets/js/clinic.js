// Renew Wellness Clinic Management System JavaScript

// Billing & Payments Functions
function refreshBills() {
    Swal.fire({
        title: "Refreshing Bills...",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        },
    });

    setTimeout(() => {
        Swal.close();
        Swal.fire({
            icon: "success",
            title: "Bills Refreshed",
            text: "Patient bills have been updated",
            timer: 2000,
            showConfirmButton: false,
        });
    }, 1500);
}

function printBill(billId) {
    Swal.fire({
        title: "Printing Bill...",
        text: `Generating bill ${billId} for printing`,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        },
    });

    setTimeout(() => {
        Swal.close();
        Swal.fire({
            icon: "success",
            title: "Print Complete",
            text: `Bill ${billId} has been sent to printer`,
            timer: 2000,
            showConfirmButton: false,
        });
    }, 2000);
}

document.addEventListener("DOMContentLoaded", function () {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]'),
    );
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Ensure Bootstrap dropdowns (header nav, notifications, etc.) work even when
    // templates are loaded without the original theme JS bundle
    var dropdownTriggerList = [].slice.call(
        document.querySelectorAll(".dropdown-toggle"),
    );
    dropdownTriggerList.forEach(function (dropdownTriggerEl) {
        if (!dropdownTriggerEl._dropdownInstance && window.bootstrap) {
            dropdownTriggerEl._dropdownInstance = new bootstrap.Dropdown(
                dropdownTriggerEl,
            );
        }
    });

    // Fallback manual toggling for cases where Bootstrap dropdown JS conflicts
    const headerDropdowns = document.querySelectorAll(
        ".navbar-nav .nav-item.dropdown",
    );

    function closeAllHeaderDropdowns() {
        headerDropdowns.forEach(function (dropdown) {
            dropdown.classList.remove("show");
            const menu = dropdown.querySelector(".dropdown-menu");
            if (menu) {
                menu.classList.remove("show");
            }
        });
    }

    headerDropdowns.forEach(function (dropdown) {
        const toggle = dropdown.querySelector(".dropdown-toggle");
        const menu = dropdown.querySelector(".dropdown-menu");
        if (!toggle || !menu) return;

        toggle.addEventListener("click", function (event) {
            event.preventDefault();
            event.stopPropagation();

            const isOpen = dropdown.classList.contains("show");
            closeAllHeaderDropdowns();

            if (!isOpen) {
                dropdown.classList.add("show");
                menu.classList.add("show");
            }
        });
    });

    document.addEventListener("click", function () {
        closeAllHeaderDropdowns();
    });

    // SweetAlert logout confirmation without altering HTML structure
    const logoutForm = document.getElementById("logout-form");
    const logoutLinks = document.querySelectorAll(
        'a[href$="/logout"], a[onclick*="logout-form"]',
    );

    if (logoutForm && logoutLinks.length > 0) {
        logoutLinks.forEach(function (logoutLink) {
            logoutLink.addEventListener(
                "click",
                function (event) {
                    event.preventDefault();
                    event.stopImmediatePropagation();

                    if (window.Swal) {
                        Swal.fire({
                            title: "Ready to log out?",
                            text: "You will need to sign in again to access the dashboard.",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonText: "Yes, log me out",
                            cancelButtonText: "Stay logged in",
                            reverseButtons: true,
                        }).then(function (result) {
                            if (result.isConfirmed) {
                                sessionStorage.clear();
                                localStorage.removeItem("lastActivity");
                                logoutForm.submit();
                            }
                        });
                    } else {
                        sessionStorage.clear();
                        localStorage.removeItem("lastActivity");
                        logoutForm.submit();
                    }
                },
                true,
            );
        });
    }

    // Mobile menu toggle
    const mobileMenuToggle = document.querySelector(".mobile-menu-toggle");
    const sidebar = document.querySelector(".sidebar");

    if (mobileMenuToggle && sidebar) {
        mobileMenuToggle.addEventListener("click", function () {
            sidebar.classList.toggle("show");
        });
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener("click", function (event) {
        if (window.innerWidth <= 768) {
            const isClickInsideSidebar =
                sidebar && sidebar.contains(event.target);
            const isClickOnToggle =
                mobileMenuToggle && mobileMenuToggle.contains(event.target);

            if (
                !isClickInsideSidebar &&
                !isClickOnToggle &&
                sidebar.classList.contains("show")
            ) {
                sidebar.classList.remove("show");
            }
        }
    });

    // Initialize date pickers
    const datePickers = document.querySelectorAll(".date-picker");
    datePickers.forEach(function (picker) {
        picker.flatpickr({
            dateFormat: "Y-m-d",
            maxDate: "today",
        });
    });

    // Initialize time pickers
    const timePickers = document.querySelectorAll(".time-picker");
    timePickers.forEach(function (picker) {
        picker.flatpickr({
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
        });
    });

    // Auto-calculate age from date of birth
    const dobInput = document.getElementById("date_of_birth");
    const ageInput = document.getElementById("age");

    if (dobInput && ageInput) {
        dobInput.addEventListener("change", function () {
            const dob = new Date(this.value);
            const today = new Date();
            let age = today.getFullYear() - dob.getFullYear();
            const monthDiff = today.getMonth() - dob.getMonth();

            if (
                monthDiff < 0 ||
                (monthDiff === 0 && today.getDate() < dob.getDate())
            ) {
                age--;
            }

            ageInput.value = age;
        });
    }

    // Patient search functionality
    const patientSearch = document.getElementById("patient_search");
    const patientResults = document.getElementById("patient_results");

    if (patientSearch && patientResults) {
        let searchTimeout;

        patientSearch.addEventListener("input", function () {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length < 2) {
                patientResults.innerHTML = "";
                return;
            }

            searchTimeout = setTimeout(function () {
                searchPatients(query);
            }, 300);
        });
    }

    // Package selection and billing calculation
    const packageSelect = document.getElementById("package_id");
    const billingSummary = document.getElementById("billing_summary");

    if (packageSelect && billingSummary) {
        packageSelect.addEventListener("change", function () {
            const packageId = this.value;
            if (packageId) {
                loadPackageDetails(packageId);
            } else {
                billingSummary.innerHTML = "";
            }
        });
    }

    // Service selection for billing
    const serviceCheckboxes = document.querySelectorAll(
        'input[name="services[]"]',
    );
    if (serviceCheckboxes.length > 0) {
        serviceCheckboxes.forEach(function (checkbox) {
            checkbox.addEventListener("change", updateBillingTotal);
        });
    }

    // Payment form handling
    const paymentForm = document.getElementById("payment_form");
    if (paymentForm) {
        paymentForm.addEventListener("submit", handlePayment);
    }

    // Appointment scheduling
    const appointmentForm = document.getElementById("appointment_form");
    if (appointmentForm) {
        appointmentForm.addEventListener("submit", handleAppointment);
    }

    // Visit form handling - DISABLED to allow Laravel submission
    // const visitForm = document.getElementById("visit_form");
    // if (visitForm) {
    //     visitForm.addEventListener("submit", handleVisit);
    // }

    // Initialize data tables
    const dataTables = document.querySelectorAll(".data-table");
    dataTables.forEach(function (table) {
        new DataTable(table, {
            responsive: true,
            pageLength: 10,
            order: [[0, "desc"]],
        });
    });
});

// Search patients function
function searchPatients(query) {
    // This would typically make an AJAX call to the server
    // For now, we'll simulate with mock data

    const mockPatients = [
        { id: 1, name: "John Doe", phone: "08012345678", code: "P001" },
        { id: 2, name: "Jane Smith", phone: "08023456789", code: "P002" },
        { id: 3, name: "Michael Johnson", phone: "08034567890", code: "P003" },
    ];

    const filtered = mockPatients.filter(
        (patient) =>
            patient.name.toLowerCase().includes(query.toLowerCase()) ||
            patient.phone.includes(query) ||
            patient.code.toLowerCase().includes(query.toLowerCase()),
    );

    displayPatientResults(filtered);
}

function displayPatientResults(patients) {
    const resultsContainer = document.getElementById("patient_results");
    if (!resultsContainer) return;

    if (patients.length === 0) {
        resultsContainer.innerHTML =
            '<div class="alert alert-info">No patients found</div>';
        return;
    }

    let html = '<div class="list-group">';
    patients.forEach(function (patient) {
        html += `
            <div class="list-group-item list-group-item-action patient-result" 
                 data-patient-id="${patient.id}" 
                 data-patient-name="${patient.name}"
                 data-patient-code="${patient.code}">
                <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1">${patient.name}</h6>
                    <small>${patient.code}</small>
                </div>
                <p class="mb-1">${patient.phone}</p>
            </div>
        `;
    });
    html += "</div>";

    resultsContainer.innerHTML = html;

    // Add click handlers to patient results
    document.querySelectorAll(".patient-result").forEach(function (item) {
        item.addEventListener("click", function () {
            selectPatient(this.dataset);
        });
    });
}

function selectPatient(patientData) {
    document.getElementById("patient_id").value = patientData.patientId;
    document.getElementById("patient_name").value = patientData.patientName;
    document.getElementById("patient_code").value = patientData.patientCode;
    document.getElementById("patient_results").innerHTML = "";

    // Load patient details
    loadPatientDetails(patientData.patientId);
}

function loadPatientDetails(patientId) {
    // This would typically make an AJAX call to load patient details
    console.log("Loading details for patient:", patientId);
}

function loadPackageDetails(packageId) {
    // Mock package data
    const packages = {
        1: { name: "Basic Physiotherapy", price: 15000, duration: "4 weeks" },
        2: {
            name: "Comprehensive Wellness",
            price: 25000,
            duration: "8 weeks",
        },
        3: { name: "Female Health Package", price: 20000, duration: "6 weeks" },
    };

    const pkg = packages[packageId];
    if (pkg) {
        displayBillingSummary(pkg);
    }
}

function displayBillingSummary(pkg) {
    const billingSummary = document.getElementById("billing_summary");
    if (!billingSummary) return;

    const html = `
        <div class="billing-summary">
            <h6>Package Details</h6>
            <div class="billing-item">
                <span>${pkg.name}</span>
                <span>₦${pkg.price.toLocaleString()}</span>
            </div>
            <div class="billing-item">
                <span>Duration</span>
                <span>${pkg.duration}</span>
            </div>
            <div class="billing-item">
                <strong>Total Amount</strong>
                <strong>₦${pkg.price.toLocaleString()}</strong>
            </div>
        </div>
    `;

    billingSummary.innerHTML = html;
}

function updateBillingTotal() {
    const checkboxes = document.querySelectorAll(
        'input[name="services[]"]:checked',
    );
    let total = 0;

    checkboxes.forEach(function (checkbox) {
        total += parseFloat(checkbox.dataset.price || 0);
    });

    const totalElement = document.getElementById("total_amount");
    if (totalElement) {
        totalElement.textContent = `₦${total.toLocaleString()}`;
    }
}

function handlePayment(event) {
    event.preventDefault();

    const formData = new FormData(event.target);
    const paymentData = Object.fromEntries(formData);

    // Validate payment amount
    const amountPaid = parseFloat(paymentData.amount_paid);
    const amountOwed = parseFloat(paymentData.amount_owed);

    if (amountPaid > amountOwed) {
        showAlert("Payment amount cannot exceed amount owed", "danger");
        return;
    }

    // Process payment (would typically make AJAX call)
    console.log("Processing payment:", paymentData);
    showAlert("Payment processed successfully", "success");

    // Reset form
    event.target.reset();
}

function handleAppointment(event) {
    event.preventDefault();

    const formData = new FormData(event.target);
    const appointmentData = Object.fromEntries(formData);

    // Validate appointment date/time
    const appointmentDate = new Date(appointmentData.appointment_date);
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    if (appointmentDate < today) {
        showAlert("Appointment date cannot be in the past", "danger");
        return;
    }

    // Process appointment (would typically make AJAX call)
    console.log("Creating appointment:", appointmentData);
    showAlert("Appointment scheduled successfully", "success");

    // Reset form
    event.target.reset();
}

function handleVisit(event) {
    event.preventDefault();

    const formData = new FormData(event.target);
    const visitData = Object.fromEntries(formData);

    // Validate vital signs
    if (
        visitData.temperature &&
        (visitData.temperature < 35 || visitData.temperature > 42)
    ) {
        showAlert("Please enter a valid temperature (35-42°C)", "danger");
        return;
    }

    // Process visit (would typically make AJAX call)
    console.log("Recording visit:", visitData);
    showAlert("Visit recorded successfully", "success");

    // Reset form
    event.target.reset();
}

function showAlert(message, type = "info") {
    const alertContainer = document.getElementById("alert_container");
    if (!alertContainer) return;

    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;

    alertContainer.innerHTML = alertHtml;

    // Auto-dismiss after 5 seconds
    setTimeout(function () {
        const alert = alertContainer.querySelector(".alert");
        if (alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }
    }, 5000);
}

// Utility functions
function formatCurrency(amount) {
    return new Intl.NumberFormat("en-NG", {
        style: "currency",
        currency: "NGN",
    }).format(amount);
}

function formatDate(date) {
    return new Intl.DateTimeFormat("en-NG", {
        year: "numeric",
        month: "short",
        day: "numeric",
    }).format(new Date(date));
}

function formatTime(time) {
    return new Intl.DateTimeFormat("en-NG", {
        hour: "2-digit",
        minute: "2-digit",
    }).format(new Date(`2000-01-01T${time}`));
}

// Export functions for use in other scripts
window.clinicSystem = {
    searchPatients,
    selectPatient,
    loadPatientDetails,
    loadPackageDetails,
    updateBillingTotal,
    showAlert,
    formatCurrency,
    formatDate,
    formatTime,
};
