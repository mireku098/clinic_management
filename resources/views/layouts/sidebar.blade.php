<!-- Sidebar Component -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-inner">
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Dashboard
                    </a>
                </li>

                <!-- Patients Section -->
                <li class="nav-item">
                    <span class="nav-section">Patients</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('patients') }}">
                        <i class="fas fa-users me-2"></i>
                        All Patients
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('patients.add') }}">
                        <i class="fas fa-user-plus me-2"></i>
                        Add Patient
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('visits') }}">
                        <i class="fas fa-calendar-check me-2"></i>
                        Visits & Attendance
                    </a>
                </li>

                <!-- Services Section -->
                <li class="nav-item">
                    <span class="nav-section">Services</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('services') }}">
                        <i class="fas fa-stethoscope me-2"></i>
                        Services
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('packages') }}">
                        <i class="fas fa-box me-2"></i>
                        Packages
                    </a>
                </li>

                <!-- Billing Section -->
                <li class="nav-item">
                    <span class="nav-section">Billing</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('billing') }}">
                        <i class="fas fa-file-invoice-dollar me-2"></i>
                        Billing
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('payments') }}">
                        <i class="fas fa-money-bill-wave me-2"></i>
                        Payments
                    </a>
                </li>

                <!-- Appointments Section -->
                <li class="nav-item">
                    <span class="nav-section">Appointments</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('appointments') }}">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Appointments
                    </a>
                </li>

                <!-- Management Section -->
                <li class="nav-item">
                    <span class="nav-section">Management</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('users') }}">
                        <i class="fas fa-user-cog me-2"></i>
                        User Management
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('reports') }}">
                        <i class="fas fa-chart-bar me-2"></i>
                        Reports
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
