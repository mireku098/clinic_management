@extends('layouts.app')

@section('title', 'Record New Visit')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1>Record New Visit</h1>
            <p class="text-muted">Add a new patient visit record</p>
        </div>
        <div>
            <a href="{{ route('visits') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Visits
            </a>
        </div>
    </div>

    <!-- Visit Form -->
    <div class="row">
        <div class="col-lg-8">
            @include('visits._form', ['action' => route('visits.store')])
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Stats -->
            <!-- <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-chart-line me-2"></i>Today's Stats</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Total Visits</span>
                        <span class="badge bg-primary">12</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Completed</span>
                        <span class="badge bg-success">8</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Pending</span>
                        <span class="badge bg-warning">3</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Emergency</span>
                        <span class="badge bg-danger">1</span>
                    </div>
                </div>
            </div> -->

            <!-- Recent Patients -->
            <!-- <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-users me-2"></i>Recent Patients</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-0">John Doe</h6>
                                <small class="text-muted">P001</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary"
                                onclick="selectQuickPatient('1', 'John Doe', 'P001')">
                                Select
                            </button>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-0">Jane Smith</h6>
                                <small class="text-muted">P002</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary"
                                onclick="selectQuickPatient('2', 'Jane Smith', 'P002')">
                                Select
                            </button>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-0">Michael Johnson</h6>
                                <small class="text-muted">P003</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary"
                                onclick="selectQuickPatient('3', 'Michael Johnson', 'P003')">
                                Select
                            </button>
                        </div>
                    </div>
                </div>
            </div> -->

            <!-- Help -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-question-circle me-2"></i>Help</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled small">
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Search patient or use quick select
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Record vital signs accurately
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Be thorough in clinical notes
                        </li>
                        <li>
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Double-check all information
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
