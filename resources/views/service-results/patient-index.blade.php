@extends('layouts.app')

@section('title', 'Service Results - ' . $patient->first_name . ' ' . $patient->last_name)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">Service Results</h1>
            <p class="text-muted mb-0">Results and documentation for {{ $patient->first_name }} {{ $patient->last_name }} ({{ $patient->patient_code }})</p>
        </div>
        <div>
            <a href="{{ route('patients.service-results.create', $patient->id) }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add Result
            </a>
        </div>
    </div>

    <!-- Results Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-card-body">
                    <div class="stat-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="stat-details">
                        <div class="stat-number">{{ $serviceResults->count() }}</div>
                        <div class="stat-label">Total Results</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-card-body">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                    <div class="stat-details">
                        <div class="stat-number">{{ $serviceResults->where('status', 'approved')->count() }}</div>
                        <div class="stat-label">Approved</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-card-body">
                    <div class="stat-icon">
                        <i class="fas fa-clock text-warning"></i>
                    </div>
                    <div class="stat-details">
                        <div class="stat-number">{{ $serviceResults->where('status', 'pending_approval')->count() }}</div>
                        <div class="stat-label">Pending Approval</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-card-body">
                    <div class="stat-icon">
                        <i class="fas fa-times-circle text-danger"></i>
                    </div>
                    <div class="stat-details">
                        <div class="stat-number">{{ $serviceResults->where('status', 'rejected')->count() }}</div>
                        <div class="stat-label">Rejected</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Table -->
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-list me-2"></i>Service Results History</h5>
        </div>
        <div class="card-body">
            @if($serviceResults->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Service/Package</th>
                                <th>Result Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($serviceResults as $result)
                                <tr>
                                    <td>{{ $result->recorded_at ? \Carbon\Carbon::parse($result->recorded_at)->format('M d, Y') : 'N/A' }}</td>
                                    <td>
                                        @if($result->service)
                                            {{ $result->service->service_name }}
                                        @else
                                            <span class="badge bg-info">Package</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $result->status === 'approved' ? 'success' : ($result->status === 'pending_approval' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($result->result_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $result->status === 'approved' ? 'success' : ($result->status === 'pending_approval' ? 'warning' : 'danger') }}">
                                            {{ ucfirst(str_replace('_', ' ', $result->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('service-results.show', $result->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($result->status !== 'approved')
                                                {{-- Debug: Show what URL is being generated --}}
                                                {{-- Edit URL: {{ route('service-results.edit', $result->id) }} --}}
                                                {{-- Result ID: {{ $result->id }} --}}
                                                <a href="{{ route('service-results.edit', $result->id) }}" class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-edit"></i> Edit (ID: {{ $result->id }})
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No service results found for this patient.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
