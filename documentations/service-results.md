# Service Results System Documentation

## Overview

The Service Results System manages medical test results, findings, and reports for patient services. It supports multiple result types (text, numeric, file) and includes approval workflows.

## Core Components

### 1. ServiceResult Model (`App\Models\ServiceResult`)

**Key Relationships:**
- `patient()` - Belongs to Patient
- `visit()` - Belongs to PatientVisit
- `service()` - Belongs to Service
- `package()` - BelongsTo Package
- `patientService()` - BelongsTo PatientService

**Key Fields:**
- `result_type` - 'text', 'numeric', 'file'
- `result_text` - Text result content
- `result_numeric` - Numeric measurement
- `result_file_path` - File storage path
- `result_file_name` - Original filename
- `status` - 'draft', 'pending_approval', 'approved', 'rejected'
- `recorded_by` - User who recorded result
- `approved_by` - User who approved result

### 2. ServiceResult Controller (`App\Http\Controllers\ServiceResultController`)

**Main Methods:**

#### `index()`
- **Purpose**: List service results for a patient
- **Route**: `GET /patients/{patient}/service-results`
- **Features**: Filtering, sorting, status badges
- **View**: `service-results.patient-index`

#### `create()`
- **Purpose**: Show result entry form
- **Route**: `GET /patients/{patient}/service-results/create`
- **Features**: Auto-detect result type from service
- **View**: `service-results.create`

#### `store()`
- **Purpose**: Save new service result
- **Route**: `POST /patients/{patient}/service-results`
- **Features**: File upload, validation, auto-billing
- **Response**: JSON success/error response

#### `show()`
- **Purpose**: Display result details
- **Route**: `GET /patients/{patient}/service-results/{result}`
- **Features**: Result display, approval status, file preview
- **View**: `service-results.show`

#### `edit()`, `update()`
- **Purpose**: Modify existing result
- **Routes**: `GET /patients/{patient}/service-results/{result}/edit`, `PUT /patients/{patient}/service-results/{result}`
- **Features**: Result type preservation, file replacement
- **View**: `service-results.edit`

#### `saveResult()`
- **Purpose**: AJAX result saving
- **Route**: `POST /service-results/save`
- **Features**: Real-time saving, validation
- **Response**: JSON response with status

#### `approveResult()`, `rejectResult()`
- **Purpose**: Result approval workflow
- **Routes**: `POST /service-results/{result}/approve`, `POST /service-results/{result}/reject`
- **Features**: Status updates, notifications
- **Response**: JSON response

#### `destroy()`
- **Purpose**: Delete service result
- **Route**: `DELETE /patients/{patient}/service-results/{result}`
- **Features**: File cleanup, audit trail

### 3. Result Type System

#### Text Results
- **Purpose**: Narrative findings, descriptions
- **Storage**: `result_text` field
- **Validation**: Required text content
- **Display**: Formatted text with markdown support

#### Numeric Results
- **Purpose**: Measurements, lab values
- **Storage**: `result_numeric` field
- **Validation**: Numeric value, optional units
- **Display**: Formatted with units, reference ranges

#### File Results
- **Purpose**: Documents, images, PDFs
- **Storage**: File system with database reference
- **Validation**: File type, size limits (5MB max)
- **Display**: Preview, download, file info

## Service Result Workflow

### 1. Result Entry
```
User selects patient and service
→ System auto-detects result type
→ Shows appropriate input interface
→ User enters result data
→ System validates and saves
```

### 2. Auto-Detection Logic
```javascript
// Service result type detection
if (service && service.result_type) {
    showResultSection(service.result_type);
    // Hide manual result_type selection
}
```

### 3. Result Validation
- **Text**: Minimum length, content validation
- **Numeric**: Value range, format validation
- **File**: Type whitelist, size limits, virus scanning

### 4. Status Management
- **Draft**: Initial state, editable
- **Pending Approval**: Submitted for review
- **Approved**: Final, visible to patient
- **Rejected**: Needs correction

## Frontend Implementation

### 1. Result Entry Forms

#### Create Form (`service-results/create.blade.php`)
- **Dynamic Interface**: Changes based on result type
- **Auto-Detection**: Service-based result type detection
- **File Upload**: Drag-and-drop, progress indicators
- **Validation**: Real-time form validation

#### Edit Form (`service-results/edit.blade.php`)
- **Preserved Type**: Maintains original result type
- **File Replacement**: Option to replace existing files
- **History**: Shows modification history
- **Approval Status**: Current approval state

### 2. Result Display

#### Patient Index (`service-results/patient-index.blade.php`)
- **Grid Layout**: Card-based result display
- **Filtering**: By status, date, service type
- **Quick Actions**: View, edit, approve/reject
- **Status Badges**: Visual status indicators

#### Result Detail (`service-results/show.blade.php`)
- **Result Display**: Type-appropriate presentation
- **Metadata**: Recording date, staff, approval info
- **File Preview**: In-document file viewing
- **Timeline**: Result modification history

#### Patient Timeline (`service-results/patient_timeline.blade.php`)
- **Chronological View**: Time-ordered results
- **Integration**: Combined with visits and appointments
- **Visual Indicators**: Result type icons, status colors
- **Interactive**: Click for details

### 3. JavaScript Functionality

