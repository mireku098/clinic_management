@extends('layouts.app')

@section('title', $patient->first_name . ' ' . $patient->last_name . ' - ' . $patient->patient_code)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">{{ $patient->first_name }} {{ $patient->last_name }}</h1>
            <p class="text-muted mb-0">Patient Code: {{ $patient->patient_code }}</p>
        </div>
        <div>
            <a href="{{ route('patients') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Patients
            </a>
        </div>
    </div>

    <!-- Patient Information -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-user me-2"></i>Patient Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Patient Code:</strong></td>
                            <td>{{ $patient->patient_code }}</td>
                        </tr>
                        <tr>
                            <td><strong>Name:</strong></td>
                            <td>{{ $patient->first_name }} {{ $patient->last_name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>{{ $patient->email }}</td>
                        </tr>
                        <tr>
                            <td><strong>Phone:</strong></td>
                            <td>{{ $patient->phone ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Age:</strong></td>
                            <td>{{ $patient->age ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Gender:</strong></td>
                            <td>{{ $patient->gender ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Registered:</strong></td>
                            <td>{{ $patient->created_at ? $patient->created_at->format('M d, Y') : 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-chart-line me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('visits.add') }}?patient_id={{ $patient->id }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>New Visit
                        </a>
                        <a href="{{ route('patients.service-results.create', $patient->id) }}" class="btn btn-info">
                            <i class="fas fa-clipboard-list me-2"></i>Add Service Result
                        </a>
                        <a href="{{ route('patients.visits', $patient->patient_code) }}" class="btn btn-success">
                            <i class="fas fa-calendar me-2"></i>View Visits
                        </a>
                        <a href="{{ route('patients.service-results', $patient->id) }}" class="btn btn-warning">
                            <i class="fas fa-file-medical me-2"></i>Service Results
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-history me-2"></i>Recent Activity</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="text-center">
                        <h3 class="text-primary">{{ $patient->visits()->count() }}</h3>
                        <p class="text-muted">Total Visits</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <h3 class="text-success">{{ $patient->serviceResults()->count() }}</h3>
                        <p class="text-muted">Service Results</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <h3 class="text-info">{{ $patient->bills()->count() }}</h3>
                        <p class="text-muted">Bills</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
