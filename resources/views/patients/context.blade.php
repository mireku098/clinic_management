@extends('layouts.app')

@section('title', 'Patient Context')

@section('content')
<div class="patient-context-container">
    <!-- Patient Header -->
    <div class="patient-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="patient-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <div class="col patient-info">
                    <h3 id="patientName">{{ $patient->first_name }} {{ $patient->last_name }}</h3>
                    <div class="patient-meta">
                        <span class="badge bg-light text-dark me-2" id="patientId">{{ $patient->patient_code }}</span>
                        <span class="me-2"><i class="fas fa-birthday-cake me-1"></i><span id="patientAge">{{ $patient->age }}</span> years</span>
                        <span class="me-2"><i class="fas fa-venus-mars me-1"></i><span id="patientGender">{{ $patient->gender }}</span></span>
                        <span class="badge bg-success" id="patientStatus">Active</span>
                    </div>
                </div>
                <div class="col-auto patient-actions">
                    <button class="btn btn-light btn-sm" onclick="editPatient()">
                        <i class="fas fa-edit me-1"></i>Edit Patient
                    </button>
                    <button class="btn btn-light btn-sm" onclick="addVisit()">
                        <i class="fas fa-plus me-1"></i>Add Visit
                    </button>
                    <button class="btn btn-outline-light btn-sm" onclick="closePatientContext()">
                        <i class="fas fa-times me-1"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Patient Navigation -->
    <div class="patient-navigation">
        <div class="container-fluid">
            <ul class="nav nav-tabs patient-nav-tabs" id="patientNavTabs">
                <li class="nav-item">
                    <a class="nav-link active" data-module="overview" href="#" onclick="switchModule('overview')">
                        <i class="fas fa-dashboard me-1"></i>Overview
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-module="vitals" href="#" onclick="switchModule('vitals')">
                        <i class="fas fa-heartbeat me-1"></i>Vitals
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-module="service-results" href="#" onclick="switchModule('service-results')">
                        <i class="fas fa-flask me-1"></i>Service Results
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-module="medical-history" href="#" onclick="switchModule('medical-history')">
                        <i class="fas fa-history me-1"></i>Medical History
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-module="prescriptions" href="#" onclick="switchModule('prescriptions')">
                        <i class="fas fa-prescription me-1"></i>Prescriptions
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-module="medications" href="#" onclick="switchModule('medications')">
                        <i class="fas fa-pills me-1"></i>Current Medications
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-module="billing" href="#" onclick="switchModule('billing')">
                        <i class="fas fa-file-invoice-dollar me-1"></i>Billing & Payments
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Patient Content Area -->
    <div class="patient-content">
        <div class="container-fluid">
            <!-- Overview Module -->
            <div id="overview-module" class="module-container active">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-dashboard text-primary me-2"></i>
                                    Patient Overview
                                </h5>
                                <button class="btn btn-sm btn-primary" onclick="refreshOverview()">
                                    <i class="fas fa-refresh me-1"></i>Refresh
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <div class="patient-avatar-large mb-3">
                                                <i class="fas fa-user" style="font-size: 3rem"></i>
                                            </div>
                                            <h6>Registration Date</h6>
                                            <p class="text-muted" id="regDateDisplay">{{ $patient->registered_at ? (is_string($patient->registered_at) ? date('F d, Y', strtotime($patient->registered_at)) : $patient->registered_at->format('F d, Y')) : 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="text-center p-3">
                                                    <i class="fas fa-calendar-check text-success fa-2x mb-2"></i>
                                                    <h6>Total Visits</h6>
                                                    <h4 class="text-success">12</h4>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="text-center p-3">
                                                    <i class="fas fa-file-medical text-info fa-2x mb-2"></i>
                                                    <h6>Lab Reports</h6>
                                                    <h4 class="text-info">8</h4>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="text-center p-3">
                                                    <i class="fas fa-pills text-warning fa-2x mb-2"></i>
                                                    <h6>Active Medications</h6>
                                                    <h4 class="text-warning">3</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vitals Module -->
            <div id="vitals-module" class="module-container">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-heartbeat text-danger me-2"></i>
                            Vitals History
                        </h5>
                        <button class="btn btn-sm btn-primary" onclick="showAddVitalsModal()">
                            <i class="fas fa-plus me-1"></i>Add Vitals
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="vitals-timeline">
                            <div class="vital-entry">
                                <div class="vital-date">January 25, 2024 - 10:30 AM</div>
                                <div class="vital-metrics">
                                    <div class="vital-metric">
                                        <div class="value">120/80</div>
                                        <div class="label">Blood Pressure</div>
                                    </div>
                                    <div class="vital-metric">
                                        <div class="value">72</div>
                                        <div class="label">Heart Rate</div>
                                    </div>
                                    <div class="vital-metric">
                                        <div class="value">98.6</div>
                                        <div class="label">Temperature (°F)</div>
                                    </div>
                                </div>
                            </div>
                            <div class="vital-entry">
                                <div class="vital-date">January 20, 2024 - 2:15 PM</div>
                                <div class="vital-metrics">
                                    <div class="vital-metric">
                                        <div class="value">118/78</div>
                                        <div class="label">Blood Pressure</div>
                                    </div>
                                    <div class="vital-metric">
                                        <div class="value">70</div>
                                        <div class="label">Heart Rate</div>
                                    </div>
                                    <div class="vital-metric">
                                        <div class="value">98.4</div>
                                        <div class="label">Temperature (°F)</div>
                                    </div>
                                </div>
                            </div>
                            <div class="vital-entry">
                                <div class="vital-date">January 15, 2024 - 9:00 AM</div>
                                <div class="vital-metrics">
                                    <div class="vital-metric">
                                        <div class="value">122/82</div>
                                        <div class="label">Blood Pressure</div>
                                    </div>
                                    <div class="vital-metric">
                                        <div class="value">74</div>
                                        <div class="label">Heart Rate</div>
                                    </div>
                                    <div class="vital-metric">
                                        <div class="value">98.7</div>
                                        <div class="label">Temperature (°F)</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Service Results Module -->
            <div id="service-results-module" class="module-container">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-flask text-info me-2"></i>
                            Service Results
                        </h5>
                        <small class="text-muted">Results are grouped by visit date</small>
                    </div>
                    <div class="card-body">
                        <div class="lab-reports-grid" id="serviceResultsGrid">
                            <!-- Service results will be loaded dynamically -->
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                                <p>Loading service results...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medical History Module -->
            <div id="medical-history-module" class="module-container">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-history text-secondary me-2"></i>
                            Medical History
                        </h5>
                        <button class="btn btn-sm btn-primary" onclick="showAddMedicalHistoryModal()">
                            <i class="fas fa-plus me-1"></i>Add Record
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Chronic Conditions</h6>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Hypertension
                                        <span class="badge bg-warning">Diagnosed 2020</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Type 2 Diabetes
                                        <span class="badge bg-warning">Diagnosed 2019</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Allergies</h6>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Penicillin
                                        <span class="badge bg-danger">Severe</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Peanuts
                                        <span class="badge bg-warning">Moderate</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <h6>Surgeries</h6>
                                <div class="timeline">
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-primary"></div>
                                        <div class="timeline-content">
                                            <strong>Appendectomy</strong>
                                            <small class="text-muted d-block">March 15, 2018</small>
                                            <p>Laparoscopic appendectomy performed at City General Hospital</p>
                                        </div>
                                    </div>
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-success"></div>
                                        <div class="timeline-content">
                                            <strong>Gallbladder Removal</strong>
                                            <small class="text-muted d-block">June 10, 2020</small>
                                            <p>Laparoscopic cholecystectomy performed at Renew Wellness</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prescriptions Module -->
            <div id="prescriptions-module" class="module-container">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-prescription text-success me-2"></i>
                            Prescription History
                        </h5>
                        <button class="btn btn-sm btn-primary" onclick="showAddPrescriptionModal()">
                            <i class="fas fa-plus me-1"></i>New Prescription
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Medication</th>
                                        <th>Dosage</th>
                                        <th>Duration</th>
                                        <th>Prescribed By</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Jan 25, 2024</td>
                                        <td>Lisinopril</td>
                                        <td>10mg daily</td>
                                        <td>30 days</td>
                                        <td>Dr. Sarah Johnson</td>
                                        <td><span class="badge bg-success">Active</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="viewPrescription('PR001')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Jan 20, 2024</td>
                                        <td>Metformin</td>
                                        <td>500mg twice daily</td>
                                        <td>90 days</td>
                                        <td>Dr. Michael Chen</td>
                                        <td><span class="badge bg-success">Active</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="viewPrescription('PR002')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Jan 15, 2024</td>
                                        <td>Amoxicillin</td>
                                        <td>500mg three times daily</td>
                                        <td>7 days</td>
                                        <td>Dr. Sarah Johnson</td>
                                        <td><span class="badge bg-secondary">Completed</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="viewPrescription('PR003')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Billing & Payments Module -->
            <div id="billing-module" class="module-container">
                <!-- Billing Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">Total Billed</h6>
                                        <h3 class="mb-0">$1,400.00</h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-file-invoice-dollar fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">Total Paid</h6>
                                        <h3 class="mb-0">$350.00</h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-money-bill-wave fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">Outstanding</h6>
                                        <h3 class="mb-0">$1,050.00</h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">Active Bills</h6>
                                        <h3 class="mb-0">3</h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-list fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bills Table -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-file-invoice-dollar text-primary me-2"></i>
                            Patient Bills
                        </h5>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-primary" onclick="showCreateBillModal()">
                                <i class="fas fa-plus me-1"></i>Create Bill
                            </button>
                            <button class="btn btn-outline-secondary" onclick="refreshBills()">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Bill ID</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Total Amount</th>
                                        <th>Amount Paid</th>
                                        <th>Balance</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="billsTableBody">
                                    <tr>
                                        <td><span class="badge bg-primary fs-6">B001</span></td>
                                        <td><span class="badge bg-info">Package</span></td>
                                        <td>Jan 25, 2024</td>
                                        <td class="fw-bold text-primary">$500.00</td>
                                        <td class="text-success fw-semibold">$200.00</td>
                                        <td class="text-warning fw-bold">$300.00</td>
                                        <td>
                                            <span class="badge bg-warning">Partially Paid</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button class="btn btn-outline-primary" onclick="viewBillDetails('B001')" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-success" onclick="showPaymentModal('B001')" title="Make Payment">
                                                    <i class="fas fa-money-bill-wave"></i>
                                                </button>
                                                <button class="btn btn-outline-info" onclick="printBill('B001')" title="Print Bill">
                                                    <i class="fas fa-print"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-primary fs-6">B002</span></td>
                                        <td><span class="badge bg-secondary">Service</span></td>
                                        <td>Jan 20, 2024</td>
                                        <td class="fw-bold text-primary">$150.00</td>
                                        <td class="text-success fw-semibold">$150.00</td>
                                        <td class="text-success fw-bold">$0.00</td>
                                        <td>
                                            <span class="badge bg-success">Fully Paid</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button class="btn btn-outline-primary" onclick="viewBillDetails('B002')" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-success" disabled title="Already Paid">
                                                    <i class="fas fa-money-bill-wave"></i>
                                                </button>
                                                <button class="btn btn-outline-info" onclick="printBill('B002')" title="Print Bill">
                                                    <i class="fas fa-print"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-primary fs-6">B003</span></td>
                                        <td><span class="badge bg-warning text-dark">Combined</span></td>
                                        <td>Jan 15, 2024</td>
                                        <td class="fw-bold text-primary">$750.00</td>
                                        <td class="text-success fw-semibold">$0.00</td>
                                        <td class="text-danger fw-bold">$750.00</td>
                                        <td><span class="badge bg-danger">Unpaid</span></td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button class="btn btn-outline-primary" onclick="viewBillDetails('B003')" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-success" onclick="showPaymentModal('B003')" title="Make Payment">
                                                    <i class="fas fa-money-bill-wave"></i>
                                                </button>
                                                <button class="btn btn-outline-info" onclick="printBill('B003')" title="Print Bill">
                                                    <i class="fas fa-print"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Recent Payments -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-receipt text-success me-2"></i>
                            Recent Payments
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Payment ID</th>
                                        <th>Bill ID</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="badge bg-success">P001</span></td>
                                        <td>B001</td>
                                        <td class="text-success fw-bold">$200.00</td>
                                        <td><span class="badge bg-info">Credit Card</span></td>
                                        <td>Jan 25, 2024</td>
                                        <td><span class="badge bg-success">Completed</span></td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-success">P002</span></td>
                                        <td>B002</td>
                                        <td class="text-success fw-bold">$150.00</td>
                                        <td><span class="badge bg-secondary">Cash</span></td>
                                        <td>Jan 20, 2024</td>
                                        <td><span class="badge bg-success">Completed</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medications Module -->
            <div id="medications-module" class="module-container">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-pills text-warning me-2"></i>
                            Current Medications
                        </h5>
                        <button class="btn btn-sm btn-primary" onclick="showAddMedicationModal()">
                            <i class="fas fa-plus me-1"></i>Add Medication
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="medication-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="medication-name">Lisinopril</div>
                                    <div class="dosage-info">
                                        <span><i class="fas fa-pills me-1"></i>10mg</span>
                                        <span><i class="fas fa-clock me-1"></i>Once daily</span>
                                        <span><i class="fas fa-calendar me-1"></i>Started: Jan 25, 2024</span>
                                    </div>
                                    <small class="text-muted">For: Hypertension</small>
                                </div>
                                <div>
                                    <span class="medication-status active">Active</span>
                                    <div class="btn-group btn-group-sm mt-2">
                                        <button class="btn btn-outline-primary" onclick="editMedication('MED001')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-warning" onclick="discontinueMedication('MED001')">
                                            <i class="fas fa-stop"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="medication-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="medication-name">Metformin</div>
                                    <div class="dosage-info">
                                        <span><i class="fas fa-pills me-1"></i>500mg</span>
                                        <span><i class="fas fa-clock me-1"></i>Twice daily</span>
                                        <span><i class="fas fa-calendar me-1"></i>Started: Jan 20, 2024</span>
                                    </div>
                                    <small class="text-muted">For: Type 2 Diabetes</small>
                                </div>
                                <div>
                                    <span class="medication-status active">Active</span>
                                    <div class="btn-group btn-group-sm mt-2">
                                        <button class="btn btn-outline-primary" onclick="editMedication('MED002')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-warning" onclick="discontinueMedication('MED002')">
                                            <i class="fas fa-stop"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="medication-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="medication-name">Atorvastatin</div>
                                    <div class="dosage-info">
                                        <span><i class="fas fa-pills me-1"></i>20mg</span>
                                        <span><i class="fas fa-clock me-1"></i>Once daily</span>
                                        <span><i class="fas fa-calendar me-1"></i>Started: Jan 15, 2024</span>
                                    </div>
                                    <small class="text-muted">For: High Cholesterol</small>
                                </div>
                                <div>
                                    <span class="medication-status active">Active</span>
                                    <div class="btn-group btn-group-sm mt-2">
                                        <button class="btn btn-outline-primary" onclick="editMedication('MED003')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-warning" onclick="discontinueMedication('MED003')">
                                            <i class="fas fa-stop"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View/Edit Service Result Modal -->
<div class="modal fade" id="viewServiceResultModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Service Result Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="serviceResultViewContent">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editResultBtn" style="display: none;" onclick="editServiceResult()">
                    <i class="fas fa-edit me-1"></i>Edit Result
                </button>
                <button type="button" class="btn btn-success" id="saveResultBtn" style="display: none;" onclick="saveServiceResultEdit()">
                    <i class="fas fa-save me-1"></i>Save Changes
                </button>
                <button type="button" class="btn btn-warning" id="cancelEditBtn" style="display: none;" onclick="cancelServiceResultEdit()">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Medical History Modal -->
<div class="modal fade" id="addMedicalHistoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Medical History Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="medicalHistoryForm">
                    <div class="mb-3">
                        <label class="form-label">Record Type</label>
                        <select class="form-select" id="historyType" required>
                            <option value="">Select Type</option>
                            <option value="condition">Chronic Condition</option>
                            <option value="allergy">Allergy</option>
                            <option value="surgery">Surgery</option>
                            <option value="hospitalization">Hospitalization</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Condition/Procedure</label>
                        <input type="text" class="form-control" id="conditionName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" class="form-control" id="conditionDate" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Details</label>
                        <textarea class="form-control" id="conditionDetails" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveMedicalHistory()">Save Record</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Prescription Modal -->
<div class="modal fade" id="addPrescriptionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Prescription</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="prescriptionForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Medication Name</label>
                                <input type="text" class="form-control" id="medicationName" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Dosage</label>
                                <input type="text" class="form-control" id="prescriptionDosage" placeholder="e.g., 10mg" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Frequency</label>
                                <input type="text" class="form-control" id="frequency" placeholder="e.g., Once daily" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Duration</label>
                                <input type="text" class="form-control" id="duration" placeholder="e.g., 30 days" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Instructions</label>
                        <textarea class="form-control" id="instructions" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="savePrescription()">Save Prescription</button>
            </div>
        </div>
    </div>
</div>

<!-- Bill Details Modal -->
<div class="modal fade" id="billDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bill Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6>Bill Information</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Bill ID:</strong></td>
                                <td id="modalBillId">B001</td>
                            </tr>
                            <tr>
                                <td><strong>Type:</strong></td>
                                <td id="modalBillType">Package</td>
                            </tr>
                            <tr>
                                <td><strong>Date:</strong></td>
                                <td id="modalBillDate">January 25, 2024</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td id="modalBillStatus"><span class="badge bg-warning">Partially Paid</span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Financial Summary</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Total Amount:</strong></td>
                                <td class="fw-bold text-primary" id="modalTotalAmount">$500.00</td>
                            </tr>
                            <tr>
                                <td><strong>Amount Paid:</strong></td>
                                <td class="text-success" id="modalAmountPaid">$200.00</td>
                            </tr>
                            <tr>
                                <td><strong>Balance:</strong></td>
                                <td class="fw-bold text-warning" id="modalBalance">$300.00</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <h6>Bill Items</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody id="modalBillItems">
                            <tr>
                                <td>Basic Health Package</td>
                                <td>1</td>
                                <td>$500.00</td>
                                <td>$500.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <h6>Payment History</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Payment ID</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Balance Before</th>
                                <th>Balance After</th>
                            </tr>
                        </thead>
                        <tbody id="modalPaymentHistory">
                            <tr>
                                <td><span class="badge bg-success">P001</span></td>
                                <td>Jan 26, 2024</td>
                                <td class="text-success">$200.00</td>
                                <td>Cash</td>
                                <td>$500.00</td>
                                <td>$300.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="modalPaymentBtn" onclick="showPaymentModalFromDetails()">Make Payment</button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Make Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="paymentForm">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Bill ID:</strong> <span id="paymentBillId">B001</span><br>
                        <strong>Current Balance:</strong> <span id="paymentBalance" class="fw-bold text-warning">$300.00</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Amount ($)</label>
                        <input type="number" class="form-control" id="paymentAmount" step="0.01" min="0.01" required>
                        <small class="form-text text-muted">Enter amount to pay (partial payments allowed)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select class="form-select" id="paymentMethod" required>
                            <option value="">Select Payment Method</option>
                            <option value="cash">Cash</option>
                            <option value="card">Credit/Debit Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="insurance">Insurance</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Reference (Optional)</label>
                        <input type="text" class="form-control" id="paymentReference" placeholder="Transaction ID, Check number, etc.">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" id="paymentNotes" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="processPayment()">Process Payment</button>
            </div>
        </div>
    </div>
</div>

<!-- Create Bill Modal -->
<div class="modal fade" id="createBillModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Bill</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createBillForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Bill Type</label>
                                <select class="form-select" id="billType" required>
                                    <option value="">Select Bill Type</option>
                                    <option value="package">Package</option>
                                    <option value="service">Service</option>
                                    <option value="combined">Combined</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Bill Date</label>
                                <input type="date" class="form-control" id="billDate" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bill Items</label>
                        <div id="billItemsContainer">
                            <div class="row bill-item-row">
                                <div class="col-md-5">
                                    <input type="text" class="form-control" placeholder="Description" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" class="form-control" placeholder="Qty" min="1" value="1" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" class="form-control" placeholder="Price" step="0.01" min="0" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" placeholder="Total" readonly>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeBillItem(this)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addBillItem()">
                            <i class="fas fa-plus me-1"></i>Add Item
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Total Amount</label>
                                <input type="number" class="form-control fw-bold" id="totalBillAmount" step="0.01" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" id="billNotes" rows="1"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="createBill()">Create Bill</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Medication Modal -->
<div class="modal fade" id="addMedicationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Current Medication</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="medicationForm">
                    <div class="mb-3">
                        <label class="form-label">Medication Name</label>
                        <input type="text" class="form-control" id="currentMedicationName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dosage</label>
                        <input type="text" class="form-control" id="medicationDosage" placeholder="e.g., 10mg" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Frequency</label>
                        <input type="text" class="form-control" id="medicationFrequency" placeholder="e.g., Once daily" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="startDate" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <input type="text" class="form-control" id="medicationReason" placeholder="e.g., Hypertension">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveMedication()">Add Medication</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Vitals Modal -->
<div class="modal fade" id="addVitalsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Vitals</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="vitalsForm">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Blood Pressure</label>
                                <input type="text" class="form-control" id="bloodPressure" placeholder="120/80">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Heart Rate</label>
                                <input type="number" class="form-control" id="heartRate" placeholder="72">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Temperature (°F)</label>
                                <input type="number" step="0.1" class="form-control" id="temperature" placeholder="98.6">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveVitals()">Save Vitals</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
.patient-context-container {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    background: #f8f9fa;
}

.patient-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 0;
    z-index: 100;
}

.patient-header .patient-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #667eea;
    border: 3px solid rgba(255, 255, 255, 0.3);
}

