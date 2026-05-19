# Services & Packages Documentation

## Overview

The Services & Packages system manages the clinic's service catalog, pricing, and bundled service packages. It defines what services are available, their costs, and how they're organized into packages for billing and patient visits.

## Core Components

### 1. Service Model (`App\Models\Service`)

**Key Relationships:**
- `patientServices()` - Has Many PatientService records
- `serviceResults()` - Has Many ServiceResult records
- `billItems()` - Has Many BillItem records

**Key Fields:**
- `service_name` - Human-readable service name
- `description` - Detailed service description
- `price` - Current service price
- `category` - Service category (lab, imaging, consultation, etc.)
- `result_type` - Expected result type ('text', 'numeric', 'file')
- `active` - Service availability status
- `requires_appointment` - Whether service needs appointment
- `duration_minutes` - Expected service duration

### 2. Package Model (`App\Models\Package`)

**Key Relationships:**
- `services()` - Belongs to Many Services
- `patientVisits()` - Has Many PatientVisit records
- `bills()` - Has Many Bill records

**Key Fields:**
- `package_name` - Human-readable package name
- `description` - Detailed package description
- `total_cost` - Total package price
- `active` - Package availability status
- `duration_minutes` - Expected package duration
- `popular` - Mark as popular package

### 3. PackageService Model (`App\Models\PackageService`)

**Pivot Table Fields:**
- `package_id` - Foreign key to Package
- `service_id` - Foreign key to Service
- `included_price` - Price of service within package
- `optional` - Whether service is optional in package

## Service Management

### 1. Service Categories

#### Laboratory Services
- **Blood Tests**: CBC, cholesterol, diabetes screening
- **Urine Tests**: Urinalysis, drug testing
- **Pathology**: Biopsy analysis, tissue testing

#### Imaging Services
- **X-Ray**: Digital radiography
- **Ultrasound**: Diagnostic imaging
- **MRI**: Advanced imaging (if available)

#### Consultation Services
- **General Consultation**: Primary care visits
- **Specialist Consultation**: Medical specialist visits
- **Follow-up**: Post-treatment consultations

#### Preventive Services
- **Vaccinations**: Immunization services
- **Health Screening**: Preventive health checks
- **Wellness Programs**: Health and wellness services

### 2. Service Configuration

#### Result Types
```php
// Service Result Types
const RESULT_TYPES = [
    'text' => 'Text Results (Narrative findings)',
    'numeric' => 'Numeric Results (Measurements, values)',
    'file' => 'File Results (Documents, images, PDFs)'
];
```

#### Service Status
- **Active**: Available for booking and billing
- **Inactive**: Temporarily unavailable
- **Discontinued**: No longer offered

### 3. Service Pricing

#### Dynamic Pricing
```php
// Service pricing logic
public function getCurrentPrice()
{
    // Check for promotional pricing
    if ($this->hasActivePromotion()) {
        return $this->promotional_price;
    }
    
    // Check for patient-specific pricing
    if ($patient = request()->route('patient')) {
        return $this->getPatientPrice($patient);
    }
    
    return $this->price;
}
```

#### Price History
- **Base Price**: Standard service price
- **Promotional Price**: Temporary discount pricing
- **Patient Price**: Patient-specific pricing
- **Insurance Price**: Insurance negotiated rates

## Package Management

### 1. Package Types

#### Health Screening Packages
- **Basic Checkup**: Essential health screening
- **Comprehensive Checkup**: Complete health assessment
- **Executive Checkup**: Premium health screening

#### Specialized Packages
- **Women's Health**: Female-specific screening
- **Men's Health**: Male-specific screening
- **Pediatric**: Child health packages
- **Senior**: Elderly health packages

#### Diagnostic Packages
- **Cardiac Screening**: Heart health assessment
- **Diabetes Screening**: Diabetes risk assessment
- **Cancer Screening**: Cancer detection packages

### 2. Package Structure

#### Service Inclusion
```php
// Package service relationships
public function services()
{
    return $this->belongsToMany(Service::class, 'package_services')
        ->withPivot(['included_price', 'optional'])
        ->withTimestamps();
}
```

