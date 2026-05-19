# API Documentation

## Overview

The Clinic Management System provides a comprehensive RESTful API for managing patients, visits, services, billing, and service results. All API endpoints require authentication unless explicitly marked as public.

## Base Information

- **Base URL**: `http://localhost:8000/api`
- **API Version**: v1
- **Authentication**: Bearer Token / Session
- **Content-Type**: `application/json`
- **Character Encoding**: UTF-8

## Authentication

### 1. Session Authentication
Use existing web session for API access.

```http
GET /api/user/profile
Cookie: laravel_session=your_session_token
```

### 2. Token Authentication
For external applications, use API tokens.

```http
GET /api/user/profile
Authorization: Bearer your_api_token
```

### 3. Login Endpoint
```http
POST /api/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password",
    "remember": true
}
```

**Response:**
```json
{
    "success": true,
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com",
        "role": "doctor"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
}
```

## Response Format

### Success Response
```json
{
    "success": true,
    "data": {
        // Response data
    },
    "message": "Operation completed successfully"
}
```

### Error Response
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "field": ["Error message"]
    }
}
```

### Paginated Response
```json
{
    "success": true,
    "data": [
        // Array of items
    ],
    "meta": {
        "current_page": 1,
        "per_page": 15,
        "total": 100,
        "last_page": 7
    }
}
```

## Patient API Endpoints

### 1. List Patients
```http
GET /api/patients?page=1&per_page=15&search=john&status=active
```

**Parameters:**
- `page` (integer): Page number (default: 1)
- `per_page` (integer): Items per page (default: 15, max: 100)
- `search` (string): Search by name, email, phone
- `status` (string): Filter by status (active, inactive, archived)

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "patient_id": "PAT-00001",
            "first_name": "John",
            "last_name": "Doe",
            "email": "john.doe@example.com",
            "phone": "+1234567890",
            "date_of_birth": "1980-01-01",
            "gender": "male",
            "status": "active",
            "created_at": "2024-01-01T10:00:00Z",
            "updated_at": "2024-01-01T10:00:00Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "per_page": 15,
        "total": 1,
        "last_page": 1
    }
}
```

### 2. Create Patient
```http
POST /api/patients
Content-Type: application/json

{
    "first_name": "Jane",
    "last_name": "Smith",
    "email": "jane.smith@example.com",
    "phone": "+1234567890",
    "date_of_birth": "1990-05-15",
    "gender": "female",
    "address": "123 Main St, City, State",
    "medical_history": "No known allergies",
    "emergency_contact": {
        "name": "John Smith",
        "relationship": "husband",
        "phone": "+1234567891"
    }
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 2,
        "patient_id": "PAT-00002",
        "first_name": "Jane",
        "last_name": "Smith",
        "email": "jane.smith@example.com",
        "status": "active"
    },
    "message": "Patient created successfully"
}
```

### 3. Get Patient Details
```http
GET /api/patients/{id}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "patient_id": "PAT-00001",
        "first_name": "John",
        "last_name": "Doe",
        "email": "john.doe@example.com",
        "phone": "+1234567890",
        "date_of_birth": "1980-01-01",
        "gender": "male",
        "address": "123 Main St, City, State",
        "medical_history": "No known allergies",
        "allergies": "None",
        "medications": "None",
        "emergency_contact": {
            "name": "Jane Doe",
            "relationship": "wife",
            "phone": "+1234567891"
        },
        "visits": [
            {
                "id": 1,
                "visit_date": "2024-01-15",
                "visit_time": "10:00",
                "status": "completed"
            }
        ],
        "service_results": [
            {
                "id": 1,
                "service_name": "Blood Test",
                "result_type": "numeric",
                "status": "approved"
            }
        ]
    }
}
```

### 4. Update Patient
```http
PUT /api/patients/{id}
Content-Type: application/json

{
    "first_name": "John",
    "last_name": "Doe Jr.",
    "phone": "+1234567890",
    "medical_history": "Updated medical history"
}
```

### 5. Delete Patient
```http
DELETE /api/patients/{id}
```

**Response:**
```json
{
    "success": true,
    "message": "Patient deleted successfully"
}
```

## Visit API Endpoints

### 1. List Patient Visits
```http
GET /api/patients/{patient_id}/visits?status=completed&date_from=2024-01-01
```

