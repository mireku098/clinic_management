# Database Schema Documentation

## Overview

The Clinic Management System uses MySQL database with a well-structured schema that supports patient management, visits, services, billing, and service results. The database follows Laravel conventions with proper relationships and indexing.

## Database Configuration

### Environment Settings
```env
# .env database configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=clinic_management
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Connection Configuration
```php
// config/database.php
'mysql' => [
    'driver' => 'mysql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'forge'),
    'username' => env('DB_USERNAME', 'forge'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
],
```

## Core Tables

### 1. Users Table

**Purpose**: User authentication and authorization

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'doctor', 'receptionist', 'patient') NOT NULL DEFAULT 'patient',
    status ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
    phone VARCHAR(20) NULL,
    last_login TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_users_email (email),
    INDEX idx_users_role (role),
    INDEX idx_users_status (status)
);
```

**Key Fields:**
- `id`: Primary key, auto-increment
- `email`: Unique email address for login
- `password`: Bcrypt hashed password
- `role`: User role for permissions
- `status`: Account status

### 2. Patients Table

**Purpose**: Patient demographic and medical information

```sql
CREATE TABLE patients (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NULL,
    phone VARCHAR(20) UNIQUE NULL,
    date_of_birth DATE NULL,
    gender ENUM('male', 'female', 'other') NULL,
    address TEXT NULL,
    medical_history TEXT NULL,
    allergies TEXT NULL,
    medications TEXT NULL,
    emergency_contact_name VARCHAR(255) NULL,
    emergency_contact_phone VARCHAR(20) NULL,
    emergency_contact_relationship VARCHAR(50) NULL,
    blood_type VARCHAR(10) NULL,
    photo VARCHAR(255) NULL,
    status ENUM('active', 'inactive', 'archived') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_patients_patient_id (patient_id),
    INDEX idx_patients_email (email),
    INDEX idx_patients_phone (phone),
    INDEX idx_patients_status (status),
    INDEX idx_patients_name (first_name, last_name)
);
```

**Key Fields:**
- `patient_id`: Unique patient identifier (PAT-XXXXX)
- `first_name`, `last_name`: Patient name
- `email`, `phone`: Contact information
- `medical_history`: Medical background
- `emergency_contact_*`: Emergency contact details

### 3. Patient Visits Table

**Purpose**: Patient visit records and service tracking

```sql
CREATE TABLE patient_visits (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id BIGINT UNSIGNED NOT NULL,
    visit_date DATE NOT NULL,
    visit_time TIME NOT NULL,
    reason TEXT NULL,
    notes TEXT NULL,
    package_id BIGINT UNSIGNED NULL,
    selected_services JSON NULL,
    selected_package JSON NULL,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    balance_due DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    payment_status ENUM('pending', 'partial', 'paid', 'overdue') NOT NULL DEFAULT 'pending',
    status ENUM('scheduled', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'scheduled',
    completion_status ENUM('pending', 'in_progress', 'completed') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE SET NULL,
    INDEX idx_visits_patient (patient_id),
    INDEX idx_visits_date (visit_date),
    INDEX idx_visits_status (status),
    INDEX idx_visits_completion (completion_status)
);
```

**Key Fields:**
- `patient_id`: Foreign key to patients table
- `visit_date`, `visit_time`: Visit scheduling
- `selected_services`: JSON array of selected services
- `selected_package`: JSON package data
- `total_amount`: Total visit cost
- `status`: Visit progress status

### 4. Services Table

**Purpose**: Service catalog and pricing

```sql
CREATE TABLE services (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    category VARCHAR(100) NULL,
    result_type ENUM('text', 'numeric', 'file') NOT NULL DEFAULT 'text',
    duration_minutes INT UNSIGNED NULL,
    requires_appointment BOOLEAN NOT NULL DEFAULT TRUE,
    active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_services_name (service_name),
    INDEX idx_services_category (category),
    INDEX idx_services_result_type (result_type),
    INDEX idx_services_active (active)
);
```

**Key Fields:**
- `service_name`: Human-readable service name
- `price`: Current service price
- `category`: Service categorization
- `result_type`: Expected result type
- `active`: Service availability

### 5. Packages Table

**Purpose**: Service bundles and package pricing

```sql
CREATE TABLE packages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    package_name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    total_cost DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    duration_minutes INT UNSIGNED NULL,
    popular BOOLEAN NOT NULL DEFAULT FALSE,
    active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_packages_name (package_name),
    INDEX idx_packages_active (active),
    INDEX idx_packages_popular (popular)
);
```

**Key Fields:**
- `package_name`: Package display name
- `total_cost`: Package total price
- `popular`: Mark as popular package
- `active`: Package availability

### 6. Package Services (Pivot Table)

**Purpose**: Many-to-many relationship between packages and services

```sql
CREATE TABLE package_services (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    package_id BIGINT UNSIGNED NOT NULL,
    service_id BIGINT UNSIGNED NOT NULL,
    included_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    optional BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    UNIQUE KEY unique_package_service (package_id, service_id),
    INDEX idx_package_services_package (package_id),
    INDEX idx_package_services_service (service_id)
);
```

### 7. Patient Services Table

**Purpose**: Link visits to individual services

```sql
CREATE TABLE patient_services (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id BIGINT UNSIGNED NOT NULL,
    visit_id BIGINT UNSIGNED NOT NULL,
    service_id BIGINT UNSIGNED NOT NULL,
    service_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status ENUM('pending', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (visit_id) REFERENCES patient_visits(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    INDEX idx_patient_services_patient (patient_id),
    INDEX idx_patient_services_visit (visit_id),
    INDEX idx_patient_services_service (service_id),
    INDEX idx_patient_services_status (status)
);
```

### 8. Service Results Table

**Purpose**: Medical test results and findings

```sql
CREATE TABLE service_results (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id BIGINT UNSIGNED NOT NULL,
    visit_id BIGINT UNSIGNED NULL,
    service_id BIGINT UNSIGNED NULL,
    package_id BIGINT UNSIGNED NULL,
    patient_service_id BIGINT UNSIGNED NULL,
    result_type ENUM('text', 'numeric', 'file') NOT NULL DEFAULT 'text',
    result_text TEXT NULL,
    result_numeric DECIMAL(15,4) NULL,
    result_file_path VARCHAR(500) NULL,
    result_file_name VARCHAR(255) NULL,
    result_file_size INT UNSIGNED NULL,
    notes TEXT NULL,
    status ENUM('draft', 'pending_approval', 'approved', 'rejected') NOT NULL DEFAULT 'draft',
    recorded_by BIGINT UNSIGNED NULL,
    approved_by BIGINT UNSIGNED NULL,
    approved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (visit_id) REFERENCES patient_visits(id) ON DELETE SET NULL,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL,
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE SET NULL,
    FOREIGN KEY (patient_service_id) REFERENCES patient_services(id) ON DELETE SET NULL,
    FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_service_results_patient (patient_id),
    INDEX idx_service_results_visit (visit_id),
    INDEX idx_service_results_service (service_id),
    INDEX idx_service_results_status (status),
    INDEX idx_service_results_type (result_type)
);
```

### 9. Bills Table

**Purpose**: Financial billing and payment tracking

```sql
CREATE TABLE bills (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    bill_number VARCHAR(20) UNIQUE NOT NULL,
    patient_id BIGINT UNSIGNED NOT NULL,
    visit_id BIGINT UNSIGNED NULL,
    bill_type ENUM('service', 'package', 'adjustment') NOT NULL DEFAULT 'service',
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    paid_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    balance_due DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    due_date DATE NULL,
    status ENUM('draft', 'sent', 'partial', 'paid', 'overdue', 'cancelled') NOT NULL DEFAULT 'draft',
    generated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    sent_date TIMESTAMP NULL,
    paid_date TIMESTAMP NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (visit_id) REFERENCES patient_visits(id) ON DELETE SET NULL,
    INDEX idx_bills_patient (patient_id),
    INDEX idx_bills_visit (visit_id),
    INDEX idx_bills_number (bill_number),
    INDEX idx_bills_status (status),
    INDEX idx_bills_due_date (due_date)
);
```

### 10. Bill Items Table

**Purpose**: Individual line items for bills

```sql
CREATE TABLE bill_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    bill_id BIGINT UNSIGNED NOT NULL,
    service_id BIGINT UNSIGNED NULL,
    package_id BIGINT UNSIGNED NULL,
    description VARCHAR(255) NOT NULL,
    quantity DECIMAL(10,2) NOT NULL DEFAULT 1.00,
    unit_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    item_type ENUM('service', 'package', 'adjustment') NOT NULL DEFAULT 'service',
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (bill_id) REFERENCES bills(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL,
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE SET NULL,
    INDEX idx_bill_items_bill (bill_id),
    INDEX idx_bill_items_service (service_id),
    INDEX idx_bill_items_package (package_id)
);
```

### 11. Payments Table

**Purpose**: Payment transaction records

```sql
CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    bill_id BIGINT UNSIGNED NOT NULL,
    patient_id BIGINT UNSIGNED NOT NULL,
    payment_method ENUM('cash', 'card', 'bank_transfer', 'insurance', 'check') NOT NULL,
    amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    payment_date TIMESTAMP NULL,
    transaction_id VARCHAR(100) NULL,
    authorization_code VARCHAR(100) NULL,
    status ENUM('pending', 'completed', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
    failure_reason TEXT NULL,
    refund_amount DECIMAL(10,2) NULL,
    refund_date TIMESTAMP NULL,
    refund_reason TEXT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (bill_id) REFERENCES bills(id) ON DELETE CASCADE,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    INDEX idx_payments_bill (bill_id),
    INDEX idx_payments_patient (patient_id),
    INDEX idx_payments_status (status),
    INDEX idx_payments_date (payment_date)
);
```

## Supporting Tables

### 1. Password Resets Table

```sql
CREATE TABLE password_resets (
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_password_resets_email (email),
    INDEX idx_password_resets_token (token)
);
```

### 2. Failed Jobs Table

```sql
CREATE TABLE failed_jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(255) UNIQUE NOT NULL,
    connection TEXT NULL,
    queue TEXT NULL,
    payload LONGTEXT NULL,
    exception LONGTEXT NULL,
    failed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_failed_jobs_uuid (uuid)
);
```

### 3. Sessions Table

```sql
CREATE TABLE sessions (
    id VARCHAR(255) NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT UNSIGNED NULL,
    
    PRIMARY KEY (id),
    INDEX idx_sessions_user_id (user_id),
    INDEX idx_sessions_last_activity (last_activity)
);
```

## Relationships Diagram

```
Users (1) ←→ (N) Patient Visits ←→ (1) Patients
  ↓
Service Results ←→ (1) Services
  ↓
Bills ←→ (N) Bill Items ←→ Services
  ↓
Payments ←→ Bills
  ↓
Packages ←→ (N) Package Services ←→ Services
```

## Indexing Strategy

### 1. Primary Indexes
- All tables have proper primary keys
- Foreign keys are indexed for join performance
- Unique constraints on important fields

### 2. Search Indexes
```sql
-- Patient search optimization
CREATE INDEX idx_patients_search ON patients(first_name, last_name, email, patient_id);

-- Service search optimization
CREATE INDEX idx_services_search ON services(service_name, category, active);

-- Bill search optimization
CREATE INDEX idx_bills_search ON bills(bill_number, patient_id, status);
```

### 3. Performance Indexes
```sql
-- Date-based queries
CREATE INDEX idx_visits_date_status ON patient_visits(visit_date, status);
CREATE INDEX idx_bills_due_status ON bills(due_date, status);

-- Status-based queries
CREATE INDEX idx_service_results_status_type ON service_results(status, result_type);
CREATE INDEX idx_patient_services_status ON patient_services(status, created_at);
```

## Data Integrity

### 1. Foreign Key Constraints
- All relationships properly constrained
- CASCADE deletes for dependent data
- SET NULL for optional relationships
- Referential integrity maintained

### 2. Check Constraints
```sql
-- Positive values
ALTER TABLE services ADD CONSTRAINT chk_services_price 
CHECK (price >= 0);

ALTER TABLE bills ADD CONSTRAINT chk_bills_amount 
CHECK (total_amount >= 0 AND paid_amount >= 0);

-- Valid statuses
ALTER TABLE service_results ADD CONSTRAINT chk_service_results_status 
CHECK (status IN ('draft', 'pending_approval', 'approved', 'rejected'));
```

### 3. Unique Constraints
```sql
-- Patient identifiers
ALTER TABLE patients ADD CONSTRAINT unq_patients_patient_id 
UNIQUE (patient_id);

-- Bill numbers
ALTER TABLE bills ADD CONSTRAINT unq_bills_number 
UNIQUE (bill_number);

-- Email uniqueness
ALTER TABLE patients ADD CONSTRAINT unq_patients_email 
UNIQUE (email);
```

## Migration Strategy

### 1. Version Control
```php
// Migration naming convention
class CreatePatientsTable extends Migration
{
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('patient_id', 20)->unique();
            $table->string('first_name');
            $table->string('last_name');
            // ... other fields
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('patients');
    }
}
```

### 2. Seeding Strategy
```php
// Database seeding
class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UsersSeeder::class,
            ServicesSeeder::class,
            PackagesSeeder::class,
        ]);
    }
}
```

## Backup & Recovery

### 1. Backup Strategy
```bash
# Full database backup
mysqldump -u root -p clinic_management > backup_$(date +%Y%m%d_%H%M%S).sql

# Compressed backup
mysqldump -u root -p clinic_management | gzip > backup_$(date +%Y%m%d_%H%M%S).sql.gz
```

### 2. Recovery Procedures
```bash
# Restore from backup
mysql -u root -p clinic_management < backup_20240115_120000.sql

# Restore from compressed backup
gunzip < backup_20240115_120000.sql.gz | mysql -u root -p clinic_management
```

### 3. Point-in-Time Recovery
```sql
-- Enable binary logging
SET GLOBAL log_bin_trust_function_creators = 1;
SET GLOBAL log_bin = ON;

-- Point-in-time recovery
mysqlbinlog --start-datetime="2024-01-15 10:00:00" \
           --stop-datetime="2024-01-15 12:00:00" \
           /var/log/mysql/mysql-bin.000001
```

## Performance Optimization

### 1. Query Optimization
```php
// Efficient queries with proper indexing
$patients = Patient::where('status', 'active')
    ->orderBy('last_name')
    ->select(['id', 'first_name', 'last_name', 'patient_id'])
    ->paginate(15);

// Join optimization
$results = ServiceResult::with(['patient', 'service'])
    ->whereHas('patient', function($query) {
        $query->where('status', 'active');
    })
    ->orderBy('created_at', 'desc')
    ->get();
```

### 2. Caching Strategy
```php
// Query result caching
$services = Cache::remember('services.active', 3600, function () {
    return Service::where('active', true)->get();
});

// Fragment caching
$html = Cache::remember('patient.card.'.$patient->id, 1800, function () use ($patient) {
    return view('partials.patient-card', compact('patient'))->render();
});
```

### 3. Database Configuration
```sql
-- MySQL configuration optimization
SET GLOBAL innodb_buffer_pool_size = 256M;
SET GLOBAL innodb_log_file_size = 64M;
SET GLOBAL query_cache_size = 64M;
SET GLOBAL query_cache_type = ON;
```

---

**Related Documentation:**
- [Patients System](./patients-system.md)
- [Service Results](./service-results.md)
- [Billing System](./billing-system.md)
