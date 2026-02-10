<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\PatientVisit;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Payment;
use App\Models\PatientService;
use App\Models\PatientPackage;
use App\Models\Service;
use App\Models\Package;
use Illuminate\Support\Facades\Log;

class PatientContextService
{
    /**
     * Get comprehensive patient context data
     */
    public static function getPatientContextData(string $patientCode): array
    {
        try {
            $patient = Patient::where('patient_code', $patientCode)
                ->with([
                    'visits' => function($query) {
                        $query->orderBy('visit_date', 'desc')
                              ->with(['services.service', 'package', 'bill']);
                    },
                    'bills' => function($query) {
                        $query->orderBy('created_at', 'desc')
                              ->with(['items.service', 'items.package', 'payments']);
                    }
                ])
                ->firstOrFail();

            return [
                'patient' => $patient,
                'overview' => self::getOverviewData($patient),
                'vitals' => self::getVitalsData($patient),
                'medicalHistory' => self::getMedicalHistoryData($patient),
                'billing' => self::getBillingData($patient),
            ];
        } catch (\Exception $e) {
            Log::error("Error loading patient context for {$patientCode}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get overview statistics
     */
    private static function getOverviewData(Patient $patient): array
    {
        $visits = $patient->visits;
        $bills = $patient->bills;

        $totalVisits = $visits->count();
        $lastVisit = $visits->first();
        $activeServices = $visits->flatMap(function($visit) {
            return $visit->services->pluck('service');
        })->unique('id');

        $activePackages = $visits->pluck('package')->filter()->unique('id');
        
        $outstandingBalance = $bills->sum('balance');
        $totalBilled = $bills->sum('total_amount');
        $totalPaid = $bills->sum('amount_paid');

        return [
            'total_visits' => $totalVisits,
            'last_visit_date' => $lastVisit ? $lastVisit->visit_date->format('M d, Y') : 'No visits',
            'last_visit_type' => $lastVisit ? $lastVisit->visit_type : null,
            'active_services_count' => $activeServices->count(),
            'active_packages_count' => $activePackages->count(),
            'active_services' => $activeServices->take(5),
            'active_packages' => $activePackages->take(3),
            'outstanding_balance' => number_format($outstandingBalance, 2),
            'total_billed' => number_format($totalBilled, 2),
            'total_paid' => number_format($totalPaid, 2),
            'payment_status' => $outstandingBalance > 0 ? 'pending' : 'paid',
        ];
    }

    /**
     * Get vitals data
     */
    private static function getVitalsData(Patient $patient): array
    {
        $vitals = $patient->visits()
            ->whereNotNull('temperature')
            ->orWhereNotNull('blood_pressure')
            ->orWhereNotNull('heart_rate')
            ->orWhereNotNull('oxygen_saturation')
            ->orWhereNotNull('respiratory_rate')
            ->orWhereNotNull('weight')
            ->orWhereNotNull('height')
            ->orderBy('visit_date', 'desc')
            ->orderBy('visit_time', 'desc')
            ->get()
            ->map(function($visit) {
                return [
                    'visit_id' => $visit->id,
                    'visit_date' => $visit->visit_date->format('M d, Y'),
                    'visit_time' => $visit->visit_time ? $visit->visit_time->format('H:i') : null,
                    'temperature' => $visit->temperature ? number_format($visit->temperature, 1) . '°C' : null,
                    'blood_pressure' => $visit->blood_pressure,
                    'heart_rate' => $visit->heart_rate ? $visit->heart_rate . ' bpm' : null,
                    'oxygen_saturation' => $visit->oxygen_saturation ? $visit->oxygen_saturation . '%' : null,
                    'respiratory_rate' => $visit->respiratory_rate ? $visit->respiratory_rate . '/min' : null,
                    'weight' => $visit->weight ? number_format($visit->weight, 1) . ' kg' : null,
                    'height' => $visit->height ? number_format($visit->height, 1) . ' cm' : null,
                    'bmi' => $visit->bmi ? number_format($visit->bmi, 1) : null,
                ];
            });

        return [
            'vitals' => $vitals,
            'has_vitals' => $vitals->isNotEmpty(),
            'latest_vitals' => $vitals->first(),
        ];
    }

    /**
     * Get medical history data
     */
    private static function getMedicalHistoryData(Patient $patient): array
    {
        $visits = $patient->visits()
            ->with(['services.service', 'package'])
            ->orderBy('visit_date', 'desc')
            ->get();

        $medicalHistory = $visits->map(function($visit) {
            return [
                'visit_id' => $visit->id,
                'visit_date' => $visit->visit_date->format('M d, Y'),
                'chief_complaint' => $visit->chief_complaint,
                'history_present_illness' => $visit->history_present_illness,
                'assessment' => $visit->assessment,
                'treatment_plan' => $visit->treatment_plan,
                'practitioner' => $visit->practitioner,
                'department' => $visit->department,
                'services' => $visit->services->map(function($service) {
                    return [
                        'id' => $service->service_id,
                        'name' => $service->service->service_name,
                        'price' => number_format($service->service_price, 2),
                    ];
                }),
                'package' => $visit->package ? [
                    'id' => $visit->package->id,
                    'name' => $visit->package->package_name,
                    'price' => number_format($visit->package->total_cost, 2),
                ] : null,
                'notes' => $visit->notes,
            ];
        });

        return [
            'medical_history' => $medicalHistory,
            'has_history' => $medicalHistory->isNotEmpty(),
            'total_visits' => $medicalHistory->count(),
        ];
    }

    /**
     * Get billing data
     */
    private static function getBillingData(Patient $patient): array
    {
        $bills = $patient->bills()->with(['items.service', 'items.package', 'payments'])->get();

        $billingData = $bills->map(function($bill) {
            return [
                'bill_id' => $bill->id,
                'bill_type' => $bill->bill_type,
                'total_amount' => number_format($bill->total_amount, 2),
                'amount_paid' => number_format($bill->amount_paid, 2),
                'balance' => number_format($bill->balance, 2),
                'status' => $bill->status,
                'created_at' => $bill->created_at->format('M d, Y'),
                'items' => $bill->items->map(function($item) {
                    return [
                        'description' => $item->description,
                        'quantity' => $item->quantity,
                        'unit_price' => number_format($item->unit_price, 2),
                        'total_price' => number_format($item->total_price, 2),
                        'item_type' => $item->item_type,
                    ];
                }),
                'payments' => $bill->payments->map(function($payment) {
                    return [
                        'payment_id' => $payment->id,
                        'amount_paid' => number_format($payment->amount_paid, 2),
                        'payment_method' => $payment->payment_method,
                        'payment_date' => $payment->payment_date->format('M d, Y'),
                        'received_by' => $payment->received_by,
                        'notes' => $payment->notes,
                    ];
                }),
            ];
        });

        $totalBilled = $bills->sum('total_amount');
        $totalPaid = $bills->sum('amount_paid');
        $outstandingBalance = $bills->sum('balance');

        return [
            'bills' => $billingData,
            'has_bills' => $billingData->isNotEmpty(),
            'total_billed' => number_format($totalBilled, 2),
            'total_paid' => number_format($totalPaid, 2),
            'outstanding_balance' => number_format($outstandingBalance, 2),
            'payment_summary' => [
                'paid_bills' => $bills->where('status', 'paid')->count(),
                'partial_bills' => $bills->where('status', 'partial')->count(),
                'pending_bills' => $bills->where('status', 'pending')->count(),
            ],
        ];
    }
}