#### Result Type Management
```javascript
function showResultSection(resultType) {
    // Hide all sections
    document.querySelectorAll('.result-section').forEach(section => {
        section.style.display = 'none';
    });
    
    // Show selected section
    const targetSection = document.getElementById(resultType + '_result_section');
    if (targetSection) {
        targetSection.style.display = 'block';
    }
}
```

#### Auto-Detection
```javascript
// Service selection auto-detects result type
document.getElementById('service_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const resultType = selectedOption.getAttribute('data-result-type');
    
    if (resultType) {
        showResultSection(resultType);
    }
});
```

#### File Upload
```javascript
// File preview and validation
document.getElementById('result_file').addEventListener('change', function() {
    const file = this.files[0];
    const maxSize = 5 * 1024 * 1024; // 5MB
    
    if (file.size > maxSize) {
        alert('File size exceeds 5MB limit');
        this.value = '';
        return;
    }
    
    // Show preview
    showFilePreview(file);
});
```

## File Management

### 1. Upload Process
- **Validation**: File type, size, security scan
- **Storage**: Organized by patient and date
- **Naming**: Unique filename generation
- **Database**: Store path and metadata

### 2. File Storage Structure
```
uploads/
├── service-results/
│   ├── {patient_id}/
│   │   ├── {year}/
│   │   │   ├── {month}/
│   │   │   │   ├── {unique_filename}.pdf
│   │   │   │   ├── {unique_filename}.jpg
│   │   │   │   └── ...
```

### 3. File Security
- **Access Control**: Role-based file access
- **Encryption**: Sensitive file encryption
- **Backup**: Automated file backups
- **Cleanup**: Orphaned file removal

## Approval Workflow

### 1. Submission Process
```
User completes result entry
→ System validates result
→ Status changes to 'pending_approval'
→ Notification sent to approver
→ Approver reviews and decides
```

### 2. Approval Actions
- **Approve**: Mark as approved, notify patient
- **Reject**: Return to draft with comments
- **Request Changes**: Specific modification requests

### 3. Notification System
- **Email Alerts**: Approval status changes
- **Dashboard Notifications**: Real-time updates
- **Patient Notifications**: Result availability

## Integration Points

### 1. Patient Visits
- **Automatic Creation**: Visit generates result records
- **Service Linking**: Results linked to visit services
- **Completion Tracking**: Visit completion based on results

### 2. Billing System
- **Service Billing**: Results trigger service billing
- **Package Billing**: Package result handling
- **Invoice Generation**: Automatic invoice creation

### 3. Patient Timeline
- **Chronological Integration**: Results in patient timeline
- **Visual Indicators**: Result type and status icons
- **Interactive Elements**: Click for detailed view

## API Endpoints

### Result Management
```php
GET    /api/patients/{patient}/service-results     // List patient results
POST   /api/patients/{patient}/service-results     // Create result
GET    /api/service-results/{result}               // Get result details
PUT    /api/service-results/{result}               // Update result
DELETE /api/service-results/{result}               // Delete result
```

### Result Actions
```php
POST   /api/service-results/{result}/approve       // Approve result
POST   /api/service-results/{result}/reject        // Reject result
POST   /api/service-results/save                    // AJAX save
GET    /api/service-results/{result}/download       // Download file
```

### Search & Filtering
```php
GET    /api/service-results/search?q={query}      // Search results
GET    /api/service-results/filter?{filters}       // Filter results
```

## Performance Optimization

### 1. Database Optimization
- **Indexes**: Patient, service, status indexes
- **Query Optimization**: Efficient result queries
- **Caching**: Frequently accessed results

### 2. File Optimization
- **Lazy Loading**: Load files on demand
- **Compression**: File compression for storage
- **CDN Integration**: File delivery optimization

### 3. Frontend Optimization
- **Pagination**: Large result sets
- **Lazy Loading**: Infinite scroll for results
- **Caching**: Client-side result caching

## Security & Compliance

### 1. Data Protection
- **Encryption**: Result data encryption
- **Access Control**: Role-based permissions
- **Audit Trail**: Complete access logging
- **HIPAA Compliance**: Medical data standards

### 2. File Security
- **Virus Scanning**: Upload file scanning
- **Type Validation**: Strict file type checking
- **Access Logging**: File access tracking
- **Secure Storage**: Encrypted file storage

## Error Handling

### 1. Validation Errors
- **Form Validation**: Real-time feedback
- **File Validation**: Type and size errors
- **Data Validation**: Result format validation

### 2. System Errors
- **Upload Errors**: File upload failures
- **Database Errors**: Connection and constraint issues
- **Permission Errors**: Access denied scenarios

### 3. User Experience
- **Error Messages**: Clear, actionable feedback
- **Recovery Options**: Data preservation
- **Help Documentation**: Contextual assistance

## Testing & Quality Assurance

### 1. Unit Tests
- **Model Tests**: ServiceResult model functionality
- **Controller Tests**: API endpoint testing
- **Service Tests**: Business logic validation

### 2. Integration Tests
- **File Upload**: Complete upload workflow
- **Approval Process**: End-to-end approval testing
- **Patient Integration**: Patient system integration

### 3. User Testing
- **Usability Testing**: Form usability evaluation
- **Performance Testing**: Large dataset handling
- **Security Testing**: Vulnerability assessment

---

**Related Documentation:**
- [Patients System](./patients-system.md)
- [Services & Packages](./services-packages.md)
- [API Documentation](./api-documentation.md)