.patient-navigation {
    background: white;
    border-bottom: 1px solid #e2e8f0;
}

.patient-nav-tabs {
    border: none;
    flex-wrap: nowrap;
    overflow-x: auto;
}

.patient-nav-tabs .nav-link {
    border: none;
    color: #64748b;
    padding: 1rem 1.5rem;
    font-weight: 500;
    white-space: nowrap;
    border-bottom: 3px solid transparent;
}

.patient-nav-tabs .nav-link.active {
    color: #667eea;
    background: rgba(102, 126, 234, 0.1);
    border-bottom-color: #667eea;
}

.patient-content {
    flex: 1;
    padding: 2rem 0;
}

.module-container {
    display: none;
    animation: fadeIn 0.3s ease-in-out;
}

.module-container.active {
    display: block;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.vitals-timeline {
    border-left: 3px solid #e2e8f0;
    padding-left: 20px;
}

.vital-entry {
    margin-bottom: 1.5rem;
}

.vital-date {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.vital-metrics {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.vital-metric {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 0.5rem;
    min-width: 140px;
    text-align: center;
}

.vital-metric .value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #667eea;
}

.lab-reports-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1rem;
}

.lab-report-card {
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    padding: 1rem;
    cursor: pointer;
    transition: transform 0.2s ease;
    background: white;
}

.lab-report-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 20px rgba(102, 126, 234, 0.15);
}

