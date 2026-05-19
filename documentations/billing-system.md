# Billing System Documentation

## Overview

The Billing System handles all financial operations including automatic bill generation, payment tracking, invoice management, and financial reporting. It integrates seamlessly with patient visits and service results.

## Core Components

### 1. Bill Model (`App\Models\Bill`)

**Key Relationships:**
- `patient()` - Belongs to Patient
- `visit()` - BelongsTo PatientVisit
- `items()` - Has Many BillItems
- `payments()` - Has Many Payments

**Key Fields:**
- `bill_number` - Unique bill identifier (BILL-XXXXX)
- `bill_type` - 'service', 'package', 'adjustment'
- `total_amount` - Total bill amount
- `paid_amount` - Amount paid so far
- `balance_due` - Remaining balance
- `status` - 'draft', 'sent', 'partial', 'paid', 'overdue', 'cancelled'
- `due_date` - Payment due date
- `generated_date` - Bill creation date

### 2. BillItem Model (`App\Models\BillItem`)

**Key Relationships:**
- `bill()` - BelongsTo Bill
- `service()` - BelongsTo Service
- `package()` - BelongsTo Package

**Key Fields:**
- `description` - Item description
- `quantity` - Item quantity
- `unit_price` - Price per unit
- `total_price` - Total item price
- `item_type` - 'service', 'package', 'adjustment'

### 3. Payment Model (`App\Models\Payment`)

**Key Relationships:**
- `bill()` - BelongsTo Bill
- `patient()` - BelongsTo Patient

**Key Fields:**
- `payment_method` - 'cash', 'card', 'bank_transfer', 'insurance'
- `amount` - Payment amount
- `payment_date` - Payment date
- `transaction_id` - External transaction reference
- `status` - 'pending', 'completed', 'failed', 'refunded'

### 4. BillingService (`App\Services\BillingService`)

**Core Methods:**

#### `createOrUpdateBillForVisit(PatientVisit $visit)`
- **Purpose**: Main billing trigger for visits
- **Logic**: Creates new bill or updates existing
- **Integration**: Service and package processing
- **Features**: Duplicate prevention, accurate totals

#### `createNewBill(PatientVisit $visit)`
- **Purpose**: Create new bill for visit
- **Process**: Service/package analysis, item creation
- **Returns**: Created Bill instance

#### `updateExistingBill(Bill $bill, PatientVisit $visit)`
- **Purpose**: Update existing bill
- **Process**: Item recalculation, balance updates
- **Features**: Change tracking, audit trail

#### `createBillItems(Bill $bill, PatientVisit $visit)`
- **Purpose**: Generate bill line items
- **Logic**: Prioritizes JSON data over relationships
- **Prevention**: Duplicate item prevention

## Billing Workflow

### 1. Automatic Bill Generation
```
Patient visit completed
→ BillingService triggered
→ Analyze services and packages
→ Create/update bill
→ Generate bill items
→ Calculate totals
→ Send notifications
```

### 2. Service Processing Logic
```php
// Priority: JSON data > Relationship table
if ($visit->selected_services) {
    $servicesFromJson = json_decode($visit->selected_services, true);
    // Process from JSON (primary source)
} elseif ($visit->services()->exists()) {
    // Process from relationship (fallback)
}
```

### 3. Package vs Individual Services
- **Package Billing**: Single item with package details
- **Service Billing**: Individual service items
- **Mixed Billing**: Package + additional services

## Bill Generation Process

### 1. Visit Analysis
- **Service Detection**: Identify all services provided
- **Package Detection**: Check for package usage
- **Pricing**: Apply current or historical pricing
- **Discounts**: Apply applicable discounts

### 2. Item Creation
- **Service Items**: Individual service line items
- **Package Items**: Package bundle items
- **Adjustment Items**: Discounts, additional charges

### 3. Total Calculation
```php
$totalAmount = 0;

// Package pricing
if ($visit->package_id && $visit->package) {
    $totalAmount = $visit->package->total_cost;
} 
// Individual service pricing
else {
    // Calculate from JSON or relationship
    $totalAmount += $serviceData['price'] ?? 0;
}

// Update bill totals
$bill->total_amount = $totalAmount;
$bill->balance_due = $totalAmount - $bill->paid_amount;
```

### 4. Status Management
- **Draft**: Initial bill creation
- **Sent**: Bill sent to patient
- **Partial**: Partial payment received
- **Paid**: Full payment received
- **Overdue**: Payment past due date
- **Cancelled**: Bill voided

## Payment Processing

### 1. Payment Methods
- **Cash**: Direct cash payment
- **Card**: Credit/debit card processing
- **Bank Transfer**: Electronic funds transfer
- **Insurance**: Insurance claim processing

### 2. Payment Workflow
```
User selects bill
→ Chooses payment method
→ Enters payment details
→ System processes payment
→ Updates bill status
→ Generates receipt
```

### 3. Payment Validation
- **Amount Validation**: Sufficient payment amount
- **Method Validation**: Supported payment methods
- **Duplicate Prevention**: Avoid duplicate payments
- **Security**: Fraud detection checks

## Frontend Implementation

### 1. Bill Management Views

#### Bill Index (`bills/index.blade.php`)
- **Features**: Search, filtering, status badges
- **Data Display**: Bill number, patient, amount, status
- **Actions**: View, edit, print, send, delete

#### Bill Detail (`bills/show.blade.php`)
- **Sections**: Bill overview, item details, payment history
- **Actions**: Make payment, print bill, send invoice
- **Status**: Visual status indicators, due dates