#### Package Pricing Logic
```php
// Package total calculation
public function calculateTotalCost()
{
    $total = 0;
    
    foreach ($this->services as $service) {
        $total += $service->pivot->included_price;
    }
    
    // Apply package discount
    $discount = $this->getPackageDiscount();
    $total = $total * (1 - $discount);
    
    return $total;
}
```

### 3. Package Benefits

#### Cost Savings
- **Bundle Discount**: Reduced cost vs individual services
- **Promotional Pricing**: Special package pricing
- **Insurance Coverage**: Better insurance reimbursement

#### Convenience
- **Single Visit**: Multiple services in one visit
- **Coordinated Care**: Integrated service delivery
- **Streamlined Billing**: Single invoice for package

## Frontend Implementation

### 1. Service Management Views

#### Service Index (`services/index.blade.php`)
- **Features**: Search, filtering, category navigation
- **Data Display**: Service cards with pricing, availability
- **Actions**: View, edit, activate/deactivate, pricing history

#### Service Form (`services/create.blade.php`, `services/edit.blade.php`)
- **Fields**: Service details, pricing, result type configuration
- **Validation**: Real-time price validation, category selection
- **Features**: Image upload, description editor, scheduling options

#### Service Detail (`services/show.blade.php`)
- **Sections**: Service overview, pricing history, usage statistics
- **Actions**: Edit service, view results, manage pricing
- **Analytics**: Service utilization, revenue tracking

### 2. Package Management Views

#### Package Index (`packages/index.blade.php`)
- **Features**: Popular packages, category filtering, pricing display
- **Data Display**: Package cards with included services
- **Actions**: View, edit, activate/deactivate, pricing management

#### Package Builder (`packages/create.blade.php`, `packages/edit.blade.php`)
- **Service Selection**: Multi-select service inclusion
- **Pricing Calculator**: Real-time package pricing
- **Configuration**: Optional services, package duration

#### Package Detail (`packages/show.blade.php`)
- **Sections**: Package overview, included services, pricing details
- **Analytics**: Package utilization, revenue tracking
- **Actions**: Edit package, view bookings, manage pricing

### 3. JavaScript Functionality

#### Service Selection
```javascript
// Service selection for packages
function updatePackageServices() {
    const selectedServices = [];
    let totalPrice = 0;
    
    $('.service-checkbox:checked').each(function() {
        const serviceId = $(this).val();
        const servicePrice = parseFloat($(this).data('price'));
        
        selectedServices.push(serviceId);
        totalPrice += servicePrice;
    });
    
    // Update package total
    $('#package_total').text(totalPrice.toFixed(2));
    
    // Apply package discount
    const discount = parseFloat($('#package_discount').val());
    const finalPrice = totalPrice * (1 - discount);
    $('#final_price').text(finalPrice.toFixed(2));
}
```

#### Price Calculator
```javascript
// Real-time price calculation
function calculatePackagePrice() {
    const basePrice = parseFloat($('#base_price').val());
    const discount = parseFloat($('#discount').val());
    const finalPrice = basePrice * (1 - discount);
    
    $('#calculated_price').val(finalPrice.toFixed(2));
    $('#display_price').text(formatCurrency(finalPrice));
}
```

## Integration Points

### 1. Patient Visits
- **Service Selection**: Choose individual services or packages
- **Scheduling**: Service duration affects appointment scheduling
- **Billing**: Service/package pricing drives bill generation

### 2. Service Results
- **Result Type Configuration**: Service defines expected result type
- **Auto-Detection**: Service result type auto-detection in forms
- **Quality Control**: Service-specific result validation

### 3. Billing System
- **Price Calculation**: Service/package pricing for billing
- **Item Generation**: Service/package items in bills
- **Revenue Tracking**: Service/package revenue analysis

## API Endpoints

