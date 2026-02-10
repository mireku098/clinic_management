@extends('layouts.app')

@section('title', 'Service Results Timeline')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">
                <i class="fas fa-flask text-info me-2"></i>
                Service Results Timeline
            </h4>
            <p class="text-muted mb-0 mt-1">
                Patient: <strong>{{ $patient->first_name }} {{ $patient->last_name }}</strong> 
                ({{ $patient->patient_code }})
            </p>
        </div>
        <a href="{{ route('visits') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Visits
        </a>
    </div>

    @php $mostRecentVisit = $visits->first(); @endphp

    <div class="row">
        <!-- Service Results Timeline -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-history text-primary me-2"></i>
                        Results History
                    </h5>
                </div>
                <div class="card-body">
                    @if($serviceResults->count() > 0)
                        <!-- Timeline -->
                        <div class="timeline">
                            @foreach($serviceResults as $result)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-{{ $result->status === 'approved' ? 'success' : $result->status === 'draft' ? 'warning' : 'secondary' }}">
                                    <i class="fas fa-flask"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h6 class="mb-1">
                                                        @if($result->package)
                                                            <span class="badge bg-primary me-2">
                                                                <i class="fas fa-box me-1"></i>Package
                                                            </span>
                                                            {{ $result->package->package_name }}
                                                        @elseif($result->service)
                                                            {{ $result->service->service_name }}
                                                        @else
                                                            Unknown Service/Package
                                                        @endif
                                                    </h6>
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        {{ is_object($result->created_at) ? $result->created_at->format('M d, Y H:i') : \Carbon\Carbon::parse($result->created_at)->format('M d, Y H:i') }}
                                                    </small>
                                                </div>
                                                <span class="badge bg-{{ $result->status === 'approved' ? 'success' : $result->status === 'draft' ? 'warning' : $result->status === 'pending_approval' ? 'info' : 'secondary' }}">
                                                    {{ ucfirst($result->status) }}
                                                </span>
                                            </div>
                                            
                                            <div class="row mb-2">
                                                <div class="col-md-6">
                                                    <small class="text-muted">Result Type:</small>
                                                    <span class="badge bg-info ms-1">{{ ucfirst($result->result_type) }}</span>
                                                </div>
                                                <div class="col-md-6">
                                                    <small class="text-muted">Visit:</small>
                                                    <a href="{{ route('visits.show', $result->visit_id) }}" class="ms-1">
                                                        {{ is_object($result->visit->visit_date) ? $result->visit->visit_date->format('M d, Y') : \Carbon\Carbon::parse($result->visit->visit_date)->format('M d, Y') }}
                                                    </a>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-2">
                                                <small class="text-muted">Result Value:</small>
                                                <div class="mt-1">
                                                    @if($result->result_type === 'text')
                                                        <p class="mb-0">{{ $result->result_text }}</p>
                                                    @elseif($result->result_type === 'numeric')
                                                        <span class="h5 text-primary">{{ $result->result_numeric }}</span>
                                                    @elseif($result->result_type === 'file' && $result->result_file_path)
                                                        <a href="/storage/{{ $result->result_file_path }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-download me-1"></i>Download File
                                                        </a>
                                                    @else
                                                        <span class="text-muted">No result value</span>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            @if($result->notes)
                                            <div class="mb-2">
                                                <small class="text-muted">Notes:</small>
                                                <p class="mb-0">{{ $result->notes }}</p>
                                            </div>
                                            @endif
                                            
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    Recorded by: {{ $result->recorder ? $result->recorder->name : 'System' }}
                                                </small>
                                                <div>
                                                    @if($result->service)
                                                    <a href="{{ route('service-results.result-page', ['patient' => $result->patient_id, 'visit' => $result->visit_id, 'service' => $result->service_id]) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit me-1"></i>View/Edit
                                                    </a>
                                                @else
                                                    <a href="{{ route('service-results.create') }}?patient_id={{ $result->patient_id }}&package_id={{ $result->package_id }}&visit_id={{ $result->visit_id }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit me-1"></i>View/Edit
                                                    </a>
                                                @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-flask fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Service Results Yet</h5>
                            <p class="text-muted">Service results will appear here once added.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Add New Result Panel -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle text-success me-2"></i>
                        Add Service & Package Results
                        @if($mostRecentVisit)
                            <small class="text-muted ms-2">
                                (From visit on {{ is_object($mostRecentVisit->visit_date) ? $mostRecentVisit->visit_date->format('M d, Y') : \Carbon\Carbon::parse($mostRecentVisit->visit_date)->format('M d, Y') }})
                            </small>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Show Services -->
                    @if($availableServices)
                        @php $hasServices = false; @endphp
                        @foreach($availableServices as $available)
                            @if(isset($available['service']))
                                @if(!$hasServices)
                                    <h6 class="text-info mb-3">
                                        <i class="fas fa-flask me-2"></i>Available Services
                                    </h6>
                                    @php $hasServices = true; @endphp
                                @endif
                                
                                <div class="border rounded p-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1">
                                                <span class="badge bg-info me-2">
                                                    <i class="fas fa-flask me-1"></i>Service
                                                </span>
                                                {{ $available['service']->service_name }}
                                            </h6>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                Visit: {{ is_object($available['visit']->visit_date) ? $available['visit']->visit_date->format('M d, Y') : \Carbon\Carbon::parse($available['visit']->visit_date)->format('M d, Y') }}
                                            </small>
                                        </div>
                                        <span class="badge bg-info">{{ ucfirst($available['service']->result_type) }}</span>
                                    </div>
                                    
                                    <a href="{{ route('service-results.result-page', ['patient' => $patient->id, 'visit' => $available['visit']->id, 'service' => $available['service']->id]) }}" 
                                       class="btn btn-sm btn-primary w-100">
                                        <i class="fas fa-plus me-1"></i>Add Service Result
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    @endif
                    
                    <!-- Show Package -->
                    @if($availableServices)
                        @php $hasPackage = false; @endphp
                        @foreach($availableServices as $available)
                            @if(isset($available['package']))
                                @if(!$hasPackage)
                                    @if($hasServices)
                                        <hr>
                                    @endif
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-box me-2"></i>Available Package
                                    </h6>
                                    @php $hasPackage = true; @endphp
                                @endif
                                
                                <div class="border rounded p-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1">
                                                <span class="badge bg-primary me-2">
                                                    <i class="fas fa-box me-1"></i>Package
                                                </span>
                                                {{ $available['package']->package_name }}
                                            </h6>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                Visit: {{ is_object($available['visit']->visit_date) ? $available['visit']->visit_date->format('M d, Y') : \Carbon\Carbon::parse($available['visit']->visit_date)->format('M d, Y') }}
                                            </small>
                                        </div>
                                        <span class="badge bg-info">Text</span>
                                    </div>
                                    
                                    <a href="{{ route('service-results.create') }}?patient_id={{ $patient->id }}&package_id={{ $available['package']->id }}&visit_id={{ $available['visit']->id }}" 
                                       class="btn btn-sm btn-primary w-100">
                                        <i class="fas fa-plus me-1"></i>Add Package Result
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    @endif
                    
                    <!-- No Services or Packages -->
                    @if(!$availableServices)
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h6 class="text-muted">All Results Recorded</h6>
                            <p class="text-muted small">All services and packages for this patient have results recorded.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar text-warning me-2"></i>
                        Quick Stats
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="border-end">
                                <h4 class="mb-1 text-primary">{{ $serviceResults->count() }}</h4>
                                <small class="text-muted">Total</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border-end">
                                <h4 class="mb-1 text-success">{{ $serviceResults->where('status', 'approved')->count() }}</h4>
                                <small class="text-muted">Approved</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <h4 class="mb-1 text-warning">{{ $serviceResults->where('status', 'pending_approval')->count() }}</h4>
                            <small class="text-muted">Pending</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
    border: 2px solid white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.timeline-content {
    margin-left: 0;
}
</style>
@endsection
