@extends('layouts.app')
@section('title', 'Payments')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
      <h1>Payments (Admin)</h1>
      <p class="text-muted">Global payment overview and administrative reporting</p>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-success">
            <i class="fas fa-receipt"></i>
          </div>
          <div class="stat-details">
            <h3>487</h3>
            <p>Total Payments</p>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-primary">
            <i class="fas fa-dollar-sign"></i>
          </div>
          <div class="stat-details">
            <h3>$125,450</h3>
            <p>Total Revenue</p>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-info">
            <i class="fas fa-credit-card"></i>
          </div>
          <div class="stat-details">
            <h3>85.4%</h3>
            <p>Avg. Payment Rate</p>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-warning">
            <i class="fas fa-calendar"></i>
          </div>
          <div class="stat-details">
            <h3>$8,945</h3>
            <p>Today's Revenue</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Advanced Filters -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="card-title mb-0">
          <i class="fas fa-filter me-2"></i>Filters & Search
        </h5>
      </div>
      <div class="card-body">
        <form id="paymentsFiltersForm" class="row g-3">
          <div class="col-md-3">
            <label for="payment_patient_filter" class="form-label">Patient</label>
            <input type="text" class="form-control" id="payment_patient_filter" placeholder="Search by name or ID..." />
          </div>
          <div class="col-md-2">
            <label for="payment_method_filter" class="form-label">Payment Method</label>
            <select class="form-select" id="payment_method_filter">
              <option value="">All Methods</option>
              <option value="cash">Cash</option>
              <option value="card">Card</option>
              <option value="bank">Bank Transfer</option>
              <option value="mobile">Mobile Money</option>
            </select>
          </div>
          <div class="col-md-2">
            <label for="payment_status_filter" class="form-label">Status</label>
            <select class="form-select" id="payment_status_filter">
              <option value="">All Status</option>
              <option value="completed">Completed</option>
              <option value="pending">Pending</option>
              <option value="failed">Failed</option>
            </select>
          </div>
          <div class="col-md-2">
            <label for="payment_date_from" class="form-label">Date From</label>
            <input type="date" class="form-control" id="payment_date_from" />
          </div>
          <div class="col-md-2">
            <label for="payment_date_to" class="form-label">Date To</label>
            <input type="date" class="form-control" id="payment_date_to" />
          </div>
          <div class="col-md-1 d-flex align-items-end">
            <button type="button" class="btn btn-primary w-100">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Payments Table -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
          <i class="fas fa-list me-2"></i>Payment Records
        </h5>
        <div class="d-flex gap-2">
          <input type="text" class="form-control" placeholder="Quick search..." style="width: 200px" id="quickSearchPayment" />
          <button class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-refresh me-1"></i>Clear
          </button>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover" id="paymentsTable">
            <thead>
              <tr>
                <th>Payment ID</th>
                <th>Patient</th>
                <th>Amount</th>
                <th>Payment Method</th>
                <th>Status</th>
                <th>Payment Date</th>
                <th>Reference</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="paymentsTableBody">
              <!-- Payment records will be populated here -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection
