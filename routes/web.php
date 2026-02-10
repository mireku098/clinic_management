<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceResultController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| UI-first routing for the clinic admin dashboard.
| All routes currently return Blade views directly.
| Controllers can be introduced later without breaking links.
|--------------------------------------------------------------------------
*/

// Landing (redirect to login)
Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

/*
|--------------------------------------------------------------------------
| Patients
|--------------------------------------------------------------------------
*/
Route::get('/patients', function () {
    $patients = \App\Models\Patient::latest()->get();
    return view('patients', compact('patients'));
})->name('patients');

Route::get('/patients/add', function () {
    return view('patients.add');
})->name('patients.add');

Route::post('/patients/add', [PatientController::class, 'store'])->name('patients.store');

Route::get('/patients/{id}/json', [PatientController::class, 'getPatientJson'])->name('patients.json');

Route::get('/patients/context', [PatientController::class, 'context'])->name('patients.context');
Route::get('/patients/{patientCode}/visits', [PatientController::class, 'visits'])->name('patients.visits');

// Patient Context API Routes
Route::get('/api/patients/{patientCode}/overview', [PatientController::class, 'getOverviewData']);
Route::get('/api/patients/{patientCode}/vitals', [PatientController::class, 'getVitalsData']);
Route::get('/api/patients/{patientCode}/medical-history', [PatientController::class, 'getMedicalHistoryData']);
Route::get('/api/patients/{patientCode}/billing', [PatientController::class, 'getBillingData']);

// Move specific visit routes before parameterized ones
Route::get('/visits', [VisitController::class, 'index'])->name('visits');
Route::get('/visits/add', [VisitController::class, 'create'])->name('visits.add');
Route::get('/visits/{id}/edit', [VisitController::class, 'edit'])->name('visits.edit');
Route::put('/visits/{id}', [VisitController::class, 'update'])->name('visits.update');
Route::delete('/visits/{id}', [VisitController::class, 'destroy'])->name('visits.destroy');
Route::post('/visits', [VisitController::class, 'store'])->name('visits.store');

// Parameterized route should come last
Route::get('/visits/{visit}', [VisitController::class, 'show'])->name('visits.show');

// API routes for patient context modules
Route::get('/api/patients', [PatientController::class, 'getPatientsApi']);
Route::get('/api/patients/{patientCode}/overview', [PatientController::class, 'getOverviewData']);
Route::get('/api/patients/{patientCode}/visits', [PatientController::class, 'getVisitsData']);
Route::get('/api/patients/{patientCode}/bills', [PatientController::class, 'getBillsData']);
Route::get('/api/patients/{patientCode}/service-results', [PatientController::class, 'getServiceResultsData']);
Route::get('/api/patients/{patientCode}/vitals', [PatientController::class, 'getVitalsData']);
Route::get('/api/services', [ServiceController::class, 'getServicesApi']);
Route::get('/api/service-results/{resultId}', [ServiceResultController::class, 'showApi']);
Route::post('/api/service-results/update', [ServiceResultController::class, 'updateFromPatientContext']);
Route::post('/api/service-results', [ServiceResultController::class, 'storeFromPatientContext']);

Route::get('/patients/{patient}/visits/{visit}/services/{service}/result', [ServiceResultController::class, 'showResultPage'])->name('service-results.result-page');
Route::get('/patients/{patient}/service-results', [ServiceResultController::class, 'patientServiceResults'])->name('service-results.patient-timeline');
Route::post('/service-results/save', [ServiceResultController::class, 'saveResult'])->name('service-results.save');

Route::get('/patients/edit', function () {
    $patientCode = request('code');
    $patient = \App\Models\Patient::where('patient_code', $patientCode)->firstOrFail();
    return view('patients.edit', compact('patient'));
})->name('patients.edit');

Route::put('/patients/update/{patient}', [PatientController::class, 'update'])->name('patients.update');

// Test file upload route
Route::get('/test-upload', function () {
    // Get all uploaded files for display
    $allFiles = [];
    $storagePath = storage_path('app/public/test-uploads');
    if (is_dir($storagePath)) {
        $files = scandir($storagePath);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $storagePath . '/' . $file;
                $allFiles[] = (object) [
                    'name' => $file,
                    'path' => 'test-uploads/' . $file,
                    'size' => filesize($filePath),
                    'created_at' => date('Y-m-d H:i:s', filemtime($filePath))
                ];
            }
        }
    }
    
    return view('test-upload', [
        'uploaded_file' => session('uploaded_file'),
        'all_files' => $allFiles
    ]);
})->name('test.upload');

Route::post('/test-upload', function () {
    try {
        if (!request()->hasFile('test_file')) {
            return back()->with('error', 'No file uploaded');
        }
        
        $file = request()->file('test_file');
        
        if (!$file->isValid()) {
            return back()->with('error', 'File upload failed: ' . $file->getErrorMessage());
        }
        
        // Store the file
        $path = $file->store('test-uploads', 'public');
        
        // Get file info
        $fileInfo = [
            'path' => $path,
            'size' => $file->getSize(),
            'mime' => $file->getMimeType(),
            'original' => $file->getClientOriginalName(),
            'image_url' => asset('storage/' . $path)
        ];
        
        return back()
            ->with('success', 'File uploaded successfully!')
            ->with('uploaded_file', $fileInfo);
            
    } catch (\Exception $e) {
        return back()->with('error', 'Upload failed: ' . $e->getMessage());
    }
})->name('test.upload.post');

/*
|--------------------------------------------------------------------------
| Appointments
|--------------------------------------------------------------------------
*/
// Visit routes are already defined above with correct order

Route::get('/patients/search', function () {
    $query = request('q');
    
    $patients = \App\Models\Patient::where('first_name', 'like', "%{$query}%")
        ->orWhere('last_name', 'like', "%{$query}%")
        ->orWhere('patient_code', 'like', "%{$query}%")
        ->orWhere('phone', 'like', "%{$query}%")
        ->limit(10)
        ->get(['id', 'patient_code', 'first_name', 'last_name', 'phone']);
    
    return response()->json($patients);
})->name('patients.search');

Route::get('/appointments', function () {
    return view('appointments');
})->name('appointments');

Route::get('/appointments/add', function () {
    return view('appointments.add');
})->name('appointments.add');

/*
|--------------------------------------------------------------------------
| Services
|--------------------------------------------------------------------------
*/
Route::get('/services', [ServiceController::class, 'index'])->name('services');
Route::get('/services/add', [ServiceController::class, 'create'])->name('services.add');
Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
Route::get('/services/{id}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/services/{id}/edit', [ServiceController::class, 'edit'])->name('services.edit');
Route::put('/services/{id}', [ServiceController::class, 'update'])->name('services.update');
Route::delete('/services/{id}', [ServiceController::class, 'destroy'])->name('services.destroy');

/*
|--------------------------------------------------------------------------
| Service Results
|--------------------------------------------------------------------------
*/
Route::get('/service-results', [ServiceResultController::class, 'index'])->name('service-results.index');
Route::get('/service-results/add', [ServiceResultController::class, 'create'])->name('service-results.create');
Route::post('/service-results', [ServiceResultController::class, 'store'])->name('service-results.store');
Route::get('/service-results/{id}', [ServiceResultController::class, 'show'])->name('service-results.show');
Route::get('/service-results/{id}/edit', [ServiceResultController::class, 'edit'])->name('service-results.edit');
Route::put('/service-results/{id}', [ServiceResultController::class, 'update'])->name('service-results.update');
Route::delete('/service-results/{id}', [ServiceResultController::class, 'destroy'])->name('service-results.destroy');
Route::post('/service-results/{id}/submit-approval', [ServiceResultController::class, 'submitForApproval'])->name('service-results.submit-approval');
Route::post('/service-results/{id}/approve', [ServiceResultController::class, 'approveResult'])->name('service-results.approve');

/*
|--------------------------------------------------------------------------
| Billing & Payments
|--------------------------------------------------------------------------
*/
Route::get('/billing', [App\Http\Controllers\BillingController::class, 'index'])->name('billing');
Route::get('/billing/get-bills', [App\Http\Controllers\BillingController::class, 'getBills'])->name('billing.get-bills');
Route::get('/billing/get-bill-details/{billId}', [App\Http\Controllers\BillingController::class, 'getBillDetails'])->name('billing.get-bill-details');
Route::post('/billing/create-from-package', [App\Http\Controllers\BillingController::class, 'createFromPackage'])->name('billing.create-from-package');
Route::post('/billing/create-from-services', [App\Http\Controllers\BillingController::class, 'createFromServices'])->name('billing.create-from-services');
Route::post('/billing/process-payment', [App\Http\Controllers\BillingController::class, 'processPayment'])->name('billing.process-payment');
Route::put('/billing/update-payment-status/{billId}', [App\Http\Controllers\BillingController::class, 'updatePaymentStatus'])->name('billing.update-payment-status');

/*
|--------------------------------------------------------------------------
| Users & Packages
|--------------------------------------------------------------------------
*/
Route::get('/users', function () {
    return view('users');
})->name('users');

Route::get('/packages', function () {
    $packages = \App\Models\Package::with('services.service')->get();
    return view('packages', compact('packages'));
})->name('packages');

Route::get('/packages/add', function () {
    $services = \App\Models\Service::where('status', 'active')->get();
    return view('packages.add', compact('services'));
})->name('packages.add');

Route::get('/packages/{id}/edit', function ($id) {
    $package = \App\Models\Package::with('services')->findOrFail($id);
    $services = \App\Models\Service::where('status', 'active')->get();
    return view('packages.edit', compact('package', 'services'));
})->name('packages.edit');

Route::post('/packages', [App\Http\Controllers\PackageController::class, 'store'])->name('packages.store');
Route::put('/packages/{id}', [App\Http\Controllers\PackageController::class, 'update'])->name('packages.update');

/*
|--------------------------------------------------------------------------
| Reports
|--------------------------------------------------------------------------
*/
Route::get('/reports', function () {
    return view('reports');
})->name('reports');

/*
|--------------------------------------------------------------------------
| Profile & Settings (Navbar dropdown)
|--------------------------------------------------------------------------
*/
Route::get('/profile', function () {
    return view('profile');
})->name('profile');

Route::put('/profile', function () {
    // placeholder for update logic
    return redirect()->route('profile');
})->name('profile.update');

Route::get('/settings', function () {
    return view('settings');
})->name('settings');

Route::put('/settings', function () {
    // placeholder for update logic
    return redirect()->route('settings');
})->name('settings.update');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.perform');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.perform');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
