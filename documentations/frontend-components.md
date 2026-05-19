# Frontend Components Documentation

## Overview

The Frontend Components system provides reusable UI elements, interactive features, and user interface components for the clinic management system. Built with Bootstrap, jQuery, and custom JavaScript for responsive, user-friendly interfaces.

## Core Technologies

### 1. CSS Framework
- **Bootstrap 5**: Responsive grid system and components
- **Font Awesome**: Icon library
- **Custom CSS**: Clinic-specific styling
- **Responsive Design**: Mobile-first approach

### 2. JavaScript Libraries
- **jQuery 3.2.1**: DOM manipulation and AJAX
- **SweetAlert2**: Beautiful alert dialogs
- **Chart.js**: Data visualization
- **DataTables**: Advanced table functionality

### 3. Blade Templating
- **Laravel Blade**: Server-side templating
- **Component Includes**: Reusable view components
- **Directive Extensions**: Custom Blade directives
- **Asset Management**: Laravel Mix compilation

## Layout Components

### 1. Main Layout (`layouts/app.blade.php`)

**Structure:**
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Clinic Management</title>
    
    <!-- CSS Assets -->
    <link href="{{ asset('assets/css/clinic.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/fontawesome.min.css') }}" rel="stylesheet">
</head>
<body>
    @include('partials.navigation')
    
    <main class="main-content">
        @include('partials.sidebar')
        
        <div class="content-wrapper">
            @yield('content')
        </div>
    </main>
    
    @include('partials.footer')
    
    <!-- JavaScript Assets -->
    <script src="{{ asset('assets/js/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    @yield('scripts')
</body>
</html>
```

**Features:**
- **Responsive Navigation**: Mobile-friendly menu
- **Dynamic Sidebar**: Context-aware navigation
- **Asset Management**: Optimized CSS/JS loading
- **SEO Meta**: Proper meta tags

### 2. Navigation Component (`partials/navigation.blade.php`)

**Features:**
- **User Menu**: Profile, settings, logout
- **Breadcrumb Navigation**: Current location indicator
- **Search Bar**: Global search functionality
- **Notifications**: Real-time notification display

### 3. Sidebar Component (`partials/sidebar.blade.php`)

**Dynamic Menu:**
```php
// Role-based menu items
$menuItems = [
    'admin' => [
        ['title' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'fas fa-tachometer-alt'],
        ['title' => 'Patients', 'route' => 'patients.index', 'icon' => 'fas fa-users'],
        ['title' => 'Services', 'route' => 'services.index', 'icon' => 'fas fa-medkit'],
    ],
    'doctor' => [
        ['title' => 'My Patients', 'route' => 'doctor.patients', 'icon' => 'fas fa-user-md'],
        ['title' => 'Service Results', 'route' => 'service-results.index', 'icon' => 'fas fa-file-medical'],
    ]
];
```

## Form Components

### 1. Patient Registration Form

**Dynamic Fields:**
```html
<div class="form-group">
    <label for="first_name" class="form-label">First Name *</label>
    <input type="text" class="form-control @error('first_name')" 
           id="first_name" name="first_name" 
           value="{{ old('first_name') }}" required>
    @error('first_name')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
```

**Real-time Validation:**
```javascript
// Form validation
$('#patient_form').on('input', function() {
    validateField($(this).find('input:focus'));
});

function validateField(field) {
    const fieldName = field.attr('name');
    const value = field.val();
    
    // Real-time validation rules
    const rules = {
        'email': /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
        'phone': /^[\d\s\-\+\(\)]+$/,
        'first_name': /^[a-zA-Z\s]+$/
    };
    
    if (rules[fieldName] && !rules[fieldName].test(value)) {
        field.addClass('is-invalid');
        showFieldError(fieldName, 'Invalid format');
    } else {
        field.removeClass('is-invalid');
        hideFieldError(fieldName);
    }
}
```

### 2. Service Selection Form

**Dynamic Service Loading:**
```javascript
// Service selection with pricing
function loadServices(category = null) {
    $.ajax({
        url: '/api/services' + (category ? '?category=' + category : ''),
        type: 'GET',
        success: function(response) {
            const serviceSelect = $('#service_id');
            serviceSelect.empty().append('<option value="">Select Service</option>');
            
            response.data.forEach(function(service) {
                serviceSelect.append(`
                    <option value="${service.id}" 
                            data-price="${service.price}" 
                            data-result-type="${service.result_type}">
                        ${service.service_name} - $${service.price}
                    </option>
                `);
            });
        }
    });
}
```

### 3. File Upload Component

**Drag & Drop Upload:**
```html
<div class="file-upload-area" id="file_upload_area">
    <div class="upload-content">
        <i class="fas fa-cloud-upload-alt fa-3x"></i>
        <p>Drag & Drop files here or click to browse</p>
        <input type="file" id="result_file" name="result_file" 
               class="file-input" accept=".pdf,.jpg,.png,.doc">
    </div>
    <div class="upload-progress" id="upload_progress" style="display: none;">
        <div class="progress">
            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
        </div>
        <span class="progress-text">0%</span>
    </div>
