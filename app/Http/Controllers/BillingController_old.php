<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\PatientVisit;
use App\Models\Payment;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class BillingController extends Controller
{
    /**
     * Display billing page with visit-centric billing data
     */
    public function index(): View
    {
        $visits = PatientVisit::with(['patient', 'package'])
            ->latest('visit_date')
            ->get();
            
        return view('billing.index', compact('visits'));
    }
    
    /**
     * Process payment for visit
     */
    public function processPayment(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            
            $validated = $request->validate([
                'visit_id' => 'required|exists:patient_visits,id',
                'amount_paid' => 'required|numeric|min:0.01',
                'payment_method' => 'required|string|in:cash,card,bank_transfer,insurance',
                'received_by' => 'required|exists:users,id'
            ]);
            
            $visit = PatientVisit::findOrFail($validated['visit_id']);
            $amountPaid = $validated['amount_paid'];
            
            // Validate payment amount doesn't exceed balance
            if ($amountPaid > $visit->balance_due) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount cannot exceed balance due'
                ], 422);
            }
            
            $newAmountPaid = $visit->amount_paid + $amountPaid;
            $newBalance = $visit->balance_due - $amountPaid;
            $newStatus = $newBalance <= 0 ? 'paid' : 'partial';
            
            // Create payment record
            Payment::create([
                'patient_id' => $visit->patient_id,
                'bill_id' => null, // Not using separate bills table
                'amount_before' => $visit->balance_due,
                'amount_paid' => $amountPaid,
                'balance_after' => $newBalance,
                'payment_method' => $validated['payment_method'],
                'received_by' => $validated['received_by'],
                'payment_date' => now()->format('Y-m-d'),
                'payment_time' => now()->format('H:i:s')
            ]);
            
            // Update visit payment status
            $visit->update([
                'amount_paid' => $newAmountPaid,
                'balance_due' => $newBalance,
                'payment_status' => $newStatus
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'data' => [
                    'amount_paid' => $amountPaid,
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

    /**
     * Get visits with filtering and search for billing
     */
    public function getVisits(Request $request): JsonResponse
    {
        try {
            $query = PatientVisit::with(['patient', 'package']);
            
            // Filter by patient
            if ($request->filled('patient_id')) {
                $query->where('patient_id', $request->patient_id);
            }
            
            // Filter by payment status
            if ($request->filled('payment_status')) {
                $query->where('payment_status', $request->payment_status);
            }
            
            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('visit_date', '>=', $request->date_from);
            }
            
            if ($request->filled('date_to')) {
                $query->whereDate('visit_date', '<=', $request->date_to);
            }
            
            // Only show visits with billing data (have total_amount > 0)
            $query->where('total_amount', '>', 0);
            
            $visits = $query->latest('visit_date')->get();
            
            return response()->json([
                'success' => true,
                'data' => $visits
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching visits: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get visit details with billing breakdown
     */
    public function getVisitDetails($visitId): JsonResponse
    {
        try {
            $visit = PatientVisit::with([
                'patient', 
                'package.services.service',
                'payments.staff'
            ])->findOrFail($visitId);
            
            return response()->json([
                'success' => true,
                'data' => $visit
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching visit details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update visit payment status (for manual adjustments)
     */
    public function updatePaymentStatusVisit($visitId, Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'payment_status' => 'required|in:pending,partial,paid',
                'amount_paid' => 'nullable|numeric|min:0',
                'balance_due' => 'nullable|numeric|min:0'
            ]);
            
            $visit = PatientVisit::findOrFail($visitId);
            
            $updateData = ['payment_status' => $validated['payment_status']];
            
            if (isset($validated['amount_paid'])) {
                $updateData['amount_paid'] = $validated['amount_paid'];
            }
            
            if (isset($validated['balance_due'])) {
                $updateData['balance_due'] = $validated['balance_due'];
            }
            
            $visit->update($updateData);
            
            return response()->json([
                'success' => true,
                'message' => 'Payment status updated successfully',
                'data' => $visit->fresh()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating payment status: ' . $e->getMessage()
            ], 500);
        }
    }
}