.report-status {
    display: inline-block;
    padding: 0.2rem 0.6rem;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 600;
}

.report-status.normal {
    background: rgba(16, 185, 129, 0.15);
    color: #0f9d58;
}

.report-status.abnormal {
    background: rgba(236, 72, 153, 0.15);
    color: #b83280;
}

.report-status.pending {
    background: rgba(251, 191, 36, 0.15);
    color: #b7791f;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e2e8f0;
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
}

.timeline-marker {
    position: absolute;
    left: -25px;
    top: 5px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 2px solid white;
}

.timeline-content {
    background: white;
    padding: 1rem;
    border-radius: 0.5rem;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.medication-card {
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    padding: 1rem;
    margin-bottom: 1rem;
}

.medication-name {
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 0.25rem;
}

.dosage-info {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    font-size: 0.9rem;
    color: #64748b;
}

.medication-status {
    display: inline-block;
    padding: 0.2rem 0.75rem;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 600;
}

.medication-status.active {
    background: rgba(16, 185, 129, 0.15);
    color: #0f9d58;
}
</style>
@endsection

@section('js')
<script>
const patientIdFromServer = '{{ $patientId ?? '' }}';
const patientsRoute = "{{ route('patients') }}";
const editPatientRouteTemplate = "{{ route('patients.edit') }}?code=:id";
const addVisitRoute = "{{ route('visits.add') }}?patient=:id";

let currentPatientId = null;
let currentModule = "overview";

// Patient data passed from server
const patientData = @json($patient->toArray());

document.addEventListener("DOMContentLoaded", () => {
    const urlParams = new URLSearchParams(window.location.search);
    const fallbackPatient = urlParams.get("patient") || "P001";
    const module = urlParams.get("module");
    currentPatientId = patientIdFromServer || fallbackPatient;

    initializePatientContext(currentPatientId);

    if (module && module !== "overview") {
        switchModule(module);
    }
});

function initializePatientContext(patientId) {
    currentPatientId = patientId;
    loadPatientData(patientId);
    switchModule("overview");
}

function loadPatientData(patientId) {
    // Use the real patient data passed from server
    if (patientData) {
        document.getElementById("patientName").textContent = `${patientData.first_name} ${patientData.last_name}`;
        document.getElementById("patientId").textContent = patientData.patient_code;
        document.getElementById("patientAge").textContent = patientData.age;
        document.getElementById("patientGender").textContent = patientData.gender;
        document.getElementById("patientStatus").textContent = "Active";
        document.getElementById("patientStatus").className = "badge bg-success";
        
        const regDateElement = document.getElementById("regDateDisplay");
        if (patientData.registered_at) {
            let regDate;
            if (typeof patientData.registered_at === 'string') {
                regDate = new Date(patientData.registered_at);
            } else {
                regDate = patientData.registered_at;
            }
            
            if (!isNaN(regDate.getTime())) {
                regDateElement.textContent = regDate.toLocaleDateString("en-US", {
                    year: "numeric",
                    month: "long",
                    day: "numeric"
                });
            }
        }
    }
}

function switchModule(moduleName) {
    event?.preventDefault();
    document.querySelectorAll(".patient-nav-tabs .nav-link").forEach(link => link.classList.remove("active"));
    const targetNav = document.querySelector(`[data-module="${moduleName}"]`);
    if (targetNav) {
        targetNav.classList.add("active");
    }

    document.querySelectorAll(".module-container").forEach(container => container.classList.remove("active"));
    const targetModule = document.getElementById(`${moduleName}-module`);
    if (targetModule) {
        targetModule.classList.add("active");
    }

    currentModule = moduleName;
    loadModuleData(moduleName);
}

function loadModuleData(moduleName) {
    Swal.fire({
        title: "Loading...",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    // Make API call based on module
    let apiUrl = `/api/patients/${currentPatientId}/${moduleName}`;
    
    // Map module names to API endpoints
    const endpointMap = {
        'overview': 'overview',
        'vitals': 'vitals',
        'billing': 'billing',
        'medical-history': 'medical-history'
    };
    
    if (endpointMap[moduleName]) {
        apiUrl = `/api/patients/${currentPatientId}/${endpointMap[moduleName]}`;
    }
    
    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            Swal.close();
            if (data.success) {
                updateModuleContent(moduleName, data.data);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Failed to load data'
                });
            }
        })
        .catch(error => {
            Swal.close();
            console.error('Error loading module data:', error);
            // Keep showing static data for now if API fails
        });
}