### Service Management
```php
GET    /api/services                          // List services
POST   /api/services                          // Create service
GET    /api/services/{id}                     // Get service details
PUT    /api/services/{id}                     // Update service
DELETE /api/services/{id}                     // Delete service
```

### Package Management
```php
GET    /api/packages                          // List packages
POST   /api/packages                          // Create package
GET    /api/packages/{id}                     // Get package details
PUT    /api/packages/{id}                     // Update package
DELETE /api/packages/{id}                     // Delete package
```

### Service Operations
```php
GET    /api/services/categories               // Get service categories
GET    /api/services/search?q={query}         // Search services
POST   /api/services/{id}/price               // Update service price
GET    /api/services/{id}/history             // Price history
```

### Package Operations
```php
GET    /api/packages/{id}/services            // Package services
POST   /api/packages/{id}/services            // Add service to package
DELETE /api/packages/{id}/services/{service_id} // Remove service
GET    /api/packages/popular                  // Popular packages
```

## Pricing Management

### 1. Price Updates
- **Bulk Updates**: Update multiple service prices
- **Scheduled Updates**: Future price changes
- **Promotional Pricing**: Temporary price reductions
- **Seasonal Pricing**: Seasonal price adjustments

### 2. Price History
```php
// Price history tracking
public function recordPriceChange($oldPrice, $newPrice, $reason)
{
    PriceHistory::create([
        'service_id' => $this->id,
        'old_price' => $oldPrice,
        'new_price' => $newPrice,
        'change_reason' => $reason,
        'changed_by' => auth()->id(),
        'effective_date' => now()
    ]);
}
```

### 3. Revenue Analytics
- **Service Revenue**: Revenue by service type
- **Package Revenue**: Package sales analysis
- **Trend Analysis**: Pricing trend analysis
- **Profit Margins**: Service profitability analysis

## Performance Optimization

### 1. Database Optimization
- **Indexes**: Service name, category, price indexes
- **Query Optimization**: Efficient service/package queries
- **Caching**: Frequent service data caching

### 2. Frontend Optimization
- **Lazy Loading**: Large service catalog loading
- **Search Optimization**: Fast service search
- **Image Optimization**: Service image optimization

### 3. API Optimization
- **Pagination**: Large service dataset handling
- **Caching**: API response caching
- **Compression**: Response data compression

## Quality Control

### 1. Service Validation
- **Required Fields**: Service name, price, category
- **Price Validation**: Positive price values
- **Category Validation**: Valid category selection
- **Result Type Validation**: Valid result type selection

### 2. Package Validation
- **Service Inclusion**: At least one service required
- **Price Validation**: Package price合理性
- **Service Compatibility**: Compatible service combinations
- **Duration Validation**: Realistic package duration

### 3. Data Integrity
- **Referential Integrity**: Service/package relationships
- **Consistency Checks**: Price consistency validation
- **Duplicate Prevention**: Duplicate service/package prevention

## Reporting & Analytics

### 1. Service Reports
- **Utilization Report**: Service usage statistics
- **Revenue Report**: Service revenue analysis
- **Trend Report**: Service popularity trends
- **Performance Report**: Service performance metrics

### 2. Package Reports
- **Sales Report**: Package sales analysis
- **Popularity Report**: Most popular packages
- **Revenue Report**: Package revenue breakdown
- **Utilization Report**: Package usage statistics

### 3. Pricing Analytics
- **Price History**: Historical price changes
- **Competitive Analysis**: Market price comparison
- **Profit Analysis**: Service profitability
- **Optimization**: Pricing optimization recommendations

## Configuration & Settings

### 1. Service Settings
- **Default Duration**: Standard service duration
- **Result Types**: Available result type options
- **Categories**: Service category configuration
- **Pricing Rules**: Default pricing policies

### 2. Package Settings
- **Default Discount**: Standard package discount
- **Minimum Services**: Minimum services per package
- **Maximum Duration**: Maximum package duration
- **Popular Threshold**: Popular package criteria

---

**Related Documentation:**
- [Patients System](./patients-system.md)
- [Service Results](./service-results.md)
- [Billing System](./billing-system.md)
