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

    <!-- Results Timeline -->
    <div class="row">
        <div class="col-12">
            @if($serviceResults->count() > 0)
                <div class="timeline">
                    @foreach($serviceResults as $result)
                        <div class="timeline-item">
                            <div class="timeline-marker">
                                <div class="timeline-icon result-type-{{ $result->result_type }}">
                                    @if ($result->result_type === 'text')
                                        <i class="fas fa-file-alt"></i>
                                    @elseif ($result->result_type === 'numeric')
                                        <i class="fas fa-calculator"></i>
                                    @elseif ($result->result_type === 'file')
                                        <i class="fas fa-file-pdf"></i>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="timeline-content">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="result-type-icon result-type-{{ $result->result_type }}">
                                                @if ($result->result_type === 'text')
                                                    <i class="fas fa-file-alt"></i>
                                                @elseif ($result->result_type === 'numeric')
                                                    <i class="fas fa-calculator"></i>
                                                @elseif ($result->result_type === 'file')
                                                    <i class="fas fa-file-pdf"></i>
                                                @endif
                                            </div>
                                            <span class="badge status-badge bg-{{ $result->status === 'approved' ? 'success' : ($result->status === 'pending_approval' ? 'warning' : ($result->status === 'rejected' ? 'danger' : 'secondary')) }}">
                                                {{ ucfirst(str_replace('_', ' ', $result->status)) }}
                                            </span>
                                        </div>
                                        
                                        <!-- Service/Package Name -->
                                        <h6 class="card-title">
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
                                        
                                        <p class="card-text text-muted small mb-2">
                                            <i class="fas fa-user me-1"></i> {{ $result->patient->first_name }} {{ $result->patient->last_name }}
                                            @if ($result->visit)
                                                <span class="ms-2"><i class="fas fa-calendar me-1"></i> Visit #{{ $result->visit->id }} - {{ $result->visit->created_at->format('M d, Y H:i') }}</span>
                                            @endif
                                        </p>
                                        
                                        <div class="result-value mb-3">
                                            @if ($result->result_type === 'text')
                                                <p class="mb-0">{{ Str::limit($result->result_text, 200) }}</p>
                                            @elseif ($result->result_type === 'numeric')
                                                <h5 class="text-primary mb-0">{{ $result->result_numeric }}</h5>
                                            @elseif ($result->result_type === 'file')
                                                <a href="{{ asset('storage/' . $result->result_file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-download me-1"></i> {{ $result->result_file_name }}
                                                </a>
                                            @endif
                                        </div>
                                        
                                        @if ($result->notes)
                                            <p class="card-text text-muted small mb-3">
                                                <i class="fas fa-sticky-note me-1"></i> {{ Str::limit($result->notes, 100) }}
                                            </p>
                                        @endif
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i> {{ $result->created_at->format('M d, Y H:i') }}
                                            </small>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('service-results.show', $result->id) }}" class="btn btn-outline-primary" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                @if ($result->isEditable())
                                                    <a href="{{ route('service-results.edit', $result->id) }}" class="btn btn-outline-secondary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    @if ($result->status === 'draft')
                                                        <form action="{{ route('service-results.submit-approval', $result->id) }}" method="POST" style="display: inline;" class="submit-approval-form">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-warning" title="Submit for Approval">
                                                                <i class="fas fa-paper-plane"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                                
                                                @if ($result->status === 'pending_approval')
                                                    <button type="button" class="btn btn-outline-success" title="Approve" onclick="approveResult({{ $result->id }}, 'approve')">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger" title="Reject" onclick="approveResult({{ $result->id }}, 'reject')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                                
                                                @if ($result->isEditable())
                                                    <form action="{{ route('service-results.destroy', $result->id) }}" method="POST" style="display: inline;" class="delete-result-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this result?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
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
    
    <!-- Add Result Button -->
    <div class="fixed-bottom-btn">
        @if($availableItems && count($availableItems) > 0)
            <div class="card">
                <div class="card-body">
                    <h5><i class="fas fa-plus me-2"></i>Add New Result</h5>
                    <div class="row">
                        @foreach($availableItems as $item)
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        @if($item['type'] === 'service')
                                            <h6 class="text-primary">
                                                <i class="fas fa-flask me-2"></i>{{ $item['service']->service_name }}
                                            </h6>
                                            <p class="text-muted small">
                                                Service from Visit #{{ $item['visit']->id }} on {{ $item['visit']->created_at->format('M d, Y') }}
                                            </p>
                                        @elseif($item['type'] === 'package')
                                            <h6 class="text-primary">
                                                <i class="fas fa-box me-2"></i>{{ $item['package']->package_name }}
                                            </h6>
                                            <p class="text-muted small">
                                                Package from Visit #{{ $item['visit']->id }} on {{ $item['visit']->created_at->format('M d, Y') }}
                                            </p>
                                        @endif
                                        
                                        <a href="{{ route('service-results.create') }}?patient_id={{ $patient->id }}{{ $item['type'] === 'service' ? '&service_id=' . $item['service']->id : '&package_id=' . $item['package']->id }}{{ isset($item['patient_service_id']) ? '&patient_service_id=' . $item['patient_service_id'] : '' }}" 
                                           class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>Add {{ ucfirst($item['type']) }} Result
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Alert Container -->
<div id="alert-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1050"></div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -15px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #f8f9fa;
    border: 3px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1;
}

.timeline-content {
    margin-left: 30px;
    position: relative;
}

.timeline-icon {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
}

.result-type-text { background: #007bff; }
.result-type-numeric { background: #28a745; }
.result-type-file { background: #dc3545; }

.status-badge {
    font-size: 11px;
    padding: 4px 8px;
    border-radius: 12px;
}

.fixed-bottom-btn {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
</style>

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