function updateModuleContent(moduleName, data) {
    switch(moduleName) {
        case 'overview':
            updateOverviewModule(data);
            break;
        case 'vitals':
            updateVitalsModule(data);
            break;
        case 'billing':
            updateBillingModule(data);
            break;
        case 'medical-history':
            updateMedicalHistoryModule(data);
            break;
        default:
            console.log('No update handler for module:', moduleName);
    }
}

function updateOverviewModule(data) {
    // Update the overview statistics
    const totalVisitsElement = document.querySelector('#overview-module .text-success h4');
    const totalBillsElement = document.querySelector('#overview-module .text-info h4');
    const outstandingElement = document.querySelector('#overview-module .text-warning h4');
    
    if (totalVisitsElement) totalVisitsElement.textContent = data.total_visits || 0;
    if (totalBillsElement) totalBillsElement.textContent = data.active_services_count || 0;
    if (outstandingElement) outstandingElement.textContent = `GH₵${data.outstanding_balance || '0.00'}`;
    
    // Update last visit info
    const lastVisitElement = document.querySelector('#overview-module .last-visit');
    if (lastVisitElement) {
        lastVisitElement.innerHTML = `
            <strong>Last Visit:</strong> ${data.last_visit_date}<br>
            <strong>Type:</strong> ${data.last_visit_type || 'N/A'}
        `;
    }
    
    // Update active services
    const activeServicesElement = document.querySelector('#overview-module .active-services');
    if (activeServicesElement && data.active_services) {
        activeServicesElement.innerHTML = data.active_services.map(service => 
            `<span class="badge bg-light text-dark me-1">${service.service_name}</span>`
        ).join('');
    }
    
    // Update active packages
    const activePackagesElement = document.querySelector('#overview-module .active-packages');
    if (activePackagesElement && data.active_packages) {
        activePackagesElement.innerHTML = data.active_packages.map(pkg => 
            `<span class="badge bg-primary me-1">${pkg.package_name}</span>`
        ).join('');
    }
}

function updateVitalsModule(data) {
    const vitalsTableBody = document.querySelector('#vitals-module tbody');
    
    if (!vitalsTableBody) return;
    
    if (!data.has_vitals) {
        vitalsTableBody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center text-muted">
                    <i class="fas fa-heartbeat me-2"></i>
                    No vitals recorded for this patient
                </td>
            </tr>
        `;
        return;
    }
    
    vitalsTableBody.innerHTML = data.vitals.map(vital => `
        <tr>
            <td>${vital.visit_date}</td>
            <td>${vital.visit_time || 'N/A'}</td>
            <td>${vital.temperature || 'N/A'}</td>
            <td>${vital.blood_pressure || 'N/A'}</td>
            <td>${vital.heart_rate || 'N/A'}</td>
            <td>${vital.oxygen_saturation || 'N/A'}</td>
            <td>${vital.respiratory_rate || 'N/A'}</td>
            <td>${vital.weight || 'N/A'}</td>
            <td>${vital.height || 'N/A'}</td>
            <td>${vital.bmi || 'N/A'}</td>
        </tr>
    `).join('');
}

function updateMedicalHistoryModule(data) {
    const historyTableBody = document.querySelector('#medical-history-module tbody');
    
    if (!historyTableBody) return;
    
    if (!data.has_history) {
        historyTableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-muted">
                    <i class="fas fa-history me-2"></i>
                    No medical history recorded for this patient
                </td>
            </tr>
        `;
        return;
    }
    
    historyTableBody.innerHTML = data.medical_history.map(visit => `
        <tr>
            <td>${visit.visit_date}</td>
            <td>${visit.chief_complaint || 'N/A'}</td>
            <td>${visit.assessment || 'N/A'}</td>
            <td>${visit.treatment_plan || 'N/A'}</td>
            <td>
                ${visit.services.length > 0 ? visit.services.map(s => 
                    `<span class="badge bg-light text-dark me-1">${s.name}</span>`
                ).join('') : 'No services'}
                ${visit.package ? `<span class="badge bg-primary">${visit.package.name}</span>` : ''}
            </td>
            <td>
                <button class="btn btn-sm btn-outline-info" onclick="viewVisitDetails(${visit.visit_id})">
                    <i class="fas fa-eye"></i> Details
                </button>
            </td>
        </tr>
    `).join('');
}

function updateBillingModule(data) {
    // Update billing summary cards
    const totalBilledElement = document.querySelector('.bg-primary h3');
    const totalPaidElement = document.querySelector('.bg-success h3');
    const outstandingElement = document.querySelector('.bg-warning h3');
    const activeBillsElement = document.querySelector('.bg-info h3');
    
    if (totalBilledElement) totalBilledElement.textContent = `GH₵${data.total_billed || '0.00'}`;
    if (totalPaidElement) totalPaidElement.textContent = `GH₵${data.total_paid || '0.00'}`;
    if (outstandingElement) outstandingElement.textContent = `GH₵${data.outstanding_balance || '0.00'}`;
    if (activeBillsElement) activeBillsElement.textContent = data.payment_summary.pending_bills || 0;
    
    // Update bills table
    const billsTableBody = document.getElementById('billsTableBody');
    if (billsTableBody && data.bills) {
        billsTableBody.innerHTML = data.bills.map(bill => {
            const statusClass = bill.status === 'paid' ? 'success' : bill.status === 'partial' ? 'warning' : 'danger';
            const statusText = bill.status === 'paid' ? 'Fully Paid' : bill.status === 'partial' ? 'Partially Paid' : 'Unpaid';
            
            return `
                <tr>
                    <td><span class="badge bg-primary fs-6">B${bill.bill_id}</span></td>
                    <td><span class="badge bg-info">${bill.bill_type}</span></td>
                    <td>${bill.created_at}</td>
                    <td class="fw-bold text-primary">GH₵${bill.total_amount}</td>
                    <td class="fw-bold text-success">GH₵${bill.amount_paid}</td>
                    <td class="fw-bold text-warning">GH₵${bill.balance}</td>
                    <td><span class="badge bg-${statusClass}">${statusText}</span></td>
                    <td>
                        <button class="btn btn-outline-primary btn-sm me-1" onclick="viewBillDetails(${bill.bill_id})" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${bill.balance > 0 ? `
                            <button class="btn btn-outline-success btn-sm" onclick="makePayment(${bill.bill_id})" title="Make Payment">
                                <i class="fas fa-money-bill-wave"></i>
                            </button>
                        ` : ''}
                    </td>
                </tr>
            `;
        }).join('');
    }
    
    // Update payment history
    const paymentHistoryBody = document.getElementById('paymentHistoryBody');
    if (paymentHistoryBody && data.bills) {
        const allPayments = data.bills.flatMap(bill => bill.payments || []);
        
        if (allPayments.length === 0) {
            paymentHistoryBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-muted">No payments recorded</td>
                </tr>
            `;
        } else {
            paymentHistoryBody.innerHTML = allPayments.map(payment => `
                <tr>
                    <td><span class="badge bg-success">P${payment.payment_id}</span></td>
                    <td>${payment.payment_date}</td>
                    <td>GH₵${payment.amount_paid}</td>
                    <td>${payment.payment_method}</td>
                    <td>${payment.received_by}</td>
                    <td>${payment.notes || '-'}</td>
                </tr>
            `).join('');
        }
    }
}

function updateVisitsModule(data) {
    // Implementation for visits module would go here
    console.log('Visits data:', data);
}

function updateServiceResultsModule(data) {
    const serviceResultsGrid = document.getElementById('serviceResultsGrid');
    
    if (!serviceResultsGrid) return;
    
    if (data && data.length > 0) {
        // Group results by visit
        const groupedResults = {};
        data.forEach(result => {
            const visitId = result.visit_id || 'no-visit';
            const visitDate = result.visit && result.visit.visit_date ? 
                new Date(result.visit.visit_date).toLocaleDateString('en-US', { 
                    month: 'short', 
                    day: 'numeric', 
                    year: 'numeric' 
                }) : 'No Visit';
            
            if (!groupedResults[visitId]) {
                groupedResults[visitId] = {
                    visitDate: visitDate,
                    visitId: visitId,
                    results: []
                };
            }
            groupedResults[visitId].results.push(result);
        });
        
        // Generate HTML for grouped results using original lab-report-card styling
        let html = '';
        Object.values(groupedResults).forEach(group => {
            // Add visit header
            html += `
                <div class="mb-3">
                    <h6 class="text-muted">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Visit: ${group.visitDate}
                        <small class="ms-2">(${group.results.length} results)</small>
                    </h6>
                </div>
            `;
            
            // Add result cards using original lab-report-card styling
            group.results.forEach(result => {
                const statusClass = result.status === 'approved' ? 'normal' : result.status === 'draft' ? 'abnormal' : 'pending';
                const serviceName = result.service ? result.service.service_name : 'Unknown Service';
                const resultDate = result.created_at ? new Date(result.created_at).toLocaleDateString('en-US', { 
                    month: 'short', 
                    day: 'numeric', 
                    year: 'numeric' 
                }) : 'N/A';
                
                // Get the appropriate result value
                let resultValue = 'N/A';
                if (result.result_type === 'text') {
                    resultValue = result.result_text || 'N/A';
                } else if (result.result_type === 'numeric') {
                    resultValue = result.result_numeric || 'N/A';
                } else if (result.result_type === 'file') {
                    resultValue = result.result_file_name || 'File uploaded';
                }
                
                html += `
                    <div class="lab-report-card" onclick="viewServiceResult(${result.id})">
                        <div class="report-type">${serviceName}</div>
                        <div class="report-date">${resultDate}</div>
                        <div class="report-status ${statusClass}">${result.status}</div>
                        <div class="mt-2">
                            <small class="text-muted">Type: ${result.result_type}</small><br>
                            <small class="text-muted">Value: ${resultValue}</small>
                        </div>
                    </div>
                `;
            });
        });
        
        serviceResultsGrid.innerHTML = html;
    } else {
        serviceResultsGrid.innerHTML = `
            <div class="text-center text-muted py-4">
                <i class="fas fa-flask fa-2x mb-2"></i>
                <p>No service results found for this patient</p>
                <small class="text-muted">Results are added when services are performed during visits</small>
            </div>
        `;
    }
}

let currentServiceResult = null;

function viewServiceResult(resultId) {
    // Fetch service result details
    fetch(`/api/service-results/${resultId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentServiceResult = data.data;
                displayServiceResultDetails(data.data);
                new bootstrap.Modal(document.getElementById("viewServiceResultModal")).show();
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: data.message || "Failed to load service result"
                });
            }
        })
        .catch(error => {
            console.error('Error loading service result:', error);
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Failed to load service result"
            });
        });
}

