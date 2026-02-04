@extends('layouts.app')
@section('title', 'Edit Visit')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
      <div>
        <h1>Edit Visit</h1>
        <p class="text-muted">Update visit information for {{ $visit->patient->first_name }} {{ $visit->patient->last_name }}</p>
      </div>
      <div>
        <a href="{{ route('visits') }}" class="btn btn-outline-secondary">
          <i class="fas fa-arrow-left me-2"></i>
          Back to Visits
        </a>
      </div>
    </div>

    <!-- Edit Visit Form -->
    <div class="row">
        <div class="col-lg-8">
            @include('visits._form', ['action' => isset($visit) ? route('visits.update', $visit->id) : route('visits.store')])
        </div>
    </div>
  </div>
@endsection
