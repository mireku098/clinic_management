@extends('layouts.app')

@section('title', 'Visit Details - ' . $visit->patient->first_name . ' ' . $visit->patient->last_name)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">
                <i class="fas fa-calendar-alt text-primary me-2"></i>
                Visit Details
            </h4>
            <p class="text-muted mb-0">
                {{ $visit->patient->first_name }} {{ $visit->patient->last_name }} ({{ $visit->patient->patient_code }})
            </p>
        </div>
        <div>
            <a href="{{ route('visits') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i>Back to Visits
            </a>
            <a href="{{ route('patients.context', ['patient' => $visit->patient->patient_code]) }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-user me-1"></i>Patient Context
            </a>
            <a href="{{ route('visits.edit', $visit->id) }}" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i>Edit Visit
            </a>
        </div>
    </div>

    <!-- Visit Information -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Visit Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <strong>Visit ID:</strong>
                        </div>
                        <div class="col-sm-8">
                            #{{ $visit->id }}
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-4">
                            <strong>Date:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ is_object($visit->visit_date) ? $visit->visit_date->format('M d, Y') : \Carbon\Carbon::parse($visit->visit_date)->format('M d, Y') }}
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-4">
                            <strong>Time:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ is_object($visit->visit_time) ? $visit->visit_time->format('h:i A') : \Carbon\Carbon::parse($visit->visit_time)->format('h:i A') }}
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-4">
                            <strong>Type:</strong>
                        </div>
                        <div class="col-sm-8">
                            <span class="badge bg-{{ $visit->visit_type == 'appointment' ? 'primary' : 'warning' }}">
                                {{ ucfirst($visit->visit_type) }}
                            </span>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-4">
                            <strong>Purpose:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ $visit->purpose }}
                        </div>
                    </div>
                    @if($visit->notes)
                    <div class="row mt-2">
                        <div class="col-sm-4">
                            <strong>Notes:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ $visit->notes }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-heartbeat text-danger me-2"></i>
                        Vital Signs
                    </h5>
                </div>
                <div class="card-body">
                    @if($visit->blood_pressure || $visit->temperature || $visit->heart_rate)
                        @if($visit->blood_pressure)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><i class="fas fa-heart text-danger me-2"></i>Blood Pressure</span>
                            <strong>{{ $visit->blood_pressure }}</strong>
                        </div>
                        @endif
                        @if($visit->temperature)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><i class="fas fa-thermometer-half text-info me-2"></i>Temperature</span>
                            <strong>{{ $visit->temperature }}°C</strong>
                        </div>
                        @endif
                        @if($visit->heart_rate)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><i class="fas fa-heartbeat text-danger me-2"></i>Heart Rate</span>
                            <strong>{{ $visit->heart_rate }} bpm</strong>
                        </div>
                        @endif
                    @else
                        <p class="text-muted mb-0">No vital signs recorded</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Services -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-flask text-info me-2"></i>
                Services Performed
            </h5>
        </div>
        <div class="card-body">
            @if($visit->services && $visit->services->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Service Name</th>
                                <th>Category</th>
                                <th>Result Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($visit->services as $patientService)
                            <tr>
                                <td>{{ $patientService->service ? $patientService->service->service_name : 'Unknown Service' }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $patientService->service ? ucfirst($patientService->service->category) : 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $patientService->service ? ucfirst($patientService->service->result_type) : 'N/A' }}</span>
                                </td>
                                <td>
                                    <?php
                                    $result = \App\Models\ServiceResult::where('patient_id', $visit->patient->id)
                                        ->where('visit_id', $visit->id)
                                        ->where('service_id', $patientService->service_id)
                                        ->first();
                                    ?>
                                    @if($result)
                                        <span class="badge bg-{{ $result->status === 'approved' ? 'success' : $result->status === 'draft' ? 'warning' : $result->status === 'pending_approval' ? 'info' : 'secondary' }}">
                                            {{ ucfirst($result->status) }}
                                        </span>
                                    @else
                                        <span class="badge bg-light text-dark">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @if($patientService->service)
                                    <a href="{{ route('service-results.result-page', ['patient' => $visit->patient->id, 'visit' => $visit->id, 'service' => $patientService->service_id]) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-flask me-1"></i>
                                        @if($result) Edit @else Add @endif Result
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-flask fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Services Performed</h5>
                    <p class="text-muted">No services were recorded for this visit.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Metadata -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-clock text-muted me-2"></i>
                Metadata
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-sm-4">
                            <strong>Attended By:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ $visit->attendingUser ? $visit->attendingUser->name : 'Unknown' }}
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-4">
                            <strong>Created:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ is_object($visit->created_at) ? $visit->created_at->format('M d, Y H:i') : \Carbon\Carbon::parse($visit->created_at)->format('M d, Y H:i') }}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-sm-4">
                            <strong>Last Updated:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ is_object($visit->updated_at) ? $visit->updated_at->format('M d, Y H:i') : \Carbon\Carbon::parse($visit->updated_at)->format('M d, Y H:i') }}
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-4">
                            <strong>Status:</strong>
                        </div>
                        <div class="col-sm-8">
                            <span class="badge bg-success">{{ ucfirst($visit->status) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
