@extends('layouts.app')
@section('title', 'User Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
      <div>
        <h1>User Management</h1>
        <p class="text-muted">Manage system users and their roles</p>
      </div>
      <div>
        <button
          type="button"
          class="btn btn-primary"
          data-bs-toggle="modal"
          data-bs-target="#addUserModal"
        >
          <i class="fas fa-user-plus me-2"></i>
          Add New User
        </button>
      </div>
    </div>

    <!-- User Stats -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-primary">
            <i class="fas fa-users"></i>
          </div>
          <div class="stat-details">
            <h3>12</h3>
            <p>Total Users</p>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-success">
            <i class="fas fa-user-check"></i>
          </div>
          <div class="stat-details">
            <h3>10</h3>
            <p>Active Users</p>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-warning">
            <i class="fas fa-user-clock"></i>
          </div>
          <div class="stat-details">
            <h3>2</h3>
            <p>Inactive Users</p>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-info">
            <i class="fas fa-user-shield"></i>
          </div>
          <div class="stat-details">
            <h3>4</h3>
            <p>Admins</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label for="user_search" class="form-label">Search Users</label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="fas fa-search"></i>
              </span>
              <input
                type="text"
                class="form-control"
                id="user_search"
                placeholder="Search by name, email, or role..."
              />
            </div>
          </div>
          <div class="col-md-2">
            <label for="role_filter" class="form-label">Role</label>
            <select class="form-select" id="role_filter">
              <option value="">All Roles</option>
              <option value="admin">Admin</option>
              <option value="doctor">Doctor</option>
              <option value="nurse">Nurse</option>
              <option value="receptionist">Receptionist</option>
            </select>
          </div>
          <div class="col-md-2">
            <label for="status_filter" class="form-label">Status</label>
            <select class="form-select" id="status_filter">
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
          <div class="col-md-2">
            <label for="department_filter" class="form-label">Department</label>
            <select class="form-select" id="department_filter">
              <option value="">All Departments</option>
              <option value="admin">Administration</option>
              <option value="medical">Medical</option>
              <option value="nursing">Nursing</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">&nbsp;</label>
            <button type="button" class="btn btn-outline-primary w-100">
              <i class="fas fa-filter me-2"></i>
              Apply Filters
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Users Table -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5>User List</h5>
        <div>
          <button type="button" class="btn btn-sm btn-outline-success me-2">
            <i class="fas fa-file-excel me-1"></i>
            Export Excel
          </button>
          <button type="button" class="btn btn-sm btn-outline-danger">
            <i class="fas fa-file-pdf me-1"></i>
            Export PDF
          </button>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Department</th>
                <th>Status</th>
                <th>Last Login</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <!-- User records will be populated here -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection
