@extends('layouts.app')

@section('title', 'Patient Service Results Timeline')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-0">{{ $patient->first_name }} {{ $patient->last_name }} - Service Results</h2>
            <p class="text-muted mb-0">Patient Code: {{ $patient->patient_code }}</p>
        </div>
        <div>
            <a href="{{ route('service-results.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to All Results
            </a>
        </div>
    </div>

    <!-- Results Timeline and Add Result Panel -->
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
                                    <div class="timeline-marker bg-warning">
                                        <i class="fas fa-flask"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <h6 class="mb-1">
                                                            @if($result->package)
                                                                {{ $result->package->package_name }}
                                                            @elseif($result->service)
                                                                {{ $result->service->service_name }}
                                                            @else
                                                                Unknown Service/Package
                                                            @endif
                                                        </h6>
                                                        <small class="text-muted">
                                                            <i class="fas fa-calendar me-1"></i>
                                                            {{ optional($result->created_at)->format('M d, Y H:i') }}
                                                        </small>
                                                    </div>
                                                    <span class="badge bg-info">
                                                        {{ ucfirst(str_replace('_', ' ', $result->status)) }}
                                                    </span>
                                                </div>
                                                
                                                <div class="row mb-2">
                                                    <div class="col-md-6">
                                                        <small class="text-muted">Result Type:</small>
                                                        <span class="badge bg-info ms-1">{{ ucfirst($result->result_type) }}</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <small class="text-muted">Visit:</small>
                                                        @if ($result->visit)
                                                            <a href="{{ route('visits.show', $result->visit->id) }}" class="ms-1">
                                                                Visit #{{ $result->visit->id }} - {{ $result->visit->visit_date ? \Carbon\Carbon::parse($result->visit->visit_date)->format('M d, Y') : 'No date' }} @ {{ $result->visit->visit_time ? \Carbon\Carbon::parse($result->visit->visit_time)->format('H:i') : 'No time' }}
                                                            </a>
                                                        @else
                                                            <span class="ms-1">No visit</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-2">
                                                    <small class="text-muted">Result Value:</small>
                                                    <div class="mt-1">
                                                        @if ($result->result_type === 'text')
                                                            <p class="mb-0">{{ Str::limit($result->result_text, 200) }}</p>
                                                        @elseif ($result->result_type === 'numeric')
                                                            <span class="h5 text-primary">{{ $result->result_numeric }}</span>
                                                        @elseif ($result->result_type === 'file')
                                                            <a href="{{ asset('storage/' . $result->result_file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-download me-1"></i>Download File
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                @if ($result->notes)
                                                    <div class="mb-2">
                                                        <small class="text-muted">Notes:</small>
                                                        <p class="mb-0">{{ Str::limit($result->notes, 100) }}</p>
                                                    </div>
                                                @endif
                                                
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">
                                                        Recorded by: {{ optional($result->recorder)->name ?? 'Unknown' }}
                                                    </small>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('patients.service-results.show', [$patient->id, $result->id]) }}" class="btn btn-outline-primary">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                        @if ($result->isEditable())
                                                            <a href="{{ route('patients.service-results.edit', [$patient->id, $result->id]) }}" class="btn btn-outline-secondary">
                                                                <i class="fas fa-edit"></i> Edit
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
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No Service Results Found</h4>
                            <p class="text-muted">This patient has no service or package results recorded yet.</p>
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
                        @if($visits && $visits->count() > 0)
                            <small class="text-muted ms-2">
                                (From visit on {{ optional($visits->first()->created_at)->format('M d, Y') }})
                            </small>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    @if($availableServices && count($availableServices) > 0)
                        @foreach($availableServices as $item)
                            @if(isset($item['package']))
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-box me-2"></i>Available Package
                                </h6>
                                
                                <div class="border rounded p-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1">
                                                <span class="badge bg-primary me-2">
                                                    <i class="fas fa-box me-1"></i>Package
                                                </span>
                                                {{ $item['package']->package_name }}
                                            </h6>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                Visit: {{ optional($item['visit']->created_at)->format('M d, Y') }}
                                            </small>
                                        </div>
                                        <span class="badge bg-info">{{ ucfirst($item['package']->default_result_type ?? 'Text') }}</span>
                                    </div>
                                    
                                    <a href="{{ route('service-results.create') }}?patient_id={{ $patient->id }}&package_id={{ $item['package']->id }}&visit_id={{ $item['visit']->id }}" 
                                       class="btn btn-sm btn-primary w-100">
                                        <i class="fas fa-plus me-1"></i>Add Package Result
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <p class="text-muted">No available services or packages to add results for.</p>
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

<!-- Alert Container -->
<div id="alert-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1050"></div>

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

@section('js')
<script>
// Auto-refresh every 30 seconds for real-time updates
setInterval(() => {
    fetch('{{ route('service-results.patient-timeline', $patient->id) }}', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.html) {
            document.querySelector('.timeline').innerHTML = data.html;
        }
    })
    .catch(error => console.error('Error:', error));
}, 30000); // 30 seconds
</script>
@endsection
