@extends('layouts.app')

@section('title', 'Session Expired')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Session Expired
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                    </div>
                    
                    <h4>Your session has expired</h4>
                    <p class="text-muted mb-4">
                        For security reasons, your session has expired due to inactivity. 
                        This helps protect your account and keeps the system secure.
                    </p>
                    
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>What happened?</strong>
                        <p class="mb-2">Your session automatically expired after {{ config('session.lifetime') }} minutes of inactivity.</p>
                        <p class="mb-0">This is a security feature to prevent unauthorized access.</p>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Login Again
                        </a>
                        
                        <a href="{{ route('logout.get') }}" class="btn btn-outline-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: none;
}

.card-header.bg-warning {
    background: linear-gradient(135deg, #f8d735, #f5c6cb);
    color: white;
}

.fa-clock {
    animation: pulse 2s infinite;
}
</style>
@endsection