</div>
```

**Upload Handler:**
```javascript
// File upload with progress
$('#file_upload_area').on('drop', function(e) {
    e.preventDefault();
    const files = e.originalEvent.dataTransfer.files;
    
    if (files.length > 0) {
        uploadFile(files[0]);
    }
});

function uploadFile(file) {
    const formData = new FormData();
    formData.append('file', file);
    formData.append('service_id', $('#service_id').val());
    
    $.ajax({
        url: '/api/service-results/upload',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        xhr: function() {
            const xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    updateUploadProgress(percent);
                }
            });
            return xhr;
        },
        success: function(response) {
            if (response.success) {
                showUploadSuccess(response.data);
            }
        }
    });
}
```

## Data Display Components

### 1. Patient Cards

**Responsive Card Layout:**
```html
<div class="patient-card" data-patient-id="{{ $patient->id }}">
    <div class="card-header">
        <div class="patient-avatar">
            @if($patient->photo)
                <img src="{{ asset('uploads/patients/' . $patient->photo) }}" 
                     alt="{{ $patient->name }}">
            @else
                <i class="fas fa-user fa-2x"></i>
            @endif
        </div>
        <div class="patient-info">
            <h5 class="patient-name">{{ $patient->first_name }} {{ $patient->last_name }}</h5>
            <p class="patient-id">{{ $patient->patient_id }}</p>
        </div>
        <div class="patient-status">
            <span class="badge badge-{{ $patient->status }}">
                {{ ucfirst($patient->status) }}
            </span>
        </div>
    </div>
    
    <div class="card-body">
        <div class="patient-details">
            <div class="detail-item">
                <i class="fas fa-envelope"></i>
                <span>{{ $patient->email }}</span>
            </div>
            <div class="detail-item">
                <i class="fas fa-phone"></i>
                <span>{{ $patient->phone }}</span>
            </div>
            <div class="detail-item">
                <i class="fas fa-calendar"></i>
                <span>Last Visit: {{ $patient->last_visit_date ?? 'No visits' }}</span>
            </div>
        </div>
        
        <div class="card-actions">
            <a href="{{ route('patients.show', $patient->id) }}" 
               class="btn btn-primary btn-sm">View</a>
            <a href="{{ route('patients.edit', $patient->id) }}" 
               class="btn btn-warning btn-sm">Edit</a>
            <button class="btn btn-danger btn-sm delete-patient" 
                    data-patient-id="{{ $patient->id }}">Delete</button>
        </div>
    </div>
</div>
```

### 2. Service Results Grid

**Dynamic Grid Layout:**
```html
<div class="results-grid">
    @foreach($results as $result)
        <div class="result-card" data-result-type="{{ $result->service ? $result->service->result_type : $result->result_type }}">
            <div class="result-header">
                <div class="result-icon">
                    @if($result->service && $result->service->result_type === 'file')
                        <i class="fas fa-file-medical"></i>
                    @elseif($result->service && $result->service->result_type === 'numeric')
                        <i class="fas fa-chart-line"></i>
                    @else
                        <i class="fas fa-file-alt"></i>
                    @endif
                </div>
                <div class="result-info">
                    <h6>{{ $result->service->service_name ?? 'Unknown Service' }}</h6>
                    <span class="badge badge-info">
                        {{ ucfirst($result->service && $result->service->result_type ? $result->service->result_type : $result->result_type) }}
                    </span>
                </div>
                <div class="result-status">
                    <span class="badge badge-{{ $result->status }}">
                        {{ ucfirst(str_replace('_', ' ', $result->status)) }}
                    </span>
                </div>
            </div>
            
            <div class="result-content">
                @if($result->service && $result->service->result_type === 'text')
                    <p class="text-result">{{ $result->result_text }}</p>
                @elseif($result->service && $result->service->result_type === 'numeric')
                    <div class="numeric-result">
                        <span class="value">{{ $result->result_numeric }}</span>
                        <span class="unit">{{ $result->service->unit ?? '' }}</span>
                    </div>
                @elseif($result->service && $result->service->result_type === 'file')
                    <div class="file-result">
                        <i class="fas fa-file"></i>
                        <span>{{ $result->result_file_name }}</span>
                        <a href="{{ route('service-results.download', $result->id) }}" 
                           class="btn btn-sm btn-primary">Download</a>
                    </div>
                @endif
            </div>
            
            <div class="result-actions">
                <a href="{{ route('service-results.show', $result->id) }}" 
                   class="btn btn-primary btn-sm">View Details</a>
                @if(auth()->user()->can('approve', $result))
                    <button class="btn btn-success btn-sm approve-result" 
                            data-result-id="{{ $result->id }}">Approve</button>
                @endif
            </div>
        </div>
    @endforeach
</div>
```

### 3. Billing Tables

**Data Table with Actions:**
```html
<table class="table table-striped" id="bills_table">
    <thead>
        <tr>
            <th>Bill Number</th>
            <th>Patient</th>
            <th>Total Amount</th>
            <th>Paid Amount</th>
            <th>Balance</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($bills as $bill)
            <tr data-bill-id="{{ $bill->id }}">
                <td>{{ $bill->bill_number }}</td>
                <td>{{ $bill->patient->name }}</td>
                <td>${{ number_format($bill->total_amount, 2) }}</td>
                <td>${{ number_format($bill->paid_amount, 2) }}</td>
                <td>${{ number_format($bill->balance_due, 2) }}</td>
                <td>
                    <span class="badge badge-{{ $bill->status }}">
                        {{ ucfirst($bill->status) }}
                    </span>
                </td>
                <td>
                    <div class="btn-group">
                        <a href="{{ route('bills.show', $bill->id) }}" 
                           class="btn btn-sm btn-primary">View</a>
                        @if($bill->balance_due > 0)
                            <a href="{{ route('bills.pay', $bill->id) }}" 
                               class="btn btn-sm btn-success">Pay</a>
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
```

## Interactive Components

### 1. Patient Timeline

**Timeline Component:**
```html
<div class="timeline">
    @foreach($timelineEvents as $event)
        <div class="timeline-item timeline-{{ $event->type }}">
            <div class="timeline-marker">
                <i class="fas fa-{{ $event->icon }}"></i>
            </div>
            <div class="timeline-content">
                <div class="timeline-date">{{ $event->date->format('M d, Y') }}</div>
                <div class="timeline-title">{{ $event->title }}</div>
                <div class="timeline-description">{{ $event->description }}</div>
                @if($event->actions)
                    <div class="timeline-actions">
                        @foreach($event->actions as $action)
                            <a href="{{ $action['url'] }}" 
                               class="btn btn-sm btn-{{ $action['color'] }}">
                                {{ $action['label'] }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>
```

### 2. Service Result Type Switcher

**Dynamic Form Switching:**
```javascript
// Result type switching
function switchResultType(resultType) {
    // Hide all result sections
    $('.result-section').hide();
    
    // Show selected section
    $('#' + resultType + '_result_section').show();
    
    // Update form validation
    updateValidationRules(resultType);
}

function updateValidationRules(resultType) {
    const textRules = {
        'result_text': 'required|string|min:10'
    };
    
    const numericRules = {
        'result_numeric': 'required|numeric|min:0'
    };
    
    const fileRules = {
        'result_file': 'required|file|max:5120|mimes:pdf,jpg,png'
    };
    
    // Apply appropriate validation rules
    const rules = resultType === 'text' ? textRules :
                  resultType === 'numeric' ? numericRules : fileRules;
    
    updateFormValidation(rules);
}
```

### 3. Advanced Search Component

**Multi-Field Search:**
```html
<div class="search-container">
    <div class="search-filters">
        <div class="filter-group">
            <label>Search Type</label>
            <select class="form-control" id="search_type">
                <option value="all">All Fields</option>
                <option value="name">Patient Name</option>
                <option value="email">Email</option>
                <option value="phone">Phone</option>
                <option value="patient_id">Patient ID</option>
            </select>
        </div>
        
        <div class="filter-group">
            <label>Date Range</label>
            <div class="date-range">
                <input type="date" class="form-control" id="date_from" placeholder="From">
                <span>to</span>
                <input type="date" class="form-control" id="date_to" placeholder="To">
            </div>
        </div>
        
        <div class="filter-group">
            <label>Status</label>
            <select class="form-control" id="status_filter" multiple>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="archived">Archived</option>
            </select>
        </div>
    </div>
    
    <div class="search-input">
        <div class="input-group">
            <input type="text" class="form-control" id="search_query" 
                   placeholder="Search patients...">
            <button class="btn btn-primary" id="search_button">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>
</div>
```

**Search Handler:**
```javascript
// Advanced search functionality
function performSearch() {
    const searchType = $('#search_type').val();
    const searchQuery = $('#search_query').val();
    const dateFrom = $('#date_from').val();
    const dateTo = $('#date_to').val();
    const status = $('#status_filter').val();
    
    const searchParams = {
        q: searchQuery,
        type: searchType,
        date_from: dateFrom,
        date_to: dateTo,
        status: status
    };
    
    // Remove empty parameters
    Object.keys(searchParams).forEach(key => {
        if (!searchParams[key]) {
            delete searchParams[key];
        }
    });
    
    $.ajax({
        url: '/api/patients/search',
        type: 'GET',
        data: searchParams,
        success: function(response) {
            updateSearchResults(response.data);
        }
    });
}
```

## Responsive Design

### 1. Mobile Navigation

**Mobile Menu:**
```javascript
// Mobile menu toggle
function toggleMobileMenu() {
    const sidebar = $('.sidebar');
    const content = $('.content-wrapper');
    
    sidebar.toggleClass('mobile-open');
    content.toggleClass('sidebar-collapsed');
    
    // Update menu icon
    $('.mobile-menu-toggle i').toggleClass('fa-bars fa-times');
}
```

### 2. Responsive Tables

**Mobile Table Optimization:**
```css
/* Responsive table styles */
@media (max-width: 768px) {
    .table-responsive {
        border: none;
    }
    
    .table thead {
        display: none;
    }
    
    .table, .table tbody, .table tr, .table td {
        display: block;
        width: 100%;
    }
    
    .table tr {
        border: 1px solid #ccc;
        margin-bottom: 10px;
    }
    
    .table td {
        border: none;
        border-bottom: 1px solid #eee;
        position: relative;
        padding-left: 50%;
    }
    
    .table td:before {
        position: absolute;
        top: 6px;
        left: 6px;
        width: 45%;
        padding-right: 10px;
        white-space: nowrap;
        font-weight: bold;
    }
}
```

### 3. Touch-Friendly Components

**Touch-Optimized Buttons:**
```css
/* Touch-friendly button styles */
.btn {
    min-height: 44px;
    min-width: 44px;
    padding: 12px 16px;
    font-size: 16px;
    touch-action: manipulation;
}

.btn-group {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

@media (max-width: 768px) {
    .btn-group {
        flex-direction: column;
    }
}
```

## Performance Optimization

### 1. Lazy Loading

**Image Lazy Loading:**
```html
<img class="lazy-load" data-src="{{ $image->url }}" 
     alt="{{ $image->alt }}" loading="lazy">
```

```javascript
// Lazy loading implementation
const lazyLoadObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const img = entry.target;
            img.src = img.dataset.src;
            img.classList.remove('lazy-load');
            lazyLoadObserver.unobserve(img);
        }
    });
});

