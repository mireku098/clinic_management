@extends('layouts.app')

@section('title', 'Service Result Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3><i class="fas fa-clipboard-list me-2"></i>Service Result Details</h3>
                <a href="{{ request()->header('referer') ?: route('service-results.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Results
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <!-- Patient Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5><i class="fas fa-user-injured me-2"></i>Patient Information</h5>
                            <div class="p-3 bg-light rounded">
                                <p class="mb-1"><strong>Name:</strong> {{ $result->patient->first_name }} {{ $result->patient->last_name }}</p>
                                <p class="mb-1"><strong>Patient Code:</strong> {{ $result->patient->patient_code }}</p>
                                <p class="mb-0"><strong>Email:</strong> {{ $result->patient->email }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="fas fa-flask me-2"></i>Service Information</h5>
                            <div class="p-3 bg-light rounded">
                                @if($result->service)
                                    <p class="mb-1"><strong>Service:</strong> {{ $result->service->service_name }}</p>
                                    <p class="mb-1"><strong>Category:</strong> {{ ucfirst($result->service->category) }}</p>
                                    <p class="mb-1"><strong>Price:</strong> ${{ number_format($result->service->price, 2) }}</p>
                                @else
                                    <p class="mb-1"><strong>Package:</strong> {{ $result->package->package_name ?? 'Unknown Package' }}</p>
                                    <p class="mb-1"><strong>Category:</strong> Package</p>
                                    <p class="mb-1"><strong>Price:</strong> ${{ number_format($result->package->price ?? 0, 2) }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Visit Information -->
                    @if($result->visit)
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5><i class="fas fa-calendar me-2"></i>Visit Information</h5>
                                <div class="p-3 bg-light rounded">
                                    <p class="mb-1"><strong>Visit #:</strong> {{ $result->visit->id }}</p>
                                    <p class="mb-1"><strong>Date:</strong> {{ $result->visit->visit_date ? \Carbon\Carbon::parse($result->visit->visit_date)->format('F d, Y') : 'No date' }}</p>
                                    <p class="mb-1"><strong>Time:</strong> {{ $result->visit->visit_time ? \Carbon\Carbon::parse($result->visit->visit_time)->format('H:i') : 'No time' }}</p>
                                    <p class="mb-1"><strong>Type:</strong> {{ ucfirst($result->visit->visit_type) }}</p>
                                    <p class="mb-0"><strong>Practitioner:</strong> {{ $result->visit->practitioner ?? 'Not assigned' }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Result Details -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5><i class="fas fa-clipboard-check me-2"></i>Result Details</h5>
                            <div class="p-3 bg-light rounded">
                                <div class="row">
                                    <div class="col-md-4">
                                        <p class="mb-1"><strong>Result Type:</strong> 
                                            <span class="badge bg-info ms-1">{{ ucfirst($result->result_type) }}</span>
                                        </p>
                                        <p class="mb-1"><strong>Status:</strong> 
                                            <span class="badge bg-{{ $result->status === 'approved' ? 'success' : ($result->status === 'pending_approval' ? 'warning' : ($result->status === 'rejected' ? 'danger' : 'secondary')) }}">
                                                {{ ucfirst(str_replace('_', ' ', $result->status)) }}
                                            </span>
                                        </p>
                                        <p class="mb-1"><strong>Recorded Date:</strong> {{ $result->recorded_at ? \Carbon\Carbon::parse($result->recorded_at)->format('F d, Y H:i') : 'No date' }}</p>
                                    </div>
                                    <div class="col-md-8">
                                        <p class="mb-1"><strong>Result Value:</strong></p>
                                        <div class="p-3 bg-white border rounded">
                                            @if ($result->result_type === 'text')
                                                <p class="mb-0">{{ $result->result_text }}</p>
                                            @elseif ($result->result_type === 'numeric')
                                                <h3 class="text-primary mb-0">{{ $result->result_numeric }}</h3>
                                            @elseif ($result->result_type === 'file')
                                                @if ($result->result_file_path)
                                                    <a href="{{ asset('storage/' . $result->result_file_path) }}" target="_blank" class="btn btn-primary">
                                                        <i class="fas fa-download me-2"></i>Download {{ $result->result_file_name }}
                                                    </a>
                                                @else
                                                    <p class="text-muted">No file uploaded</p>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    @if($result->notes)
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5><i class="fas fa-sticky-note me-2"></i>Additional Information</h5>
                                <div class="p-3 bg-light rounded">
                                    <p class="mb-0"><strong>Notes:</strong></p>
                                    <div class="p-3 bg-white border rounded">
                                        <p class="mb-0">{{ $result->notes }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Approval Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5><i class="fas fa-check-circle me-2"></i>Approval Information</h5>
                            <div class="p-3 bg-light rounded">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Recorded By:</strong> {{ optional($result->recorder)->name ?? 'Unknown' }}</p>
                                        <p class="mb-0"><strong>Created:</strong> {{ $result->created_at ? \Carbon\Carbon::parse($result->created_at)->format('F d, Y H:i') : 'No date' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        @if($result->approved_at)
                                            <p class="mb-1"><strong>Approved By:</strong> {{ optional($result->approver)->name ?? 'Unknown' }}</p>
                                            <p class="mb-0"><strong>Approved At:</strong> {{ \Carbon\Carbon::parse($result->approved_at)->format('F d, Y H:i') }}</p>
                                        @else
                                            <p class="mb-0 text-muted"><em>Not yet approved</em></p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                @if ($result->isEditable())
                                    <a href="{{ route('service-results.edit', $result->id) }}" class="btn btn-warning">
                                        <i class="fas fa-edit me-2"></i>Edit Result
                                    </a>
                                @endif
                                
                                @if ($result->status === 'draft')
                                    <form method="POST" action="{{ route('service-results.submit-approval', $result->id) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-paper-plane me-2"></i>Submit for Approval
                                        </button>
                                    </form>
                                @endif
                                
                                @if (auth()->user() && auth()->user()->role === 'admin' && $result->status === 'pending_approval')
                                    <form method="POST" action="{{ route('service-results.approve', $result->id) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check me-2"></i>Approve Result
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