function displayServiceResultDetails(result) {
    const content = document.getElementById('serviceResultViewContent');
    const editBtn = document.getElementById('editResultBtn');
    const saveBtn = document.getElementById('saveResultBtn');
    const cancelBtn = document.getElementById('cancelEditBtn');
    
    // Reset buttons
    editBtn.style.display = 'none';
    saveBtn.style.display = 'none';
    cancelBtn.style.display = 'none';
    
    // Get result value based on type
    let resultValue = 'N/A';
    if (result.result_type === 'text') {
        resultValue = result.result_text || 'N/A';
    } else if (result.result_type === 'numeric') {
        resultValue = result.result_numeric || 'N/A';
    } else if (result.result_type === 'file') {
        resultValue = result.result_file_name || 'File uploaded';
    }
    
    // Show edit button only for draft status
    if (result.status === 'draft') {
        editBtn.style.display = 'inline-block';
    }
    
    content.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6>Service Information</h6>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Service:</strong></td>
                        <td>${result.service ? result.service.service_name : 'Unknown Service'}</td>
                    </tr>
                    <tr>
                        <td><strong>Result Type:</strong></td>
                        <td>${result.result_type}</td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td><span class="badge bg-${result.status === 'approved' ? 'success' : result.status === 'draft' ? 'warning' : 'secondary'}">${result.status}</span></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Visit Information</h6>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Visit Date:</strong></td>
                        <td>${result.visit && result.visit.visit_date ? new Date(result.visit.visit_date).toLocaleDateString() : 'N/A'}</td>
                    </tr>
                    <tr>
                        <td><strong>Recorded By:</strong></td>
                        <td>${result.recorder ? result.recorder.name : 'N/A'}</td>
                    </tr>
                    <tr>
                        <td><strong>Recorded At:</strong></td>
                        <td>${result.recorded_at ? new Date(result.recorded_at).toLocaleString() : 'N/A'}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <h6>Result Value</h6>
                <div class="border rounded p-3 bg-light">
                    ${result.result_type === 'file' && result.result_file_path ? 
                        `<a href="/storage/${result.result_file_path}" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-download me-1"></i>Download File
                        </a>` : 
                        `<p class="mb-0">${resultValue}</p>`
                    }
                </div>
            </div>
        </div>
        ${result.notes ? `
        <div class="row mt-3">
            <div class="col-12">
                <h6>Notes</h6>
                <div class="border rounded p-3 bg-light">
                    <p class="mb-0">${result.notes}</p>
                </div>
            </div>
        </div>
        ` : ''}
    `;
}

function editServiceResult() {
    if (!currentServiceResult) return;
    
    const content = document.getElementById('serviceResultViewContent');
    const editBtn = document.getElementById('editResultBtn');
    const saveBtn = document.getElementById('saveResultBtn');
    const cancelBtn = document.getElementById('cancelEditBtn');
    
    // Show save/cancel buttons, hide edit button
    editBtn.style.display = 'none';
    saveBtn.style.display = 'inline-block';
    cancelBtn.style.display = 'inline-block';
    
    // Get current result value
    let currentValue = '';
    if (currentServiceResult.result_type === 'text') {
        currentValue = currentServiceResult.result_text || '';
    } else if (currentServiceResult.result_type === 'numeric') {
        currentValue = currentServiceResult.result_numeric || '';
    } else if (currentServiceResult.result_type === 'file') {
        currentValue = currentServiceResult.result_file_name || '';
    }
    
    content.innerHTML = `
        <form id="editServiceResultForm">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Service</label>
                        <input type="text" class="form-control" value="${currentServiceResult.service ? currentServiceResult.service.service_name : 'Unknown Service'}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Result Type</label>
                        <input type="text" class="form-control" value="${currentServiceResult.result_type}" readonly>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Result Value</label>
                        ${currentServiceResult.result_type === 'text' ? 
                            `<textarea class="form-control" id="editResultValue" rows="3">${currentValue}</textarea>` :
                            currentServiceResult.result_type === 'numeric' ?
                            `<input type="number" class="form-control" id="editResultValue" value="${currentValue}" step="any">` :
                            `<div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                File uploads cannot be edited. Please upload a new file if needed.
                            </div>
                            <input type="text" class="form-control" value="${currentValue}" readonly>`
                        }
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="editResultStatus">
                            <option value="draft" ${currentServiceResult.status === 'draft' ? 'selected' : ''}>Draft</option>
                            <option value="approved" ${currentServiceResult.status === 'approved' ? 'selected' : ''}>Approved</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea class="form-control" id="editResultNotes" rows="3">${currentServiceResult.notes || ''}</textarea>
            </div>
        </form>
    `;
}

function saveServiceResultEdit() {
    if (!currentServiceResult) return;
    
    const form = document.getElementById('editServiceResultForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const formData = new FormData();
    formData.append('result_id', currentServiceResult.id);
    formData.append('status', document.getElementById('editResultStatus').value);
    formData.append('notes', document.getElementById('editResultNotes').value);
    
    // Only include result value if it's editable (not file type)
    if (currentServiceResult.result_type !== 'file') {
        formData.append('result_value', document.getElementById('editResultValue').value);
    }
    
    Swal.fire({
        title: "Saving Result...",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    
    fetch('/api/service-results/update', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        if (data.success) {
            Swal.fire({
                icon: "success",
                title: "Result Updated",
                text: "Service result has been updated successfully",
                timer: 2000,
                showConfirmButton: false
            });
            bootstrap.Modal.getInstance(document.getElementById("viewServiceResultModal")).hide();
            loadModuleData("service-results");
        } else {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: data.message || "Failed to update service result"
            });
        }
    })
    .catch(error => {
        Swal.close();
        console.error('Error updating service result:', error);
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Failed to update service result"
        });
    });
}

function cancelServiceResultEdit() {
    if (currentServiceResult) {
        displayServiceResultDetails(currentServiceResult);
    }
}

// Handle result type change to show/hide file upload
document.addEventListener('DOMContentLoaded', function() {
    const resultTypeSelect = document.getElementById('resultTypeSelect');
    const fileUploadSection = document.getElementById('fileUploadSection');
    const resultValueInput = document.getElementById('resultValue');
    
    if (resultTypeSelect) {
        resultTypeSelect.addEventListener('change', function() {
            if (this.value === 'file') {
                fileUploadSection.style.display = 'block';
                resultValueInput.required = false;
                resultValueInput.placeholder = 'File will be uploaded';
            } else {
                fileUploadSection.style.display = 'none';
                resultValueInput.required = true;
                resultValueInput.placeholder = 'Enter result value';
            }
        });
    }
});

function showAddVitalsModal() {
    new bootstrap.Modal(document.getElementById("addVitalsModal")).show();
}

function saveVitals() {
    const form = document.getElementById("vitalsForm");
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    Swal.fire({
        title: "Saving Vitals...",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    setTimeout(() => {
        Swal.close();
        Swal.fire({
            icon: "success",
            title: "Vitals Saved",
            text: "Patient vitals have been recorded successfully",
            timer: 2000,
            showConfirmButton: false
        });
        bootstrap.Modal.getInstance(document.getElementById("addVitalsModal")).hide();
        loadModuleData("vitals");
    }, 1000);
}

function refreshOverview() {
    loadModuleData("overview");
}

function editPatient() {
    if (!currentPatientId) return;
    window.location.href = editPatientRouteTemplate.replace(':id', currentPatientId);
}

function addVisit() {
    if (!currentPatientId) return;
    window.location.href = `${addVisitRoute}?patient=${currentPatientId}`;
}

function closePatientContext() {
    window.location.href = patientsRoute;
}

function showCreateBillModal() {
    new bootstrap.Modal(document.getElementById("createBillModal")).show();
}

function viewBillDetails(billId) {
    const billsData = {
        B001: {
            type: "Package",
            date: "January 25, 2024",
            status: "Partially Paid",
            totalAmount: 500,
            amountPaid: 200,
            balance: 300,
            items: [{ description: "Basic Health Package", quantity: 1, unitPrice: 500, total: 500 }],
            payments: [{ id: "P001", date: "Jan 26, 2024", amount: 200, method: "Cash", balanceBefore: 500, balanceAfter: 300 }]
        },
        B002: {
            type: "Service",
            date: "January 20, 2024",
            status: "Fully Paid",
            totalAmount: 150,
            amountPaid: 150,
            balance: 0,
            items: [{ description: "Consultation Fee", quantity: 1, unitPrice: 150, total: 150 }],
            payments: [{ id: "P002", date: "Jan 21, 2024", amount: 150, method: "Card", balanceBefore: 150, balanceAfter: 0 }]
        },
        B003: {
            type: "Combined",
            date: "January 15, 2024",
            status: "Unpaid",
            totalAmount: 750,
            amountPaid: 0,
            balance: 750,
            items: [
                { description: "Lab Tests", quantity: 2, unitPrice: 250, total: 500 },
                { description: "Consultation", quantity: 1, unitPrice: 250, total: 250 }
            ],
            payments: []
        }
    };

    const bill = billsData[billId];
    if (!bill) return;

    document.getElementById("modalBillId").textContent = billId;
    document.getElementById("modalBillType").textContent = bill.type;
    document.getElementById("modalBillDate").textContent = bill.date;
    document.getElementById("modalTotalAmount").textContent = `$${bill.totalAmount.toFixed(2)}`;
    document.getElementById("modalAmountPaid").textContent = `$${bill.amountPaid.toFixed(2)}`;
    document.getElementById("modalBalance").textContent = `$${bill.balance.toFixed(2)}`;
    document.getElementById("modalBillStatus").innerHTML = `<span class="badge bg-${bill.status === "Fully Paid" ? "success" : bill.status === "Partially Paid" ? "warning" : "danger"}">${bill.status}</span>`;

    document.getElementById("modalBillItems").innerHTML = bill.items.map(item => `
        <tr>
            <td>${item.description}</td>
            <td>${item.quantity}</td>
            <td>$${item.unitPrice.toFixed(2)}</td>
            <td>$${item.total.toFixed(2)}</td>
        </tr>
    `).join("");

    document.getElementById("modalPaymentHistory").innerHTML = bill.payments.length ? bill.payments.map(payment => `
        <tr>
            <td><span class="badge bg-success">${payment.id}</span></td>
            <td>${payment.date}</td>
            <td class="text-success">$${payment.amount.toFixed(2)}</td>
            <td>${payment.method}</td>
            <td>$${payment.balanceBefore.toFixed(2)}</td>
            <td>$${payment.balanceAfter.toFixed(2)}</td>
        </tr>
    `).join("") : `<tr><td colspan="6" class="text-center text-muted">No payments recorded yet</td></tr>`;

    const paymentBtn = document.getElementById("modalPaymentBtn");
    if (bill.status === "Fully Paid") {
        paymentBtn.disabled = true;
        paymentBtn.textContent = "Already Paid";
    } else {
        paymentBtn.disabled = false;
        paymentBtn.textContent = "Make Payment";
        paymentBtn.onclick = () => showPaymentModalFromDetails(billId);
    }

    new bootstrap.Modal(document.getElementById("billDetailsModal")).show();
}

function showPaymentModal(billId) {
    const balances = { B001: 300, B002: 0, B003: 750 };
    const balance = balances[billId];
    if (balance === undefined || balance === 0) return;

    document.getElementById("paymentBillId").textContent = billId;
    document.getElementById("paymentBalance").textContent = `$${balance.toFixed(2)}`;
    document.getElementById("paymentAmount").max = balance;

    new bootstrap.Modal(document.getElementById("paymentModal")).show();
}

function showPaymentModalFromDetails(billId) {
    bootstrap.Modal.getInstance(document.getElementById("billDetailsModal")).hide();
    setTimeout(() => showPaymentModal(billId), 300);
}

function processPayment() {
    const form = document.getElementById("paymentForm");
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const amount = parseFloat(document.getElementById("paymentAmount").value);
    const balance = parseFloat(document.getElementById("paymentBalance").textContent.replace("$", ""));

    if (amount > balance) {
        Swal.fire({
            icon: "error",
            title: "Invalid Payment Amount",
            text: `Payment amount ($${amount.toFixed(2)}) exceeds current balance ($${balance.toFixed(2)})`
        });
        return;
    }

    Swal.fire({
        title: "Processing Payment...",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    setTimeout(() => {
        Swal.close();
        Swal.fire({
            icon: "success",
            title: "Payment Processed",
            text: `Payment of $${amount.toFixed(2)} has been recorded successfully`,
            timer: 2000,
            showConfirmButton: false
        });
        bootstrap.Modal.getInstance(document.getElementById("paymentModal")).hide();
        loadModuleData("billing");
    }, 1000);
}

function createBill() {
    const form = document.getElementById("createBillForm");
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    Swal.fire({
        title: "Creating Bill...",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    setTimeout(() => {
        Swal.close();
        Swal.fire({
            icon: "success",
            title: "Bill Created",
            text: "New bill has been created successfully",
            timer: 2000,
            showConfirmButton: false
        });
        bootstrap.Modal.getInstance(document.getElementById("createBillModal")).hide();
        loadModuleData("billing");
    }, 1000);
}

function addBillItem() {
    const container = document.getElementById("billItemsContainer");
    const newItem = document.createElement("div");
    newItem.className = "row bill-item-row mt-2";
    newItem.innerHTML = `
        <div class="col-md-5"><input type="text" class="form-control" placeholder="Description" required></div>
        <div class="col-md-2"><input type="number" class="form-control" placeholder="Qty" min="1" value="1" required></div>
        <div class="col-md-2"><input type="number" class="form-control" placeholder="Price" step="0.01" min="0" required></div>
        <div class="col-md-2"><input type="text" class="form-control" placeholder="Total" readonly></div>
        <div class="col-md-1"><button type="button" class="btn btn-sm btn-danger" onclick="removeBillItem(this)"><i class="fas fa-trash"></i></button></div>
    `;
    container.appendChild(newItem);
    calculateBillTotal();
}

function removeBillItem(button) {
    button.closest(".bill-item-row").remove();
    calculateBillTotal();
}

function calculateBillTotal() {
    const rows = document.querySelectorAll(".bill-item-row");
    let total = 0;
    rows.forEach(row => {
        const qty = parseFloat(row.querySelector('input[placeholder="Qty"]').value) || 0;
        const price = parseFloat(row.querySelector('input[placeholder="Price"]').value) || 0;
        const itemTotal = qty * price;
        row.querySelector('input[placeholder="Total"]').value = itemTotal.toFixed(2);
        total += itemTotal;
    });
    document.getElementById("totalBillAmount").value = total.toFixed(2);
}

function uploadLabReport() {
    const form = document.getElementById("labReportForm");
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    Swal.fire({
        title: "Uploading Report...",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    setTimeout(() => {
        Swal.close();
        Swal.fire({
            icon: "success",
            title: "Report Uploaded",
            text: "Lab report has been uploaded successfully",
            timer: 2000,
            showConfirmButton: false
        });
        bootstrap.Modal.getInstance(document.getElementById("uploadLabReportModal")).hide();
        loadModuleData("lab-reports");
    }, 1000);
}

function saveMedicalHistory() {
    const form = document.getElementById("medicalHistoryForm");
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    Swal.fire({
        title: "Saving Record...",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    setTimeout(() => {
        Swal.close();
        Swal.fire({
            icon: "success",
            title: "Record Saved",
            text: "Medical history record has been saved successfully",
            timer: 2000,
            showConfirmButton: false
        });
        bootstrap.Modal.getInstance(document.getElementById("addMedicalHistoryModal")).hide();
        loadModuleData("medical-history");
    }, 1000);
}

function showAddMedicalHistoryModal() {
    new bootstrap.Modal(document.getElementById("addMedicalHistoryModal")).show();
}

function showAddPrescriptionModal() {
    new bootstrap.Modal(document.getElementById("addPrescriptionModal")).show();
}

function savePrescription() {
    const form = document.getElementById("prescriptionForm");
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    Swal.fire({
        title: "Saving Prescription...",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    setTimeout(() => {
        Swal.close();
        Swal.fire({
            icon: "success",
            title: "Prescription Saved",
            text: "Prescription has been saved successfully",
            timer: 2000,
            showConfirmButton: false
        });
        bootstrap.Modal.getInstance(document.getElementById("addPrescriptionModal")).hide();
        loadModuleData("prescriptions");
    }, 1000);
}

function showAddMedicationModal() {
    new bootstrap.Modal(document.getElementById("addMedicationModal")).show();
}

function saveMedication() {
    const form = document.getElementById("medicationForm");
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    Swal.fire({
        title: "Adding Medication...",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    setTimeout(() => {
        Swal.close();
        Swal.fire({
            icon: "success",
            title: "Medication Added",
            text: "Medication has been added successfully",
            timer: 2000,
            showConfirmButton: false
        });
        bootstrap.Modal.getInstance(document.getElementById("addMedicationModal")).hide();
        loadModuleData("medications");
    }, 1000);
}

function viewLabReport(reportId) {
    Swal.fire({
        title: "Lab Report Details",
        html: `
            <div class="text-start">
                <p><strong>Report ID:</strong> ${reportId}</p>
                <p><strong>Type:</strong> Complete Blood Count</p>
                <p><strong>Date:</strong> January 25, 2024</p>
                <p><strong>Status:</strong> <span class="badge bg-success">Normal</span></p>
                <hr>
                <p><strong>Results Summary:</strong></p>
                <ul>
                    <li>White Blood Cells: 7.2 x10^9/L (Normal)</li>
                    <li>Red Blood Cells: 4.8 x10^12/L (Normal)</li>
                    <li>Hemoglobin: 14.5 g/dL (Normal)</li>
                    <li>Platelets: 250 x10^9/L (Normal)</li>
                </ul>
            </div>
        `,
        width: "600px",
        confirmButtonText: "Close"
    });
}

function viewPrescription(prescriptionId) {
    Swal.fire({
        title: "Prescription Details",
        html: `
            <div class="text-start">
                <p><strong>Prescription ID:</strong> ${prescriptionId}</p>
                <p><strong>Medication:</strong> Lisinopril</p>
                <p><strong>Dosage:</strong> 10mg daily</p>
                <p><strong>Duration:</strong> 30 days</p>
                <p><strong>Prescribed By:</strong> Dr. Sarah Johnson</p>
                <p><strong>Date:</strong> January 25, 2024</p>
                <p><strong>Instructions:</strong> Take once daily with water</p>
            </div>
        `,
        width: "500px",
        confirmButtonText: "Close"
    });
}

function editMedication(medicationId) {
    Swal.fire({
        title: "Edit Medication",
        text: "Edit medication functionality would open edit form",
        icon: "info"
    });
}

function discontinueMedication(medicationId) {
    Swal.fire({
        title: "Discontinue Medication?",
        text: "This will mark the medication as discontinued. Are you sure?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, Discontinue",
        cancelButtonText: "Cancel"
    }).then(result => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: "success",
                title: "Medication Discontinued",
                text: "Medication has been marked as discontinued",
                timer: 2000,
                showConfirmButton: false
            });
            loadModuleData("medications");
        }
    });
}

function refreshBills() {
    loadModuleData("billing");
}

function printBill(billId) {
    Swal.fire({
        icon: "info",
        title: "Print Bill",
        text: `Printing bill ${billId}...`
    });
}
</script>
@endsection
