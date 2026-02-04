@extends('layouts.app')

@section('title', 'Patient Visits - ' . $patient->first_name . ' ' . $patient->last_name)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">
                <i class="fas fa-calendar-alt text-primary me-2"></i>
                Patient Visits
            </h4>
            <p class="text-muted mb-0">{{ $patient->first_name }} {{ $patient->last_name }} ({{ $patient->patient_code }})</p>
        </div>
        <div>
            <a href="{{ route('patients.context', ['patient' => $patient->patient_code]) }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i>Back to Context
            </a>
            <a href="{{ route('visits.add') }}?patient={{ $patient->patient_code }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Add Visit
            </a>
        </div>
    </div>

    <!-- Visits List -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>
                Visit History
            </h5>
        </div>
        <div class="card-body">
            @if($visits->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Visit Date</th>
                                <th>Purpose</th>
                                <th>Type</th>
                                <th>Services</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($visits as $visit)
                            <tr>
                                <td>{{ $visit->visit_date->format('M d, Y') }}</td>
                                <td>{{ $visit->purpose }}</td>
                                <td>
                                    <span class="badge bg-info">{{ ucfirst($visit->visit_type) }}</span>
                                </td>
                                <td>
                                    @if($visit->services && $visit->services->count() > 0)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($visit->services as $service)
                                                <span class="badge bg-secondary">{{ $service->service_name }}</span>
                                                @if(!$loop->last)
                                                    <a href="{{ route('service-results.result-page', ['patient' => $patient->id, 'visit' => $visit->id, 'service' => $service->id]) }}" 
                                                       class="btn btn-xs btn-outline-primary" title="Add Result">
                                                        <i class="fas fa-flask"></i>
                                                    </a>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">No services</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-success">{{ ucfirst($visit->status) }}</span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('visits.edit', $visit->id) }}" class="btn btn-outline-primary" title="Edit Visit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($visit->services && $visit->services->count() > 0)
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-info dropdown-toggle" data-bs-toggle="dropdown" title="Service Results">
                                                    <i class="fas fa-flask"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    @foreach($visit->services as $service)
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('service-results.result-page', ['patient' => $patient->id, 'visit' => $visit->id, 'service' => $service->id]) }}">
                                                                <i class="fas fa-plus me-2"></i>{{ $service->service_name }} Result
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No visits recorded</h5>
                    <p class="text-muted">This patient hasn't had any visits yet.</p>
                    <a href="{{ route('visits.add') }}?patient={{ $patient->patient_code }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Record First Visit
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
