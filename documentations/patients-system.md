# Patients System Documentation

## Overview

The Patients System manages all patient-related operations including registration, profile management, medical history, appointments, and visit tracking.

## Core Components

### 1. Patient Model (`App\Models\Patient`)

**Key Relationships:**
- `visits()` - Has many patient visits
- `serviceResults()` - Has many service results
- `bills()` - Has many bills
- `appointments()` - Has many appointments

**Key Fields:**
- `first_name`, `last_name`, `email`, `phone`
- `date_of_birth`, `gender`, `address`
- `medical_history`, `allergies`, `medications`
- `emergency_contact`, `blood_type`

### 2. Patient Controller (`App\Http\Controllers\PatientController`)

**Main Methods:**

#### `index()`
- **Purpose**: Display list of all patients
- **Route**: `GET /patients`
- **Features**: Search, filtering, pagination
- **View**: `patients.index`

#### `create()`
- **Purpose**: Show patient registration form
- **Route**: `GET /patients/create`
- **View**: `patients.create`

#### `store()`
- **Purpose**: Save new patient
- **Route**: `POST /patients`
- **Validation**: Email uniqueness, required fields
- **Response**: Redirect to patient profile

#### `show()`
- **Purpose**: Display patient profile
- **Route**: `GET /patients/{patient}`
- **Data**: Patient info, recent visits, service results
- **View**: `patients.show`

#### `edit()`, `update()`
- **Purpose**: Modify patient information
- **Routes**: `GET /patients/{patient}/edit`, `PUT /patients/{patient}`
- **Features**: Profile updates, medical history

#### `destroy()`
- **Purpose**: Delete patient record
- **Route**: `DELETE /patients/{patient}`
- **Safety**: Soft delete, dependency checks

### 3. Patient Views

#### `patients/index.blade.php`
- **Features**: Search bar, patient cards, status badges
- **Data Display**: Name, contact, last visit, status
- **Actions**: View, edit, delete, schedule appointment

#### `patients/create.blade.php`
- **Form Fields**: Personal info, medical details, emergency contacts
- **Validation**: Real-time field validation
- **Features**: Auto-save, duplicate detection

#### `patients/show.blade.php`
- **Sections**: Profile overview, medical history, recent visits
- **Tabs**: Visits, Service Results, Appointments, Billing
- **Actions**: Edit profile, schedule visit, view timeline

## Patient Registration Flow

### 1. Initial Registration
```
User clicks "Register Patient" 
→ Fills registration form
→ System validates input
→ Creates patient record
→ Redirects to patient profile
```

### 2. Data Validation
- **Email**: Must be unique and valid format
- **Phone**: Format validation for different regions
- **Date of Birth**: Must be valid date, age restrictions
- **Required Fields**: First name, last name, contact info

### 3. Auto-Generated Fields
- **Patient ID**: Unique identifier (PAT-XXXXX)
- **Registration Date**: Current timestamp
- **Status**: Active by default

## Patient Profile Management

### 1. Personal Information
- **Basic Info**: Name, contact, demographics
- **Medical Info**: History, allergies, medications
- **Emergency Contacts**: Name, relationship, phone

### 2. Medical History Tracking
- **Conditions**: Chronic diseases, past illnesses
- **Allergies**: Food, medication, environmental
- **Medications**: Current and past prescriptions
- **Surgeries**: Date, type, complications

### 3. Document Management
- **Upload**: Medical reports, scans, documents
- **File Types**: PDF, JPG, PNG, DOC
- **Storage**: Secure file system with access control

## Patient Visit Integration

### 1. Visit Creation
- **Trigger**: Manual creation or appointment conversion
- **Data**: Patient selection, date/time, reason
- **Services**: Individual services or packages

### 2. Visit Status Tracking
- **Statuses**: Scheduled, In Progress, Completed, Cancelled
- **Auto-updates**: Based on service completion
- **Notifications**: Status change alerts

### 3. Visit History
- **Chronological**: Ordered by date
- **Details**: Services provided, results, billing
- **Analytics**: Visit frequency, service patterns

## Patient Search & Filtering

### 1. Search Options
- **Name**: First name, last name, full name
- **Contact**: Email, phone number
- **ID**: Patient ID number
- **Advanced**: Medical conditions, visit dates

### 2. Filter Options
- **Status**: Active, inactive, archived
- **Age Range**: Min/max age filters
- **Registration Date**: Date range selection
- **Last Visit**: Recent activity filter

### 3. Search Performance
- **Indexing**: Optimized database indexes
- **Caching**: Frequent search results
- **Pagination**: Large dataset handling

## Patient Dashboard

### 1. Overview Section
- **Patient Stats**: Total patients, new registrations
- **Activity**: Recent visits, upcoming appointments
- **Alerts**: Critical medical conditions, follow-ups

### 2. Quick Actions
- **Register New Patient**: Quick registration form
- **Schedule Appointment**: Fast appointment booking
- **Search Patients**: Advanced search interface

### 3. Reports & Analytics
- **Demographics**: Age, gender distribution
- **Visit Patterns**: Frequency, service preferences
- **Medical Trends**: Common conditions, treatments

## API Endpoints

### Patient Management
```php
GET    /api/patients              // List patients
POST   /api/patients              // Create patient
GET    /api/patients/{id}         // Get patient details
PUT    /api/patients/{id}         // Update patient
DELETE /api/patients/{id}         // Delete patient
```

### Patient Search
```php
GET    /api/patients/search?q={query}     // Search patients
GET    /api/patients/filter?{filters}      // Filter patients
```

### Patient Visits
```php
GET    /api/patients/{id}/visits           // Patient visits
POST   /api/patients/{id}/visits           // Create visit
GET    /api/patients/{id}/visits/{visit_id} // Visit details
```

## Security & Privacy

### 1. Data Protection
- **Encryption**: Sensitive data encryption
- **Access Control**: Role-based permissions
- **Audit Trail**: Access logging
- **HIPAA Compliance**: Medical data standards

### 2. Privacy Controls
- **Consent Management**: Patient consent tracking
- **Data Sharing**: Controlled information sharing
- **Anonymization**: Research data anonymization
- **Retention**: Data retention policies

## Integration Points

### 1. Billing System
- **Patient Bills**: Automatic bill generation
- **Payment History**: Transaction tracking
- **Insurance**: Insurance information management

### 2. Service Results
- **Results Linking**: Connect results to patients
- **Timeline Integration**: Patient medical timeline
- **Notifications**: Result availability alerts

### 3. Appointments
- **Scheduling**: Patient appointment management
- **Reminders**: Automated appointment reminders
- **Calendar Integration**: External calendar sync

## Error Handling

### 1. Validation Errors
- **Form Validation**: Real-time feedback
- **Duplicate Detection**: Email/phone uniqueness
- **Format Validation**: Data format requirements

### 2. System Errors
- **Database Errors**: Connection issues, constraints
- **File Upload Errors**: Size limits, format restrictions
- **Permission Errors**: Access denied scenarios

### 3. User Experience
- **Error Messages**: Clear, actionable feedback
- **Recovery Options**: Form data preservation
- **Help Links**: Contextual assistance

## Performance Optimization

### 1. Database Optimization
- **Indexes**: Strategic index placement
- **Query Optimization**: Efficient database queries
- **Caching**: Frequently accessed data

### 2. Frontend Optimization
- **Lazy Loading**: Large dataset handling
- **Pagination**: Efficient data display
- **Search Optimization**: Fast search implementation

---

**Related Documentation:**
- [Service Results System](./service-results.md)
- [Billing System](./billing-system.md)
- [API Documentation](./api-documentation.md)