#### Payment Form (`bills/pay.blade.php`)
- **Payment Methods**: Method selection interface
- **Validation**: Real-time payment validation
- **Processing**: Payment processing indicators
- **Receipt**: Payment confirmation and receipt

### 2. JavaScript Functionality

#### Bill Search & Filtering
```javascript
// Real-time bill search
document.getElementById('bill_search').addEventListener('input', function() {
    const query = this.value.toLowerCase();
    const bills = document.querySelectorAll('.bill-item');
    
    bills.forEach(bill => {
        const text = bill.textContent.toLowerCase();
        bill.style.display = text.includes(query) ? 'block' : 'none';
    });
});
```

#### Payment Processing
```javascript
// Payment form submission
$('#payment_form').on('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    $.ajax({
        url: '/bills/process-payment',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                window.location.href = '/bills/' + response.bill_id;
            }
        }
    });
});
```

## Integration Points

### 1. Patient Visits
- **Automatic Trigger**: Visit completion triggers billing
- **Service Linking**: Services linked to bill items
- **Package Support**: Package billing integration

### 2. Service Results
- **Completion Detection**: Service results trigger billing
- **Service Pricing**: Dynamic service pricing
- **Result-Based Billing**: Bill based on completed services

### 3. Patient Management
- **Patient Billing**: All patient bills in one place
- **Payment History**: Complete payment tracking
- **Balance Tracking**: Outstanding balance management

## API Endpoints

### Bill Management
```php
GET    /api/bills                          // List bills
POST   /api/bills                          // Create bill
GET    /api/bills/{id}                     // Get bill details
PUT    /api/bills/{id}                     // Update bill
DELETE /api/bills/{id}                     // Delete bill
```

### Payment Processing
```php
POST   /api/bills/{id}/pay                 // Process payment
GET    /api/bills/{id}/payments             // Get payment history
POST   /api/bills/{id}/refund              // Process refund
```

### Billing Operations
```php
POST   /api/bills/{id}/send               // Send bill to patient
POST   /api/bills/{id}/mark-paid          // Mark as paid
GET    /api/bills/{id}/receipt             // Generate receipt
```

### Search & Filtering
```php
GET    /api/bills/search?q={query}          // Search bills
GET    /api/bills/filter?{filters}          // Filter bills
GET    /api/patients/{id}/bills            // Patient bills
```

## Reporting & Analytics

### 1. Financial Reports
- **Revenue Reports**: Total revenue by period
- **Payment Reports**: Payment method breakdown
- **Outstanding Reports**: Unpaid bills analysis
- **Refund Reports**: Refund tracking and analysis

### 2. Billing Analytics
- **Service Revenue**: Revenue by service type
- **Package Performance**: Package sales analysis
- **Patient Billing**: Patient payment patterns
- **Aging Reports**: Outstanding bill aging

### 3. Dashboard Metrics
- **Daily Revenue**: Today's total revenue
- **Outstanding Balance**: Total unpaid amount
- **Payment Trends**: Payment pattern analysis
- **Bill Status**: Bill status distribution

## Performance Optimization

### 1. Database Optimization
- **Indexes**: Patient, date, status indexes
- **Query Optimization**: Efficient billing queries
- **Caching**: Frequent bill data caching

### 2. Calculation Optimization
- **Batch Processing**: Bulk bill processing
- **Caching**: Price calculation caching
- **Optimized Queries**: Minimize database hits

### 3. Frontend Optimization
- **Pagination**: Large bill dataset handling
- **Lazy Loading**: On-demand data loading
- **Search Optimization**: Fast bill search

## Security & Compliance

### 1. Financial Security
- **Encryption**: Sensitive financial data encryption
- **Access Control**: Role-based billing permissions
- **Audit Trail**: Complete billing audit log
- **PCI Compliance**: Payment card security standards

### 2. Data Integrity
- **Validation**: Input validation and sanitization
- **Duplicate Prevention**: Duplicate bill/payment prevention
- **Consistency Checks**: Data consistency validation
- **Backup**: Regular financial data backups

## Error Handling

### 1. Billing Errors
- **Calculation Errors**: Total calculation issues
- **Duplicate Prevention**: Duplicate bill detection
- **Integration Errors**: Service/package integration issues

### 2. Payment Errors
- **Processing Errors**: Payment gateway issues
- **Validation Errors**: Payment validation failures
- **Network Errors**: Connection and timeout issues

### 3. User Experience
- **Error Messages**: Clear, actionable feedback
- **Recovery Options**: Payment retry mechanisms
- **Help Documentation**: Contextual assistance

## Testing & Quality Assurance

### 1. Unit Tests
- **Model Tests**: Bill, BillItem, Payment models
- **Service Tests**: BillingService functionality
- **Controller Tests**: API endpoint testing

### 2. Integration Tests
- **Visit Integration**: Complete visit-to-bill workflow
- **Payment Processing**: End-to-end payment testing
- **Reporting**: Report generation accuracy

### 3. Financial Testing
- **Calculation Accuracy**: Total calculation verification
- **Edge Cases**: Complex billing scenarios
- **Performance**: Large dataset handling

## Configuration

### 1. Billing Settings
- **Default Due Days**: Standard payment terms
- **Late Fees**: Automatic late fee calculation
- **Discount Rules**: Automatic discount application
- **Currency Settings**: Multi-currency support

### 2. Payment Gateway
- **Gateway Configuration**: Payment provider setup
- **Webhook Handling**: Payment notification processing
- **Security Settings**: API key management
- **Testing**: Sandbox testing environment

---

**Related Documentation:**
- [Patients System](./patients-system.md)
- [Service Results](./service-results.md)
- [API Documentation](./api-documentation.md)
