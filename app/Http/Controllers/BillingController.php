<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Payment;
use App\Models\PatientVisit;
use App\Services\BillingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class BillingController extends Controller
{
    public function index(): View
    {
        return view('billing.index');
    }

    /**
     * Get bills with filtering and search
     */
    public function getBills(Request $request): JsonResponse
    {
        try {
            $query = Bill::with(['patient', 'visit', 'items.service', 'items.package', 'payments']);
            
            // Filter by patient
            if ($request->filled('patient_id')) {
                $query->where('patient_id', $request->patient_id);
            }
            
            // Filter by visit
            if ($request->filled('visit_id')) {
                $query->where('visit_id', $request->visit_id);
            }
            
            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            
            $bills = $query->latest()->get();
            
            return response()->json([
                'success' => true,
                'data' => $bills
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading bills: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get bill details
     */
    public function getBillDetails($billId): JsonResponse
    {
        try {
            $bill = Bill::with([
                'patient',
                'visit',
                'items.service',
                'items.package',
                'payments.staff'
            ])->findOrFail($billId);
            
            return response()->json([
                'success' => true,
                'data' => $bill
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading bill details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create bill from package
     */
    public function createFromPackage(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'patient_id' => 'required|exists:patients,id',
                'package_id' => 'required|exists:packages,id',
                'notes' => 'nullable|string'
            ]);
            
            // Find or create visit for this patient and package
            $visit = PatientVisit::where('patient_id', $validated['patient_id'])
                ->where('package_id', $validated['package_id'])
                ->first();
            
            if (!$visit) {
                return response()->json([
                    'success' => false,
                    'message' => 'No visit found for this patient and package combination'
                ], 404);
            }
            
            $bill = BillingService::createOrUpdateBillForVisit($visit);
            
            return response()->json([
                'success' => true,
                'message' => 'Bill created successfully',
                'data' => $bill
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating bill: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create bill from services
     */
    public function createFromServices(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'patient_id' => 'required|exists:patients,id',
                'services' => 'required|array',
                'services.*.id' => 'required|exists:services,id',
                'services.*.quantity' => 'required|integer|min:1',
                'notes' => 'nullable|string'
            ]);
            
            // Find visit for this patient with services
            $visit = PatientVisit::where('patient_id', $validated['patient_id'])
                ->whereHas('services')
                ->first();
            
            if (!$visit) {
                return response()->json([
                    'success' => false,
                    'message' => 'No visit found for this patient with services'
                ], 404);
            }
            
            $bill = BillingService::createOrUpdateBillForVisit($visit);
            
            return response()->json([
                'success' => true,
                'message' => 'Bill created successfully',
                'data' => $bill
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating bill: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process payment
     */
    public function processPayment(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'bill_ids' => 'required|array',
                'bill_ids.*' => 'exists:bills,id',
                'amount_paid' => 'required|numeric|min:0.01',
                'payment_method' => 'required|string',
                'received_by' => 'required|string',
                'payment_date' => 'required|date',
                'notes' => 'nullable|string'
            ]);
            
            $totalAmount = 0;
            $bills = Bill::whereIn('id', $validated['bill_ids'])->get();
            
            foreach ($bills as $bill) {
                $totalAmount += $bill->balance;
            }
            
            if ($validated['amount_paid'] > $totalAmount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount cannot exceed total balance due'
                ], 400);
            }
            
            // Process payment for each bill
            $remainingAmount = $validated['amount_paid'];
            
            foreach ($bills as $bill) {
                if ($remainingAmount <= 0) break;
                
                $paymentAmount = min($remainingAmount, $bill->balance);
                
                // Create payment record
                $payment = Payment::create([
                    'patient_id' => $bill->patient_id,
                    'bill_id' => $bill->id,
                    'amount_before' => $bill->amount_paid,
                    'amount_paid' => $paymentAmount,
                    'balance_after' => $bill->amount_paid + $paymentAmount,
                    'payment_method' => $validated['payment_method'],
                    'received_by' => auth()->id(),
                    'payment_date' => $validated['payment_date'],
                    'payment_time' => now()->format('H:i:s'),
                    'notes' => $validated['notes'] ?? null
                ]);
                
                // Update bill
                $newAmountPaid = $bill->amount_paid + $paymentAmount;
                $newBalance = $bill->balance - $paymentAmount;
                $newStatus = $newBalance <= 0 ? 'paid' : ($newAmountPaid > 0 ? 'partial' : 'pending');
                
                $bill->update([
                    'amount_paid' => $newAmountPaid,
                    'balance' => max(0, $newBalance),
                    'status' => $newStatus
                ]);
                
                // Also update the corresponding patient visit
                if ($bill->visit_id) {
                    $visit = $bill->visit;
                    if ($visit) {
                        $visit->update([
                            'amount_paid' => $newAmountPaid,
                            'balance_due' => max(0, $newBalance),
                            'payment_status' => $newStatus
                        ]);
                    }
                }
                
                $remainingAmount -= $paymentAmount;
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'data' => [
                    'total_paid' => $validated['amount_paid'],
                    'bills_updated' => $bills->count()
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus($billId, Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:pending,partial,paid,cancelled'
            ]);
            
            $bill = Bill::findOrFail($billId);
            $bill->update(['status' => $validated['status']]);
            
            return response()->json([
                'success' => true,
                'message' => 'Bill status updated successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating bill status: ' . $e->getMessage()
            ], 500);
        }
    }
}
