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
                    <h3>{{ $totalPatients }}</h3>
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
                    <h3>{{ $todayVisits }}</h3>
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
                    <h3>{{ $activePackages }}</h3>
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
                    <h3>GH₵{{ number_format($todayRevenue, 2) }}</h3>
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
                        @forelse ($recentActivity as $activity)
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="{{ $activity['icon'] }}"></i>
                            </div>
                            <div class="activity-content">
                                <p><strong>{{ $activity['description'] }}</strong></p>
                                <small class="text-muted">{{ $activity['time'] }}</small>
                            </div>
                        </div>
                        @empty
                        <div class="text-muted text-center py-3">
                            <p>No recent activity</p>
                        </div>
                        @endforelse
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
                                @forelse ($upcomingAppointments as $appointment)
                                <tr>
                                    <td>{{ $appointment['patient_name'] }}</td>
                                    <td>{{ $appointment['service'] }}</td>
                                    <td>{{ $appointment['date'] }}</td>
                                    <td>{{ $appointment['time'] }}</td>
                                    <td><span class="badge bg-{{ $appointment['status'] == 'Scheduled' ? 'warning' : 'info' }}">{{ $appointment['status'] }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No upcoming appointments</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection