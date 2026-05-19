# Authentication & Security Documentation

## Overview

The Authentication & Security system manages user access, role-based permissions, session management, and data protection across the clinic management system.

## Core Components

### 1. User Model (`App\Models\User`)

**Key Relationships:**
- `roles()` - Belongs to many roles
- `patientResults()` - Has many service results (recorded_by)
- `approvedResults()` - Has many service results (approved_by)

**Key Fields:**
- `name`, `email`, `password`
- `role` - User role (admin, doctor, receptionist, patient)
- `status` - 'active', 'inactive', 'suspended'
- `last_login` - Last login timestamp
- `email_verified_at` - Email verification timestamp

### 2. Role Model (`App\Models\Role`)

**Available Roles:**
- **Admin**: Full system access and management
- **Doctor**: Patient management, service results, medical records
- **Receptionist**: Appointments, billing, patient registration
- **Patient**: View own records, appointments

**Permissions:**
- **Create**: Add new records
- **Read**: View existing records
- **Update**: Modify existing records
- **Delete**: Remove records

### 3. Authentication Controllers

#### Login Controller (`App\Http\Controllers\Auth\LoginController`)
- **Purpose**: Handle user authentication
- **Methods**: `showLoginForm()`, `login()`, `logout()`
- **Features**: Remember me, rate limiting, session management

#### Register Controller (`App\Http\Controllers\Auth\RegisterController`)
- **Purpose**: Handle user registration
- **Methods**: `showRegistrationForm()`, `register()`
- **Features**: Email verification, role assignment

#### Forgot Password Controller (`App\Http\Controllers\Auth\ForgotPasswordController`)
- **Purpose**: Handle password reset requests
- **Methods**: `showLinkRequestForm()`, `sendResetLinkEmail()`
- **Features**: Secure token generation, email delivery

#### Reset Password Controller (`App\Http\Controllers\Auth\ResetPasswordController`)
- **Purpose**: Handle password reset
- **Methods**: `showResetForm()`, `reset()`
- **Features**: Token validation, password strength requirements

## Authentication Flow

### 1. Login Process
```
User enters credentials
→ System validates email/password
→ Check user status and role
→ Create authenticated session
→ Redirect to appropriate dashboard
→ Log login activity
```

### 2. Registration Process
```
User fills registration form
→ System validates input
→ Create user account
→ Send verification email
→ Set default role/pending status
→ Redirect to verification notice
```

### 3. Password Reset Process
```
User requests password reset
→ System generates secure token
→ Send reset email with token
→ User clicks link, enters new password
→ System validates token and updates password
→ Login with new credentials
```

## Session Management

### 1. Session Configuration
```php
// config/session.php
'driver' => env('SESSION_DRIVER', 'file'),
'lifetime' => env('SESSION_LIFETIME', 120),
'expire_on_close' => false,
'encrypt' => true,
'files' => storage_path('framework/sessions'),
```

### 2. Session Security Features
- **Encryption**: All session data encrypted
- **Regeneration**: Session ID regeneration on login
- **Timeout**: Automatic session expiration
- **Secure Cookies**: HttpOnly, Secure flags

### 3. Custom Session Manager
```javascript
// resources/js/session-manager.js
class SessionManager {
    constructor() {
        this.checkSession();
        this.setupActivityTracking();
    }
    
    checkSession() {
        // Check session validity
        // Redirect if expired
    }
    
    setupActivityTracking() {
        // Track user activity
        // Extend session on activity
    }
}
```

## Role-Based Access Control (RBAC)

### 1. Permission Matrix

| Resource | Admin | Doctor | Receptionist | Patient |
|----------|--------|---------|---------------|---------|
| Patients | CRUD | CRUD | CRUD | Read (Own) |
| Visits | CRUD | CRUD | CRUD | Read (Own) |
| Service Results | CRUD | CRUD | Read | Read (Own) |
| Billing | CRUD | Read | CRUD | Read (Own) |
| Users | CRUD | Read | Read | Read (Own) |
| System Settings | CRUD | Read | None | None |

### 2. Middleware Implementation

#### Role Middleware (`app\Http\Middleware\RoleMiddleware`)
```php
public function handle($request, Closure $next, $role)
{
    if (!auth()->check() || !auth()->user()->hasRole($role)) {
        return redirect('/login')->with('error', 'Access denied');
    }
    
    return $next($request);
}
```

#### Permission Middleware (`app\Http\Middleware\PermissionMiddleware`)
```php
public function handle($request, Closure $next, $permission)
{
    $user = auth()->user();
    
    if (!$user || !$user->hasPermission($permission)) {
        abort(403, 'Unauthorized action');
    }
    
    return $next($request);
}
```

### 3. Route Protection
```php
// routes/web.php
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', 'AdminController@index');
    Route::resource('/users', 'UserController');
});

Route::middleware(['auth', 'role:doctor'])->group(function () {
    Route::get('/doctor', 'DoctorController@index');
    Route::resource('/service-results', 'ServiceResultController');
});
```

## Security Features

### 1. Password Security
- **Hashing**: Bcrypt password hashing
- **Strength Requirements**: Minimum 8 chars, mixed case, numbers, symbols
- **History**: Prevent password reuse
- **Expiry**: Force password changes periodically

### 2. Session Security
- **HTTPS**: Force secure connections
- **CSRF Protection**: Cross-site request forgery prevention
- **Session Fixation**: Session regeneration on login
- **Timeout**: Automatic session expiration

### 3. Input Validation & Sanitization
```php
// Request validation
$request->validate([
    'email' => 'required|email|max:255',
    'password' => 'required|string|min:8|confirmed',
    'name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/'
]);

// Input sanitization
$cleanInput = htmlspecialchars($request->input('user_input'), ENT_QUOTES, 'UTF-8');
```

### 4. SQL Injection Prevention
- **ORM Usage**: Eloquent ORM for all database operations
- **Parameter Binding**: Prepared statements for raw queries
- **Input Validation**: Strict input validation
- **Error Handling**: Secure error messages

### 5. XSS Protection
- **Output Escaping**: Blade auto-escaping
- **Content Security Policy**: CSP headers
- **Input Sanitization**: HTML purification
- **HTTP Headers**: Security headers implementation

## Frontend Security

### 1. Authentication Views

#### Login Form (`auth/login.blade.php`)
- **Features**: Email/password fields, remember me, forgot password
- **Security**: CSRF token, rate limiting feedback
- **Validation**: Real-time field validation
- **User Experience**: Loading states, error messages

#### Registration Form (`auth/register.blade.php`)
- **Fields**: Name, email, password, confirmation
- **Validation**: Password strength indicator, email availability
- **Security**: CSRF protection, honeypot field
- **Features**: Role selection, terms acceptance

#### Password Reset (`auth/passwords/reset.blade.php`)
- **Security**: Token validation, password requirements
- **Features**: Password strength meter, confirmation
- **User Experience**: Clear instructions, success feedback

### 2. JavaScript Security

#### CSRF Token Handling
```javascript
// CSRF token for AJAX requests
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
```

#### Session Management
```javascript
// Session timeout handling
function checkSessionTimeout() {
    const lastActivity = localStorage.getItem('lastActivity');
    const timeout = 120 * 60 * 1000; // 2 hours
    
    if (Date.now() - lastActivity > timeout) {
        window.location.href = '/login?session=expired';
    }
}
```

#### Input Validation
```javascript
// Client-side validation
function validateForm(formData) {
    const errors = [];
    
    if (!formData.email || !isValidEmail(formData.email)) {
        errors.push('Valid email is required');
    }
    
    if (!formData.password || formData.password.length < 8) {
        errors.push('Password must be at least 8 characters');
    }
    
    return errors;
}
```

## API Security

### 1. API Authentication
- **Token-Based**: JWT or API token authentication
- **Rate Limiting**: Prevent API abuse
- **CORS**: Cross-origin resource sharing control
- **Input Validation**: Strict API input validation

### 2. API Endpoints Security
```php
// API Authentication
Route::middleware('auth:api')->group(function () {
    Route::get('/user', 'API\UserController@profile');
    Route::post('/logout', 'API\Auth\LoginController@logout');
});

// Rate Limiting
Route::middleware('throttle:60,1')->group(function () {
    Route::post('/login', 'API\Auth\LoginController@login');
});
```

### 3. API Response Security
```php
// Secure API responses
return response()->json([
    'success' => false,
    'message' => 'Validation failed',
    'errors' => $validator->errors()
], 422);
```

## Data Protection & Privacy

### 1. Personal Data Protection
- **Encryption**: Sensitive data encryption at rest
- **Access Logs**: Complete access audit trail
- **Data Minimization**: Collect only necessary data
- **Retention Policies**: Automatic data cleanup

### 2. Medical Data Security
- **HIPAA Compliance**: Medical data protection standards
- **Access Control**: Strict medical record access
- **Audit Trails**: Complete medical data access logging
- **Consent Management**: Patient consent tracking

### 3. Privacy Controls
- **Data Sharing**: Controlled information sharing
- **Anonymization**: Research data anonymization
- **Export Rights**: Patient data export capabilities
- **Deletion Rights**: Complete data removal

## Monitoring & Logging

### 1. Security Logging
```php
// Security event logging
Log::info('User login', [
    'user_id' => auth()->id(),
    'ip_address' => $request->ip(),
    'user_agent' => $request->userAgent(),
    'timestamp' => now()
]);
```

### 2. Failed Login Tracking
- **Rate Limiting**: Block repeated failed attempts
- **IP Blocking**: Temporary IP blocking
- **Alert System**: Suspicious activity notifications
- **Lockout Policies**: Account lockout after failures

### 3. Access Monitoring
- **Page Access**: Track protected page access
- **API Usage**: Monitor API endpoint usage
- **Data Access**: Log sensitive data access
- **Anomaly Detection**: Unusual activity detection

## Security Configuration

### 1. Environment Security
```env
# .env security settings
APP_ENV=production
APP_DEBUG=false
APP_URL=https://clinic.example.com

SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

### 2. Security Headers
```php
// HTTP Security Headers
return $next($request)->header('X-Content-Type-Options', 'nosniff')
    ->header('X-Frame-Options', 'DENY')
    ->header('X-XSS-Protection', '1; mode=block')
    ->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
```

### 3. File Upload Security
```php
// Secure file upload validation
$request->validate([
    'file' => 'required|file|max:5120|mimes:pdf,jpg,png,doc',
]);

// Virus scanning
if (hasVirus($uploadedFile)) {
    return back()->with('error', 'File contains virus');
}
```

## Testing & Security Audits

### 1. Security Testing
- **Penetration Testing**: Regular security assessments
- **Vulnerability Scanning**: Automated vulnerability detection
- **Code Review**: Security-focused code reviews
- **Dependency Scanning**: Third-party library vulnerabilities

### 2. Authentication Testing
- **Login Security**: Brute force protection testing
- **Session Security**: Session hijacking prevention
- **Password Security**: Password policy enforcement
- **Multi-Factor**: 2FA implementation testing

### 3. Data Protection Testing
- **Encryption Verification**: Data encryption validation
- **Access Control**: Permission system testing
- **Audit Trail**: Logging completeness verification
- **Privacy Compliance**: Regulatory compliance testing

## Best Practices

### 1. Development Security
- **Secure Coding**: Follow secure coding practices
- **Input Validation**: Validate all user inputs
- **Error Handling**: Secure error message display
- **Code Review**: Peer security reviews

### 2. Operational Security
- **Regular Updates**: Keep dependencies updated
- **Backup Security**: Secure backup procedures
- **Monitoring**: Real-time security monitoring
- **Incident Response**: Security incident procedures

### 3. User Education
- **Security Training**: Regular security awareness
- **Password Guidelines**: Strong password practices
- **Phishing Awareness**: Email security training
- **Reporting**: Security incident reporting

---

**Related Documentation:**
- [Patients System](./patients-system.md)
- [API Documentation](./api-documentation.md)
- [Frontend Components](./frontend-components.md)