document.querySelectorAll('.lazy-load').forEach(img => {
    lazyLoadObserver.observe(img);
});
```

### 2. Infinite Scroll

**Pagination Alternative:**
```javascript
// Infinite scroll implementation
let page = 1;
let loading = false;

function loadMoreContent() {
    if (loading) return;
    
    loading = true;
    $('#loading_indicator').show();
    
    $.ajax({
        url: '/api/patients?page=' + page,
        type: 'GET',
        success: function(response) {
            if (response.data.length > 0) {
                appendPatientCards(response.data);
                page++;
            } else {
                $('#no_more_content').show();
            }
            loading = false;
            $('#loading_indicator').hide();
        }
    });
}

$(window).on('scroll', function() {
    if ($(window).scrollTop() + $(window).height() >= $(document).height() - 200) {
        loadMoreContent();
    }
});
```

### 3. Asset Optimization

**CSS/JS Minification:**
```javascript
// webpack.mix.js
mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        require('tailwindcss'),
    ])
    .options({
        processCssUrls: true,
        purifyCss: true,
    });
```

## Accessibility Features

### 1. Keyboard Navigation

**Tab Navigation:**
```javascript
// Keyboard navigation support
$(document).on('keydown', function(e) {
    if (e.key === 'Tab') {
        // Ensure focus is visible
        const focusedElement = document.activeElement;
        focusedElement.scrollIntoView({ behavior: 'smooth' });
    }
    
    if (e.key === 'Escape') {
        // Close modals and dropdowns
        $('.modal.show').modal('hide');
        $('.dropdown.show').dropdown('hide');
    }
});
```

### 2. Screen Reader Support

**ARIA Labels:**
```html
<button class="btn btn-primary" 
        aria-label="Edit patient {{ $patient->name }}"
        aria-describedby="patient-actions-{{ $patient->id }}">
    <i class="fas fa-edit"></i>
</button>

<div id="patient-actions-{{ $patient->id }}" class="sr-only">
    Actions available: Edit, View Details, Delete
</div>
```

### 3. High Contrast Mode

**Theme Switching:**
```javascript
// High contrast theme
function toggleHighContrast() {
    $('body').toggleClass('high-contrast');
    
    // Save preference
    localStorage.setItem('highContrast', $('body').hasClass('high-contrast'));
}

// Load saved preference
if (localStorage.getItem('highContrast') === 'true') {
    $('body').addClass('high-contrast');
}
```

---

**Related Documentation:**
- [Patients System](./patients-system.md)
- [Service Results](./service-results.md)
- [API Documentation](./api-documentation.md)
