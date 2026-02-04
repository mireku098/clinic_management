@extends('layouts.app')

@section('title', 'Dashboard - Clinic Management System')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1>Dashboard</h1>
        <p class="text-muted">Clinic Management Overview</p>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-details">
                    <h3>248</h3>
                    <p>Total Patients</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-details">
                    <h3>12</h3>
                    <p>Today's Visits</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-details">
                    <h3>8</h3>
                    <p>Active Packages</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-danger">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-details">
                    <h3>GH₵45,600</h3>
                    <p>Today's Revenue</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Quick Actions -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Recent Activity</h5>
                </div>
                <div class="card-body">
                    <div class="activity-list">
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-user-plus text-primary"></i>
                            </div>
                            <div class="activity-content">
                                <p><strong>New patient registered:</strong> Sarah Johnson</p>
                                <small class="text-muted">2 minutes ago</small>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-calendar-check text-success"></i>
                            </div>
                            <div class="activity-content">
                                <p><strong>Visit completed:</strong> Michael Brown - Physiotherapy Session</p>
                                <small class="text-muted">15 minutes ago</small>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-money-bill-wave text-success"></i>
                            </div>
                            <div class="activity-content">
                                <p><strong>Payment received:</strong> GH₵15,000 from Emma Davis</p>
                                <small class="text-muted">1 hour ago</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('patients.add') }}" class="btn btn-primary">
                            <i class="fas fa-user-plus me-2"></i>
                            Add New Patient
                        </a>
                        <a href="{{ route('visits') }}" class="btn btn-success">
                            <i class="fas fa-calendar-plus me-2"></i>
                            Record Visit
                        </a>
                        <a href="{{ route('billing') }}" class="btn btn-warning">
                            <i class="fas fa-file-invoice me-2"></i>
                            Create Bill
                        </a>
                        <a href="{{ route('appointments.add') }}" class="btn btn-info">
                            <i class="fas fa-calendar-alt me-2"></i>
                            Schedule Appointment
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Appointments -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Upcoming Appointments</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Service</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>John Doe</td>
                                    <td>Consultation</td>
                                    <td>Today</td>
                                    <td>2:00 PM</td>
                                    <td><span class="badge bg-warning">Scheduled</span></td>
                                </tr>
                                <tr>
                                    <td>Jane Smith</td>
                                    <td>Physiotherapy</td>
                                    <td>Today</td>
                                    <td>3:30 PM</td>
                                    <td><span class="badge bg-warning">Scheduled</span></td>
                                </tr>
                                <tr>
                                    <td>Robert Johnson</td>
                                    <td>Massage Therapy</td>
                                    <td>Tomorrow</td>
                                    <td>10:00 AM</td>
                                    <td><span class="badge bg-info">Confirmed</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection