<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientVisit;
use App\Models\Bill;
use App\Models\Package;
use App\Models\Appointment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get today's date
        $today = Carbon::today();
        
        // Total Patients
        $totalPatients = Patient::count();
        
        // Today's Visits
        $todayVisits = PatientVisit::whereDate('visit_date', $today)->count();
        
        // Active Packages (packages with status 'active')
        $activePackages = Package::where('status', 'active')->count();
        
        // Today's Revenue (sum of paid amounts from bills created today)
        $todayRevenue = Bill::whereDate('created_at', $today)
            ->where('status', 'paid')
            ->sum('amount_paid');
        
        // Recent Activity (last 5 activities)
        $recentActivity = $this->getRecentActivity();
        
        // Upcoming Appointments (next 5 appointments)
        $upcomingAppointments = $this->getUpcomingAppointments();
        
        return view('dashboard', compact(
            'totalPatients',
            'todayVisits', 
            'activePackages',
            'todayRevenue',
            'recentActivity',
            'upcomingAppointments'
        ));
    }
    
    private function getRecentActivity()
    {
        $activities = [];
        
        // Get recent patient registrations (last 3)
        $recentPatients = Patient::latest('created_at')
            ->limit(3)
            ->get(['id', 'first_name', 'last_name', 'created_at']);
            
        foreach ($recentPatients as $patient) {
            if ($patient->created_at) {
                $activities[] = [
                    'type' => 'patient_registered',
                    'icon' => 'fas fa-user-plus text-primary',
                    'description' => "New patient registered: {$patient->first_name} {$patient->last_name}",
                    'time' => $patient->created_at->diffForHumans(),
                    'timestamp' => $patient->created_at
                ];
            }
        }
        
        // Get recent visits (last 3)
        $recentVisits = PatientVisit::with('patient')
            ->latest('created_at')
            ->limit(3)
            ->get();
            
        foreach ($recentVisits as $visit) {
            if ($visit->created_at && $visit->patient) {
                $activities[] = [
                    'type' => 'visit_completed',
                    'icon' => 'fas fa-calendar-check text-success',
                    'description' => "Visit completed: {$visit->patient->first_name} {$visit->patient->last_name}",
                    'time' => $visit->created_at->diffForHumans(),
                    'timestamp' => $visit->created_at
                ];
            }
        }
        
        // Get recent payments (last 3)
        $recentPayments = Bill::with('patient')
            ->where('status', 'paid')
            ->latest('updated_at')
            ->limit(3)
            ->get();
            
        foreach ($recentPayments as $payment) {
            if ($payment->updated_at && $payment->patient) {
                $activities[] = [
                    'type' => 'payment_received',
                    'icon' => 'fas fa-money-bill-wave text-success',
                    'description' => "Payment received: GH₵{$payment->amount_paid} from {$payment->patient->first_name} {$payment->patient->last_name}",
                    'time' => $payment->updated_at->diffForHumans(),
                    'timestamp' => $payment->updated_at
                ];
            }
        }
        
        // Sort by timestamp and limit to 5 most recent
        usort($activities, function($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });
        
        return array_slice($activities, 0, 5);
    }
    
    private function getUpcomingAppointments()
    {
        // For now, return upcoming visits as appointments
        // In a full implementation, you'd use the Appointment model
        return PatientVisit::with('patient')
            ->whereDate('visit_date', '>=', Carbon::today())
            ->orderBy('visit_date')
            ->orderBy('visit_time')
            ->limit(5)
            ->get()
            ->filter(function($visit) {
                return $visit->patient && $visit->visit_date;
            })
            ->map(function($visit) {
                return [
                    'patient_name' => $visit->patient->first_name . ' ' . $visit->patient->last_name,
                    'service' => $visit->visit_type ?? 'General Consultation',
                    'date' => $visit->visit_date->format('M d, Y'),
                    'time' => $visit->visit_time ? $visit->visit_time->format('h:i A') : 'TBD',
                    'status' => $visit->visit_date->isToday() ? 'Scheduled' : 'Confirmed'
                ];
            });
    }
}
