@extends('layouts.app')

@section('title', 'Patient Context')

@section('css')
<style>
    .patient-context-container {
        background: #f8f9fa;
        min-height: 100vh;
    }
    .patient-header {
        background: #fff;
        padding: 2rem 0;
        border-bottom: 1px solid #e9ecef;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .patient-avatar-container {
        width: 80px;
        height: 80px;
        background: #e9ecef;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: #6c757d;
        border: 3px solid #fff;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .patient-info h3 {
        margin-bottom: 0.25rem;
        font-weight: 700;
        color: #2d3748;
    }
    .patient-meta .badge {
        font-weight: 500;
        padding: 0.5em 0.75em;
    }
    .patient-navigation {
        background: #fff;
        margin-top: -1px;
        position: sticky;
        top: 0;
        z-index: 100;
        border-bottom: 1px solid #e9ecef;
    }
    .nav-tabs.patient-nav-tabs {
        border-bottom: none;
    }
    .nav-tabs.patient-nav-tabs .nav-link {
        border: none;
        padding: 1rem 1.5rem;
        font-weight: 600;
        color: #718096;
        transition: all 0.2s;
        border-bottom: 3px solid transparent;
    }
    .nav-tabs.patient-nav-tabs .nav-link:hover {
        color: #4a5568;
        background: #f7fafc;
    }
    .nav-tabs.patient-nav-tabs .nav-link.active {
        color: #3182ce;
        border-bottom-color: #3182ce;
        background: transparent;
    }
    .patient-content {
        padding: 2rem 0;
    }
    .module-container {
        display: none;
    }
    .module-container.active {
        display: block;
        animation: fadeIn 0.3s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        margin-bottom: 1.5rem;
    }
    .card-header {
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        padding: 1.25rem 1.5rem;
        border-radius: 12px 12px 0 0 !important;
    }
    .stat-card {
        padding: 1.5rem;
        border-radius: 12px;
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .vitals-timeline {
        position: relative;
        padding-left: 2rem;
    }
    .vitals-timeline::before {
        content: '';
        position: absolute;
        left: 0.5rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e2e8f0;
    }
    .vital-entry {
        position: relative;
        padding-bottom: 2rem;
    }
    .vital-entry::before {
        content: '';
        position: absolute;
        left: -1.75rem;
        top: 0.25rem;
        width: 12px;
        height: 12px;
        background: #3182ce;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px #3182ce;
    }
    .vital-date {
        font-weight: 700;
        font-size: 0.875rem;
        color: #4a5568;
        margin-bottom: 0.5rem;
    }
    .vital-metrics {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .vital-metric {
        background: #f7fafc;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        min-width: 120px;
    }
    .vital-metric .value {
        font-weight: 700;
        font-size: 1.125rem;
        color: #2d3748;
    }
    .bg-success-soft { background-color: rgba(72, 187, 120, 0.15); }
    .bg-primary-soft { background-color: rgba(66, 153, 225, 0.15); }
    .bg-warning-soft { background-color: rgba(237, 137, 54, 0.15); }
    .bg-info-soft { background-color: rgba(66, 153, 225, 0.15); }
    .text-success { color: #2f855a !important; }
    .text-primary { color: #2b6cb0 !important; }
    .text-warning { color: #c05621 !important; }
    .text-info { color: #2b6cb0 !important; }
    
    /* Dashboard Specific Styles */
    .dashboard-stat-card {
        border-radius: 15px;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
        border: none;
        transition: transform 0.3s ease;
        height: 100%;
    }
    .dashboard-stat-card:hover {
        transform: translateY(-5px);
    }
    .dashboard-stat-card .icon-bg {
        position: absolute;
        right: -10px;
        bottom: -10px;
        font-size: 5rem;
        opacity: 0.1;
        transform: rotate(-15deg);
    }
    .dashboard-stat-card .card-title {
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
    }
    .dashboard-stat-card .card-value {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 0;
    }
    .activity-timeline {
        position: relative;
        padding-left: 1.5rem;
    }
    .activity-timeline::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #edf2f7;
    }
    .activity-item {
        position: relative;
        padding-bottom: 1.5rem;
    }
    .activity-item::before {
        content: '';
        position: absolute;
        left: -1.85rem;
        top: 0.25rem;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #fff;
        border: 2px solid #3182ce;
        z-index: 1;
    }
    .activity-item.visit::before { border-color: #3182ce; }
    .activity-item.payment::before { border-color: #48bb78; }
    .activity-item.lab::before { border-color: #805ad5; }
    
    .vitals-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    .vital-snapshot {
        background: #f8fafc;
        padding: 1rem;
        border-radius: 12px;
        text-align: center;
        border: 1px solid #f1f5f9;
    }
    .vital-snapshot .icon {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }
    .vital-snapshot .value {
        font-weight: 700;
        font-size: 1.1rem;
        display: block;
    }
    .vital-snapshot .label {
        font-size: 0.75rem;
        color: #64748b;
    }
</style>
@endsection

@section('content')
<div class="patient-context-container">
    <!-- Patient Header -->
    <div class="patient-header">
        <div class="container-fluid px-4">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="patient-avatar-container">
                        @if($patient->patient_photo)
                            <img src="{{ asset('storage/' . $patient->patient_photo) }}" alt="Patient Photo" class="rounded-circle w-100 h-100" style="object-fit: cover;">
                        @else
                            <i class="fas fa-user"></i>
                        @endif
                    </div>
                </div>
                <div class="col patient-info">
                    <h3>{{ $patient->first_name }} {{ $patient->last_name }}</h3>
                    <div class="patient-meta d-flex align-items-center">
                        <span class="badge bg-primary me-3">{{ $patient->patient_code }}</span>
                        <span class="text-muted me-3"><i class="fas fa-birthday-cake me-1 text-primary"></i>{{ $patient->age }} years</span>
                        <span class="text-muted me-3"><i class="fas fa-venus-mars me-1 text-primary"></i>{{ ucfirst($patient->gender) }}</span>
                        <span class="text-muted me-3"><i class="fas fa-phone me-1 text-primary"></i>{{ $patient->phone }}</span>
                        <span class="badge bg-success">Active</span>
                    </div>
                </div>
                <div class="col-auto patient-actions">
                    <div class="btn-group">
                        <a href="{{ route('patients.edit', $patient->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit me-1"></i>Edit Profile
                        </a>
                        <a href="{{ route('visits.add') }}?patient_code={{ $patient->patient_code }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>New Visit
                        </a>
                        <a href="{{ route('patients') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times me-1"></i>Close
                        </a>
                    </div>
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
                        <i class="fas fa-prescriptions me-1"></i>Prescriptions
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
                <!-- Top Row: Metric Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="dashboard-stat-card bg-primary text-white">
                            <i class="fas fa-calendar-check icon-bg"></i>
                            <div class="card-title">Total Visits</div>
                            <div class="card-value">{{ $contextData['overview']['total_visits'] }}</div>
                            <div class="small mt-2 opacity-75">Latest: {{ $contextData['overview']['last_visit_date'] }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="dashboard-stat-card bg-success text-white">
                            <i class="fas fa-hand-holding-medical icon-bg"></i>
                            <div class="card-title">Active Services</div>
                            <div class="card-value">{{ $contextData['overview']['active_services_count'] }}</div>
                            <div class="small mt-2 opacity-75">Assigned to patient</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="dashboard-stat-card bg-info text-white">
                            <i class="fas fa-box-open icon-bg"></i>
                            <div class="card-title">Active Packages</div>
                            <div class="card-value">{{ $contextData['overview']['active_packages_count'] }}</div>
                            <div class="small mt-2 opacity-75">Care plans active</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="dashboard-stat-card bg-warning text-white">
                            <i class="fas fa-wallet icon-bg"></i>
                            <div class="card-title">Balance Due</div>
                            <div class="card-value">GH₵{{ $contextData['overview']['outstanding_balance'] }}</div>
                            <div class="small mt-2 opacity-75">Outstanding balance</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Left Column: Activity & History -->
                    <div class="col-md-8">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 fw-bold"><i class="fas fa-history text-primary me-2"></i>Recent Activity</h5>
                                <button class="btn btn-sm btn-link text-decoration-none" onclick="switchModule('medical-history')">View All</button>
                            </div>
                            <div class="card-body">
                                <div class="activity-timeline">
                                    @if($contextData['medical_history']['has_history'])
                                        @foreach($contextData['medical_history']['medical_history']->take(3) as $activity)
                                            <div class="activity-item visit">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <h6 class="mb-0 fw-bold">Patient Visit: {{ $activity['practitioner'] }}</h6>
                                                    <small class="text-muted">{{ $activity['visit_date'] }}</small>
                                                </div>
                                                <p class="text-muted small mb-0">{{ $activity['chief_complaint'] ?? 'No complaint recorded' }}</p>
                                                <div class="mt-2">
                                                    @foreach($activity['services'] as $service)
                                                        <span class="badge bg-light text-dark border me-1 small">{{ $service['name'] }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-4">
                                            <p class="text-muted mb-0">No recent activity found.</p>
                                        </div>
                                    @endif
                                    
                                    @if($contextData['billing']['has_bills'])
                                        @foreach($contextData['billing']['bills']->take(1) as $bill)
                                            @foreach($bill['payments']->take(1) as $payment)
                                                <div class="activity-item payment">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <h6 class="mb-0 fw-bold">Payment Received</h6>
                                                        <small class="text-muted">{{ $payment['payment_date'] }}</small>
                                                    </div>
                                                    <p class="text-success fw-bold mb-0">GH₵{{ $payment['amount_paid'] }} paid via {{ ucfirst($payment['payment_method']) }}</p>
                                                </div>
                                            @endforeach
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white">
                                <h5 class="mb-0 fw-bold"><i class="fas fa-box text-primary me-2"></i>Active Packages & Care Plans</h5>
                            </div>
                            <div class="card-body p-0">
                                @if($contextData['overview']['active_packages_count'] > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="ps-4">Package Name</th>
                                                    <th>Price</th>
                                                    <th class="text-end pe-4">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($contextData['overview']['active_packages'] as $package)
                                                    <tr>
                                                        <td class="ps-4 fw-bold text-primary">{{ $package->package_name }}</td>
                                                        <td>GH₵{{ number_format($package->total_cost, 2) }}</td>
                                                        <td class="text-end pe-4">
                                                            <span class="badge rounded-pill bg-success-soft text-success px-3">Active</span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <i class="fas fa-box fa-3x text-muted opacity-25 mb-3"></i>
                                        <p class="text-muted mb-0">No active packages found.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Vitals & Summary -->
                    <div class="col-md-4">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 fw-bold"><i class="fas fa-heartbeat text-danger me-2"></i>Latest Vitals</h5>
                                <button class="btn btn-sm btn-link text-decoration-none" onclick="switchModule('vitals')">Full History</button>
                            </div>
                            <div class="card-body">
                                @if($contextData['vitals']['has_vitals'])
                                    <div class="vitals-grid">
                                        <div class="vital-snapshot">
                                            <div class="icon text-danger"><i class="fas fa-stethoscope"></i></div>
                                            <span class="value">{{ $contextData['vitals']['latest_vitals']['blood_pressure'] ?? '--' }}</span>
                                            <span class="label">Blood Pressure</span>
                                        </div>
                                        <div class="vital-snapshot">
                                            <div class="icon text-primary"><i class="fas fa-heartbeat"></i></div>
                                            <span class="value">{{ $contextData['vitals']['latest_vitals']['pulse_rate'] ?? '--' }}</span>
                                            <span class="label">Pulse Rate</span>
                                        </div>
                                        <div class="vital-snapshot">
                                            <div class="icon text-warning"><i class="fas fa-thermometer-half"></i></div>
                                            <span class="value">{{ $contextData['vitals']['latest_vitals']['temperature'] ?? '--' }}</span>
                                            <span class="label">Temperature</span>
                                        </div>
                                        <div class="vital-snapshot">
                                            <div class="icon text-info"><i class="fas fa-weight"></i></div>
                                            <span class="value">{{ $contextData['vitals']['latest_vitals']['weight'] ?? '--' }}</span>
                                            <span class="label">Weight</span>
                                        </div>
                                    </div>
                                    <div class="mt-3 text-center">
                                        <small class="text-muted">Last measured: {{ $contextData['vitals']['latest_vitals']['visit_date'] }}</small>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <p class="text-muted mb-0">No vitals recorded.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white">
                                <h5 class="mb-0 fw-bold"><i class="fas fa-info-circle text-primary me-2"></i>Patient Details</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-3">
                                        <small class="text-muted text-uppercase fw-bold d-block mb-1">Registered Since</small>
                                        <span class="fw-bold">{{ $patient->registered_at ? (is_string($patient->registered_at) ? date('M d, Y', strtotime($patient->registered_at)) : $patient->registered_at->format('M d, Y')) : 'N/A' }}</span>
                                    </li>
                                    <li class="mb-3">
                                        <small class="text-muted text-uppercase fw-bold d-block mb-1">Practitioner(s)</small>
                                        <span class="fw-bold">{{ $contextData['medical_history']['medical_history']->first()['practitioner'] ?? 'Not assigned' }}</span>
                                    </li>
                                    <li>
                                        <small class="text-muted text-uppercase fw-bold d-block mb-1">Email Address</small>
                                        <span class="fw-bold">{{ $patient->email ?? 'Not provided' }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vitals Module -->
            <div id="vitals-module" class="module-container">
                <!-- Vitals Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">Total Vitals</h6>
                                        <h3 class="mb-0">{{ $contextData['vitals']['vitals']->count() }}</h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-heartbeat fa-2x opacity-75"></i>
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
                                        <h6 class="mb-0">Latest BP</h6>
                                        <h3 class="mb-0">{{ $contextData['vitals']['latest_vitals']['blood_pressure'] ?? 'N/A' }}</h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-stethoscope fa-2x opacity-75"></i>
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
                                        <h6 class="mb-0">Latest Pulse Rate</h6>
                                        <h3 class="mb-0">{{ $contextData['vitals']['latest_vitals']['pulse_rate'] ?? 'N/A' }}</h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-heart-pulse fa-2x opacity-75"></i>
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
                                        <h6 class="mb-0">Latest Temp</h6>
                                        <h3 class="mb-0">{{ $contextData['vitals']['latest_vitals']['temperature'] ?? 'N/A' }}</h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-thermometer-half fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-heartbeat text-danger me-2"></i>
                            Vitals History
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($contextData['vitals']['has_vitals'])
                            <div class="vitals-timeline">
                                @foreach($contextData['vitals']['vitals'] as $vital)
                                    <div class="vital-entry">
                                        <div class="vital-date">{{ $vital['visit_date'] }} @if($vital['visit_time'])- {{ $vital['visit_time'] }}@endif</div>
                                        <div class="vital-metrics">
                                            @if($vital['blood_pressure'])
                                                <div class="vital-metric">
                                                    <div class="value">{{ $vital['blood_pressure'] }}</div>
                                                    <div class="label">Blood Pressure</div>
                                                </div>
                                            @endif
                                            @if($vital['pulse_rate'])
                                                <div class="vital-metric">
                                                    <div class="value">{{ $vital['pulse_rate'] }}</div>
                                                    <div class="label">Pulse Rate</div>
                                                </div>
                                            @endif
                                            @if($vital['temperature'])
                                                <div class="vital-metric">
                                                    <div class="value">{{ $vital['temperature'] }}</div>
                                                    <div class="label">Temperature</div>
                                                </div>
                                            @endif
                                            @if($vital['oxygen_saturation'])
                                                <div class="vital-metric">
                                                    <div class="value">{{ $vital['oxygen_saturation'] }}</div>
                                                    <div class="label">O₂ Saturation</div>
                                                </div>
                                            @endif
                                            @if($vital['respiratory_rate'])
                                                <div class="vital-metric">
                                                    <div class="value">{{ $vital['respiratory_rate'] }}</div>
                                                    <div class="label">Respiratory Rate</div>
                                                </div>
                                            @endif
                                            @if($vital['weight'])
                                                <div class="vital-metric">
                                                    <div class="value">{{ $vital['weight'] }}</div>
                                                    <div class="label">Weight</div>
                                                </div>
                                            @endif
                                            @if($vital['height'])
                                                <div class="vital-metric">
                                                    <div class="value">{{ $vital['height'] }}</div>
                                                    <div class="label">Height</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-heartbeat fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No Vitals Recorded</h5>
                                <p class="text-muted">No vital signs have been recorded for this patient yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Service Results Module -->
            <div id="service-results-module" class="module-container">
                <!-- Service Results Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card stat-card bg-white border-start border-primary border-4 shadow-sm">
                            <div class="card-body p-0">
                                <h6 class="text-muted text-uppercase small fw-bold">Total Results</h6>
                                <h3 class="mb-0 fw-bold">{{ $contextData['service_results']['total_results'] }}</h3>
                                <div class="mt-2 small text-primary">
                                    <i class="fas fa-flask me-1"></i> Lifetime tests
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card bg-white border-start border-success border-4 shadow-sm">
                            <div class="card-body p-0">
                                <h6 class="text-muted text-uppercase small fw-bold">Approved</h6>
                                <h3 class="mb-0 fw-bold text-success">{{ $contextData['service_results']['approved_results'] }}</h3>
                                <div class="mt-2 small text-success">
                                    <i class="fas fa-check-circle me-1"></i> Validated reports
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card bg-white border-start border-warning border-4 shadow-sm">
                            <div class="card-body p-0">
                                <h6 class="text-muted text-uppercase small fw-bold">Pending Actions</h6>
                                <h3 class="mb-0 fw-bold text-warning">{{ $contextData['service_results']['pending_results'] }}</h3>
                                <div class="mt-2 small text-warning">
                                    <i class="fas fa-clock me-1"></i> Requiring review
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card shadow-sm border-0">
                    <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-microscope text-primary me-2"></i>
                            Laboratory & Service Results
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Timestamp</th>
                                        <th>Service/Package Name</th>
                                        <th>Result Value</th>
                                        <th class="pe-4">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                            @if($contextData['service_results']['has_results'])
                                @foreach($contextData['service_results']['service_results'] as $result)
                                    <tr onclick="showServiceResultEditInfo()" style="cursor: pointer;" role="button" tabindex="0" onkeydown="if(event.key==='Enter'||event.key===' ') showServiceResultEditInfo()">
                                        <td class="ps-4">
                                            {{ $result['recorded_at'] }}
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark">{{ $result['service_name'] }}</span>
                                            <br>
                                            <small class="text-muted">{{ ucfirst($result['result_type']) }} Result</small>
                                        </td>
                                        <td>
                                            @if($result['result_type'] === 'numeric')
                                                <span class="fw-bold h6 mb-0">{{ $result['result_value'] }}</span>
                                            @elseif($result['result_type'] === 'text')
                                                <span class="text-truncate d-inline-block" style="max-width: 250px;">{{ $result['result_value'] }}</span>
                                            @else
                                                <span class="badge bg-light text-dark border"><i class="fas fa-file-pdf me-1 text-danger"></i> Attachment</span>
                                            @endif
                                        </td>
                                        <td class="pe-4">
                                            @php
                                                $statusClass = [
                                                    'approved' => 'success',
                                                    'pending_approval' => 'warning',
                                                    'draft' => 'info',
                                                    'rejected' => 'danger'
                                                ][$result['status']] ?? 'secondary';
                                            @endphp
                                            <span class="badge rounded-pill bg-{{ $statusClass }}-soft text-{{ $statusClass }} px-3">
                                                {{ ucfirst(str_replace('_', ' ', $result['status'])) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="fas fa-microscope fa-3x mb-3 opacity-25"></i>
                                        <p>No laboratory or service results found for this patient.</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medical History Module -->
            <div id="medical-history-module" class="module-container">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-history fa-4x text-muted opacity-25"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Medical History Tracking</h4>
                        <p class="text-muted mx-auto" style="max-width: 500px;">
                            We are currently building a comprehensive medical history management system. 
                            Soon, you'll be able to track chronic conditions, allergies, surgeries, and more right here.
                        </p>
                        <div class="mt-4">
                            <span class="badge rounded-pill bg-primary-soft text-primary px-4 py-2">
                                <i class="fas fa-tools me-2"></i>Coming Soon
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prescriptions Module -->
            <div id="prescriptions-module" class="module-container">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-prescription fa-4x text-muted opacity-25"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Prescription Management</h4>
                        <p class="text-muted mx-auto" style="max-width: 500px;">
                            A new prescription engine is on its way! You will soon be able to issue, 
                            track, and print digital prescriptions for your patients.
                        </p>
                        <div class="mt-4">
                            <span class="badge rounded-pill bg-success-soft text-success px-4 py-2">
                                <i class="fas fa-flask me-2"></i>Under Development
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medications Module -->
            <div id="medications-module" class="module-container">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-pills fa-4x text-muted opacity-25"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Medication Tracking</h4>
                        <p class="text-muted mx-auto" style="max-width: 500px;">
                            Keep track of active and past medications effortlessly. This module will 
                            allow you to monitor patient adherence and drug interactions.
                        </p>
                        <div class="mt-4">
                            <span class="badge rounded-pill bg-warning-soft text-warning px-4 py-2">
                                <i class="fas fa-clock me-2"></i>Stay Tuned
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Billing & Payments Module -->
            <div id="billing-module" class="module-container">
                <!-- Billing Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card bg-white border-start border-primary border-4">
                            <div class="card-body p-0">
                                <h6 class="text-muted text-uppercase small fw-bold">Total Billed</h6>
                                <h3 class="mb-0 fw-bold">GH₵{{ $contextData['billing']['total_billed'] }}</h3>
                                <div class="mt-2 small text-primary">
                                    <i class="fas fa-file-invoice-dollar me-1"></i> Lifetime billing
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-white border-start border-success border-4">
                            <div class="card-body p-0">
                                <h6 class="text-muted text-uppercase small fw-bold">Total Paid</h6>
                                <h3 class="mb-0 fw-bold text-success">GH₵{{ $contextData['billing']['total_paid'] }}</h3>
                                <div class="mt-2 small text-success">
                                    <i class="fas fa-check-circle me-1"></i> Total collections
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-white border-start border-warning border-4">
                            <div class="card-body p-0">
                                <h6 class="text-muted text-uppercase small fw-bold">Outstanding</h6>
                                <h3 class="mb-0 fw-bold text-warning">GH₵{{ $contextData['billing']['outstanding_balance'] }}</h3>
                                <div class="mt-2 small text-warning">
                                    <i class="fas fa-clock me-1"></i> Pending payments
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-white border-start border-info border-4">
                            <div class="card-body p-0">
                                <h6 class="text-muted text-uppercase small fw-bold">Bills Summary</h6>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="badge bg-success">Paid: {{ $contextData['billing']['payment_summary']['paid_bills'] }}</span>
                                    <span class="badge bg-warning">Pending: {{ $contextData['billing']['payment_summary']['pending_bills'] + $contextData['billing']['payment_summary']['partial_bills'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bills Table -->
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center bg-white">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-file-invoice-dollar text-primary me-2"></i>
                            Recent Invoices
                        </h5>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('billing.user') }}?patient={{ $patient->patient_code }}" class="btn btn-outline-primary">
                                <i class="fas fa-external-link-alt me-1"></i>Billing Dashboard
                            </a>
                            <button class="btn btn-outline-secondary" onclick="refreshBills()">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Invoice #</th>
                                        <th>Date</th>
                                        <th>Total Amount</th>
                                        <th>Paid</th>
                                        <th>Balance</th>
                                        <th>Status</th>
                                        <th class="text-center pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                            @if($contextData['billing']['has_bills'])
                                @foreach($contextData['billing']['bills'] as $bill)
                                    <tr>
                                        <td class="ps-4">
                                            <span class="fw-bold">#{{ str_pad($bill['bill_id'], 6, '0', STR_PAD_LEFT) }}</span>
                                            <br>
                                            <small class="text-muted">{{ ucfirst($bill['bill_type']) }}</small>
                                        </td>
                                        <td>{{ $bill['created_at'] }}</td>
                                        <td class="fw-bold">GH₵{{ $bill['total_amount'] }}</td>
                                        <td class="text-success fw-semibold">GH₵{{ $bill['amount_paid'] }}</td>
                                        <td class="text-{{ $bill['balance'] > 0 ? 'warning' : 'success' }} fw-bold">GH₵{{ $bill['balance'] }}</td>
                                        <td>
                                            @php
                                                $statusClass = [
                                                    'paid' => 'success',
                                                    'partial' => 'info',
                                                    'pending' => 'warning',
                                                    'overdue' => 'danger'
                                                ][$bill['status']] ?? 'secondary';
                                            @endphp
                                            <span class="badge rounded-pill bg-{{ $statusClass }}-soft text-{{ $statusClass }} px-3">
                                                {{ ucfirst($bill['status']) }}
                                            </span>
                                        </td>
                                        <td class="text-center pe-4">
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-light" onclick="viewBillDetails('{{ $bill['bill_id'] }}')" title="View">
                                                    <i class="fas fa-eye text-primary"></i>
                                                </button>
                                                @if($bill['status'] !== 'paid')
                                                    <button class="btn btn-light" onclick="showPaymentModal('{{ $bill['bill_id'] }}')" title="Pay">
                                                        <i class="fas fa-money-bill-wave text-success"></i>
                                                    </button>
                                                @endif
                                                <button class="btn btn-light" onclick="printBill('{{ $bill['bill_id'] }}')" title="Print">
                                                    <i class="fas fa-print text-info"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-file-invoice fa-3x mb-3 opacity-25"></i>
                                        <p>No billing records found for this patient.</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Recent Payments -->
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-receipt text-success me-2"></i>
                            Recent Payment History
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Receipt #</th>
                                        <th>Date</th>
                                        <th>Method</th>
                                        <th>Amount Paid</th>
                                        <th class="pe-4">Staff</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $hasPayments = false; @endphp
                                    @if($contextData['billing']['has_bills'])
                                        @foreach($contextData['billing']['bills'] as $bill)
                                            @if($bill['payments'])
                                                @foreach($bill['payments'] as $payment)
                                                    @php $hasPayments = true; @endphp
                                                    <tr>
                                                        <td class="ps-4">
                                                            <span class="fw-bold">REC-{{ str_pad($payment['payment_id'], 4, '0', STR_PAD_LEFT) }}</span>
                                                            <br>
                                                            <small class="text-muted">Invoice #{{ str_pad($bill['bill_id'], 6, '0', STR_PAD_LEFT) }}</small>
                                                        </td>
                                                        <td>{{ $payment['payment_date'] }}</td>
                                                        <td>
                                                            <span class="badge bg-light text-dark border">
                                                                <i class="fas fa-{{ $payment['payment_method'] === 'cash' ? 'money-bill' : ($payment['payment_method'] === 'mobile_money' ? 'mobile-alt' : 'university') }} me-1"></i>
                                                                {{ ucfirst(str_replace('_', ' ', $payment['payment_method'])) }}
                                                            </span>
                                                        </td>
                                                        <td class="text-success fw-bold">GH₵{{ $payment['amount_paid'] }}</td>
                                                        <td class="pe-4">{{ $payment['received_by_name'] ?? 'System' }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endif
                                    
                                    @if(!$hasPayments)
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">No payment transactions recorded.</td>
                                        </tr>
                                    @endif
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
                        <div class="text-center py-5">
                            <i class="fas fa-pills fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">Coming Soon</h4>
                            <p class="text-muted">Medication management will be available in a future update.</p>
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
                                <label class="form-label">Pulse Rate (bpm)</label>
                                <input type="number" class="form-control" id="pulseRate" placeholder="72">
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
    console.log('switchModule called with:', moduleName);
    
    document.querySelectorAll(".patient-nav-tabs .nav-link").forEach(link => link.classList.remove("active"));
    const targetNav = document.querySelector(`[data-module="${moduleName}"]`);
    if (targetNav) {
        targetNav.classList.add("active");
    }

    document.querySelectorAll(".module-container").forEach(container => container.classList.remove("active"));
    const targetModule = document.getElementById(`${moduleName}-module`);
    if (targetModule) {
        console.log('Adding active class to:', `${moduleName}-module`);
        targetModule.classList.add("active");
        
        // Additional debugging for billing module
        if (moduleName === 'billing') {
            console.log('Billing module activated!');
            console.log('Module element:', targetModule);
            console.log('Module classes:', targetModule.className);
            console.log('Module display style:', window.getComputedStyle(targetModule).display);
            
            // Check for summary cards
            const cards = targetModule.querySelectorAll('.card');
            console.log('Cards found in billing module:', cards.length);
            cards.forEach((card, index) => {
                console.log(`Card ${index + 1}:`, card.className, 'Display:', window.getComputedStyle(card).display);
            });
        }
    } else {
        console.error('Module not found:', `${moduleName}-module`);
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
    console.log('updateModuleContent called for:', moduleName, 'with data:', data);
    
    // Debug billing summary cards
    if (moduleName === 'billing') {
        const totalBilledCard = document.querySelector('.bg-primary');
        const totalPaidCard = document.querySelector('.bg-success');
        const outstandingCard = document.querySelector('.bg-warning');
        const activeBillsCard = document.querySelector('.bg-info');
        
        console.log('Billing cards found:', {
            totalBilled: !!totalBilledCard,
            totalPaid: !!totalPaidCard,
            outstanding: !!outstandingCard,
            activeBills: !!activeBillsCard
        });
        
        if (totalBilledCard) {
            console.log('Total billed card content:', totalBilledCard.innerHTML);
        }
    }
    
    switch(moduleName) {
        case 'overview':
            updateOverviewModule(data);
            break;
        case 'vitals':
            updateVitalsModule(data);
            break;
        case 'billing':
            // Update billing cards with correct API data since server data is wrong
            console.log('Billing API data received:', data);
            
            // Update summary cards with correct data
            const totalBilledCard = document.querySelector('.bg-primary h3');
            const totalPaidCard = document.querySelector('.bg-success h3');
            const outstandingCard = document.querySelector('.bg-warning h3');
            const totalBillsCard = document.querySelector('.bg-info h3');
            
            if (totalBilledCard && data.total_billed) {
                totalBilledCard.textContent = `$${data.total_billed}`;
                console.log('Updated total billed to:', `$${data.total_billed}`);
            }
            if (totalPaidCard && data.total_paid) {
                totalPaidCard.textContent = `$${data.total_paid}`;
                console.log('Updated total paid to:', `$${data.total_paid}`);
            }
            if (outstandingCard && data.outstanding_balance) {
                outstandingCard.textContent = `$${data.outstanding_balance}`;
                console.log('Updated outstanding to:', `$${data.outstanding_balance}`);
            }
            if (totalBillsCard && data.payment_summary) {
                const totalBills = data.payment_summary.paid_bills + data.payment_summary.partial_bills + data.payment_summary.pending_bills;
                totalBillsCard.textContent = totalBills;
                console.log('Updated total bills to:', totalBills);
            }
            break;
        case 'medical-history':
            updateMedicalHistoryModule(data);
            break;
        case 'service-results':
            updateServiceResultsModule(data);
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
            <td>${vital.pulse_rate || 'N/A'}</td>
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
    // Billing module is already rendered with real data from server
    // No need to update via JavaScript
    console.log('Billing module data loaded:', data);
}

function updateVisitsModule(data) {
    // Implementation for visits module would go here
    console.log('Visits data:', data);
}

function showServiceResultEditInfo() {
    Swal.fire({
        title: 'Note',
        text: 'To modify this result, please navigate to Visit & Attendance → search by visiting date and patient code.',
        icon: 'info',
        timer: 5000,
        timerProgressBar: true,
        showConfirmButton: false,
        toast: true,
        position: 'top-end',
        background: '#fff',
        color: '#333',
        iconColor: '#3085d6',
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });
}

function updateServiceResultsModule(data) {
    const tableBody = document.querySelector('#service-results-module tbody');
    if (!tableBody) return;
    
    if (data.has_results) {
        tableBody.innerHTML = data.service_results.map(result => {
            let statusClass = 'secondary';
            if (result.status === 'approved') statusClass = 'success';
            else if (result.status === 'pending_approval') statusClass = 'warning';
            else if (result.status === 'draft') statusClass = 'info';
            else if (result.status === 'rejected') statusClass = 'danger';
            
            let resultDisplay = '';
            if (result.result_type === 'numeric') {
                resultDisplay = `<span class="fw-bold h6 mb-0">${result.result_value}</span>`;
            } else if (result.result_type === 'text') {
                resultDisplay = `<span class="text-truncate d-inline-block" style="max-width: 250px;">${result.result_value}</span>`;
            } else {
                resultDisplay = `<span class="badge bg-light text-dark border"><i class="fas fa-file-pdf me-1 text-danger"></i> Attachment</span>`;
            }

            return `
                <tr onclick="showServiceResultEditInfo()" style="cursor: pointer;" role="button" tabindex="0" onkeydown="if(event.key==='Enter'||event.key===' ') showServiceResultEditInfo()">
                    <td class="ps-4">
                        ${result.recorded_at}
                    </td>
                    <td>
                        <span class="fw-bold text-dark">${result.service_name}</span>
                        <br>
                        <small class="text-muted">${result.result_type.charAt(0).toUpperCase() + result.result_type.slice(1)} Result</small>
                    </td>
                    <td>${resultDisplay}</td>
                    <td class="pe-4">
                        <span class="badge rounded-pill bg-${statusClass}-soft text-${statusClass} px-3">
                            ${result.status.charAt(0).toUpperCase() + result.status.slice(1).replace('_', ' ')}
                        </span>
                    </td>
                </tr>
            `;
        }).join('');
    } else {
        tableBody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center py-5 text-muted">
                    <i class="fas fa-microscope fa-3x mb-3 opacity-25"></i>
                    <p>No laboratory or service results found for this patient.</p>
                </td>
            </tr>
        `;
    }
}

function printServiceResult(resultId) {
    window.open(`/service-results/${resultId}/print`, '_blank');
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

    const serviceName = result.service ? result.service.name : (result.package ? result.package.package_name : 'Unknown Service');
    
    content.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="fw-bold text-muted small text-uppercase mb-3">Service Information</h6>
                <table class="table table-sm">
                    <tr>
                        <td width="40%"><strong>Service:</strong></td>
                        <td>${serviceName}</td>
                    </tr>
                    <tr>
                        <td><strong>Result Type:</strong></td>
                        <td>${result.result_type.charAt(0).toUpperCase() + result.result_type.slice(1)}</td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            <span class="badge rounded-pill bg-${result.status === 'approved' ? 'success' : (result.status === 'draft' ? 'info' : 'warning')}-soft text-${result.status === 'approved' ? 'success' : (result.status === 'draft' ? 'info' : 'warning')} px-3">
                                ${result.status.charAt(0).toUpperCase() + result.status.slice(1).replace('_', ' ')}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="fw-bold text-muted small text-uppercase mb-3">Visit & Staff</h6>
                <table class="table table-sm">
                    <tr>
                        <td width="40%"><strong>Visit Code:</strong></td>
                        <td>${result.visit ? result.visit.visit_code : 'N/A'}</td>
                    </tr>
                    <tr>
                        <td><strong>Recorded By:</strong></td>
                        <td>${result.recorder ? result.recorder.name : 'System'}</td>
                    </tr>
                    <tr>
                        <td><strong>Recorded At:</strong></td>
                        <td>${result.recorded_at ? new Date(result.recorded_at).toLocaleString() : 'N/A'}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-12">
                <h6 class="fw-bold text-muted small text-uppercase mb-2">Result Value</h6>
                <div class="p-4 rounded bg-light border">
                    ${result.result_type === 'file' && result.result_file_path ? 
                        `<div class="text-center">
                            <i class="fas fa-file-pdf fa-3x text-danger mb-3"></i>
                            <p class="fw-bold">${result.result_file_name}</p>
                            <a href="${appUrl('/storage/' + result.result_file_path)}" target="_blank" class="btn btn-primary">
                                <i class="fas fa-external-link-alt me-2"></i>Open Attachment
                            </a>
                        </div>` : 
                        `<div class="h5 mb-0 fw-bold">${resultValue}</div>`
                    }
                </div>
            </div>
        </div>
        ${result.notes ? `
        <div class="row mt-4">
            <div class="col-12">
                <h6 class="fw-bold text-muted small text-uppercase mb-2">Clinical Notes</h6>
                <div class="p-3 rounded bg-light border italic text-muted">
                    ${result.notes}
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