**Parameters:**
- `status` (string): Filter by status
- `date_from` (date): Filter visits from date
- `date_to` (date): Filter visits to date

### 2. Create Visit
```http
POST /api/patients/{patient_id}/visits
Content-Type: application/json

{
    "visit_date": "2024-01-20",
    "visit_time": "14:30",
    "reason": "Regular checkup",
    "notes": "Patient complains of headache",
    "selected_services": [
        {
            "id": 1,
            "name": "Blood Test",
            "price": 50.00,
            "result_type": "numeric"
        }
    ],
    "selected_package": null,
    "total_amount": 50.00
}
```

### 3. Get Visit Details
```http
GET /api/visits/{id}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "patient": {
            "id": 1,
            "name": "John Doe",
            "patient_id": "PAT-00001"
        },
        "visit_date": "2024-01-15",
        "visit_time": "10:00",
        "reason": "Regular checkup",
        "status": "completed",
        "services": [
            {
                "id": 1,
                "service_name": "Blood Test",
                "service_price": 50.00,
                "status": "completed"
            }
        ],
        "bill": {
            "id": 1,
            "bill_number": "BILL-00001",
            "total_amount": 50.00,
            "paid_amount": 0.00,
            "balance_due": 50.00,
            "status": "sent"
        }
    }
}
```

## Service Results API Endpoints

### 1. List Patient Service Results
```http
GET /api/patients/{patient_id}/service-results?status=approved&result_type=file
```

**Parameters:**
- `status` (string): Filter by status (draft, pending_approval, approved, rejected)
- `result_type` (string): Filter by result type (text, numeric, file)
- `date_from` (date): Filter results from date
- `date_to` (date): Filter results to date

### 2. Create Service Result
```http
POST /api/patients/{patient_id}/service-results
Content-Type: application/json

{
    "service_id": 1,
    "visit_id": 1,
    "result_type": "text",
    "result_text": "Blood test results are normal",
    "notes": "Patient in good health",
    "status": "draft"
}
```

### 3. Upload File Result
```http
POST /api/service-results/upload
Content-Type: multipart/form-data

result_file: [file]
service_id: 1
visit_id: 1
patient_id: 1
result_type: file
notes: "X-ray results"
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "result_type": "file",
        "result_file_name": "xray_20240115.jpg",
        "result_file_path": "uploads/service-results/1/2024/01/xray_20240115.jpg",
        "file_size": 2048576,
        "status": "draft"
    },
    "message": "File uploaded successfully"
}
```

### 4. Approve/Reject Result
```http
POST /api/service-results/{id}/approve
Content-Type: application/json

{
    "approval_notes": "Results verified and approved"
}
```

```http
POST /api/service-results/{id}/reject
Content-Type: application/json

{
    "rejection_reason": "Incomplete information provided",
    "correction_notes": "Please add reference ranges"
}
```

## Billing API Endpoints

### 1. List Bills
```http
GET /api/bills?status=pending&patient_id=1
```

**Parameters:**
- `status` (string): Filter by status
- `patient_id` (integer): Filter by patient
- `date_from` (date): Filter bills from date
- `date_to` (date): Filter bills to date

### 2. Get Bill Details
```http
GET /api/bills/{id}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "bill_number": "BILL-00001",
        "patient": {
            "id": 1,
            "name": "John Doe",
            "patient_id": "PAT-00001"
        },
        "bill_type": "service",
        "total_amount": 150.00,
        "paid_amount": 50.00,
        "balance_due": 100.00,
        "status": "partial",
        "due_date": "2024-02-15",
        "items": [
            {
                "id": 1,
                "description": "Blood Test",
                "quantity": 1,
                "unit_price": 50.00,
                "total_price": 50.00,
                "item_type": "service"
            },
            {
                "id": 2,
                "description": "X-Ray",
                "quantity": 1,
                "unit_price": 100.00,
                "total_price": 100.00,
                "item_type": "service"
            }
        ],
        "payments": [
            {
                "id": 1,
                "amount": 50.00,
                "payment_method": "cash",
                "payment_date": "2024-01-20",
                "status": "completed"
            }
        ]
    }
}
```

### 3. Process Payment
```http
POST /api/bills/{id}/pay
Content-Type: application/json

{
    "amount": 100.00,
    "payment_method": "card",
    "card_number": "****-****-****-1234",
    "expiry_date": "12/25",
    "cvv": "123"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "payment_id": "PAY-00001",
        "amount": 100.00,
        "status": "completed",
        "transaction_id": "txn_1234567890",
        "processed_at": "2024-01-20T15:30:00Z"
    },
    "message": "Payment processed successfully"
}
```

## Services & Packages API Endpoints

### 1. List Services
```http
GET /api/services?category=lab&active=true
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "service_name": "Blood Test",
            "description": "Complete blood count",
            "price": 50.00,
            "category": "lab",
            "result_type": "numeric",
            "active": true,
            "created_at": "2024-01-01T10:00:00Z"
        }
    ]
}
```

### 2. List Packages
```http
GET /api/packages?active=true
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "package_name": "Basic Health Checkup",
            "description": "Complete health screening",
            "total_cost": 200.00,
            "services": [
                {
                    "id": 1,
                    "service_name": "Blood Test",
                    "price": 50.00
                },
                {
                    "id": 2,
                    "service_name": "X-Ray",
                    "price": 150.00
                }
            ],
            "active": true
        }
    ]
}
```

## User Management API Endpoints

### 1. Get Current User
```http
GET /api/user/profile
```

### 2. Update Profile
```http
PUT /api/user/profile
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john.doe@example.com",
    "phone": "+1234567890"
}
```

### 3. Change Password
```http
POST /api/user/change-password
Content-Type: application/json

{
    "current_password": "old_password",
    "new_password": "new_secure_password",
    "new_password_confirmation": "new_secure_password"
}
```

## Search & Filtering

### 1. Global Search
```http
GET /api/search?q=john&type=patients
```

**Parameters:**
- `q` (string): Search query
- `type` (string): Search type (patients, visits, bills, results)

### 2. Advanced Filtering
```http
GET /api/patients/filter?age_min=25&age_max=65&gender=male&status=active
```

## Error Codes

| Code | Description | Solution |
|-------|-------------|-----------|
| 200 | Success | Request completed successfully |
| 201 | Created | Resource created successfully |
| 400 | Bad Request | Invalid request data |
| 401 | Unauthorized | Authentication required |
| 403 | Forbidden | Insufficient permissions |
| 404 | Not Found | Resource not found |
| 422 | Validation Error | Input validation failed |
| 429 | Too Many Requests | Rate limit exceeded |
| 500 | Internal Error | Server error occurred |

## Rate Limiting

- **Standard**: 60 requests per minute
- **Authentication**: 5 requests per minute
- **File Upload**: 10 requests per minute

## File Upload

### Supported Formats
- **Images**: JPG, PNG, GIF (max 5MB)
- **Documents**: PDF, DOC, DOCX (max 10MB)
- **Medical**: DICOM (max 50MB)

### Upload Endpoint
```http
POST /api/upload
Content-Type: multipart/form-data

file: [file]
type: service_result
patient_id: 1
```

## Webhooks

### Payment Status Webhook
```http
POST /api/webhooks/payment-status
Content-Type: application/json

{
    "transaction_id": "txn_1234567890",
    "status": "completed",
    "amount": 100.00,
    "bill_id": 1,
    "timestamp": "2024-01-20T15:30:00Z"
}
```

## SDK & Libraries

### JavaScript SDK
```javascript
// Include SDK
<script src="/api/js/clinic-sdk.js"></script>

// Initialize
const clinic = new ClinicAPI({
    baseURL: 'http://localhost:8000/api',
    token: 'your_api_token'
});

// Usage
clinic.patients.list({ page: 1, search: 'john' })
    .then(response => console.log(response.data))
    .catch(error => console.error(error));
```

### PHP SDK
```php
// Include SDK
require_once 'vendor/clinic-sdk/autoload.php';

// Initialize
$clinic = new ClinicAPI('your_api_token');

// Usage
$patients = $clinic->patients()->list(['page' => 1, 'search' => 'john']);
```

## Testing

### 1. Postman Collection
Download the Postman collection for easy API testing:
[API Collection Download Link]

### 2. Sandbox Environment
- **URL**: `http://localhost:8000/api`
- **Test Credentials**: `test@example.com` / `password`
- **Test Data**: Pre-populated with sample data

---

**Related Documentation:**
- [Patients System](./patients-system.md)
- [Service Results](./service-results.md)
- [Billing System](./billing-system.md)
