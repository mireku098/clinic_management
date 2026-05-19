@extends('layouts.app')

@section('title', 'Patient Billing - ' . $patient->first_name . ' ' . $patient->last_name)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>
                <i class="fas fa-file-invoice-dollar text-success me-2"></i>
                Patient Billing
            </h1>
            <p class="text-muted mb-0">
                <strong>Patient:</strong> {{ $patient->first_name }} {{ $patient->last_name }} ({{ $patient->patient_code }})
            </p>
        </div>
        <div>
            <a href="{{ route('patients') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Patients
            </a>
            <button class="btn btn-primary" onclick="location.reload()">
                <i class="fas fa-sync me-1"></i>Refresh
            </button>
        </div>
    </div>

    <!-- Patient Info Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>Patient Code:</strong><br>
                    {{ $patient->patient_code }}
                </div>
                <div class="col-md-3">
                    <strong>Name:</strong><br>
                    {{ $patient->first_name }} {{ $patient->last_name }}
                </div>
                <div class="col-md-3">
                    <strong>Phone:</strong><br>
                    {{ $patient->phone ?? 'N/A' }}
                </div>
                <div class="col-md-3">
                    <strong>Email:</strong><br>
                    {{ $patient->email ?? 'N/A' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Bills</h5>
                    <h3>{{ $stats['total_bills'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Amount</h5>
                    <h3>${{ number_format($stats['total_amount'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Paid Amount</h5>
                    <h3>${{ number_format($stats['paid_amount'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Outstanding</h5>
                    <h3>${{ number_format($stats['outstanding_amount'], 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Bills Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>
                All Bills
            </h5>
        </div>
        <div class="card-body">
            @if($bills->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Bill #</th>
                                <th>Date</th>
                                <th>Visit</th>
                                <th>Total Amount</th>
                                <th>Paid Amount</th>
                                <th>Balance</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bills as $bill)
                                <tr>
                                    <td>{{ $bill->bill_number ?? 'BILL-' . str_pad($bill->id, 6, '0', STR_PAD_LEFT) }}</td>
                                    <td>{{ $bill->created_at->format('M j, Y') }}</td>
                                    <td>
                                        @if($bill->visit)
                                            <a href="{{ route('visits.edit', $bill->visit->id) }}" class="text-decoration-none">
                                                {{ $bill->visit->visit_code }}
                                            </a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>${{ number_format($bill->total_amount, 2) }}</td>
                                    <td>${{ number_format($bill->payments->sum('amount'), 2) }}</td>
                                    <td>${{ number_format($bill->total_amount - $bill->payments->sum('amount'), 2) }}</td>
                                    <td>
                                        @switch($bill->status)
                                            @case('paid')
                                                <span class="badge bg-success">Paid</span>
                                                @break
                                            @case('pending')
                                                <span class="badge bg-warning">Pending</span>
                                                @break
                                            @case('partial')
                                                <span class="badge bg-info">Partial</span>
                                                @break
                                            @case('overdue')
                                                <span class="badge bg-danger">Overdue</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ $bill->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewBillDetails({{ $bill->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if($bill->status !== 'paid')
                                                <button type="button" class="btn btn-sm btn-outline-success" onclick="recordPayment({{ $bill->id }})">
                                                    <i class="fas fa-money-bill-wave"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Bills Found</h5>
                    <p class="text-muted">This patient doesn't have any bills yet.</p>
                    <a href="{{ route('visits.add') }}?patient_code={{ $patient->patient_code }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Create Visit & Bill
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Bill Details Modal -->
<div class="modal fade" id="billDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bill Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="billDetailsContent">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="paymentForm">
                <div class="modal-body">
                    <input type="hidden" id="paymentBillId">
                    <div class="mb-3">
                        <label for="paymentAmount" class="form-label">Payment Amount</label>
                        <input type="number" step="0.01" class="form-control" id="paymentAmount" required>
                    </div>
                    <div class="mb-3">
                        <label for="paymentMethod" class="form-label">Payment Method</label>
                        <select class="form-select" id="paymentMethod" required>
                            <option value="">Select Method</option>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="check">Check</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="paymentNotes" class="form-label">Notes</label>
                        <textarea class="form-control" id="paymentNotes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Record Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
function viewBillDetails(billId) {
    fetch(`/billing/get-bill-details/${billId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let html = `
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Bill Number:</strong> ${data.bill.bill_number || 'BILL-' + data.bill.id.padStart(6, '0')}<br>
                            <strong>Date:</strong> ${new Date(data.bill.created_at).toLocaleDateString()}<br>
                            <strong>Status:</strong> ${data.bill.status}<br>
                            <strong>Total Amount:</strong> $${parseFloat(data.bill.total_amount).toFixed(2)}
                        </div>
                        <div class="col-md-6">
                            <strong>Paid Amount:</strong> $${parseFloat(data.bill.paid_amount).toFixed(2)}<br>
                            <strong>Balance:</strong> $${(parseFloat(data.bill.total_amount) - parseFloat(data.bill.paid_amount)).toFixed(2)}<br>
                            <strong>Patient:</strong> ${data.bill.patient.first_name} ${data.bill.patient.last_name}
                        </div>
                    </div>
                    <hr>
                    <h6>Bill Items:</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                data.bill.items.forEach(item => {
                    html += `
                        <tr>
                            <td>${item.service ? item.service.name : 'N/A'}</td>
                            <td>${item.quantity}</td>
                            <td>$${parseFloat(item.price).toFixed(2)}</td>
                            <td>$${(parseFloat(item.price) * item.quantity).toFixed(2)}</td>
                        </tr>
                    `;
                });
                
                html += `
                            </tbody>
                        </table>
                    </div>
                `;
                
                if (data.bill.payments && data.bill.payments.length > 0) {
                    html += `
                        <hr>
                        <h6>Payment History:</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    
                    data.bill.payments.forEach(payment => {
                        html += `
                            <tr>
                                <td>${new Date(payment.created_at).toLocaleDateString()}</td>
                                <td>$${parseFloat(payment.amount).toFixed(2)}</td>
                                <td>${payment.method}</td>
                                <td>${payment.notes || '-'}</td>
                            </tr>
                        `;
                    });
                    
                    html += `
                                </tbody>
                            </table>
                        </div>
                    `;
                }
                
                document.getElementById('billDetailsContent').innerHTML = html;
                new bootstrap.Modal(document.getElementById('billDetailsModal')).show();
            } else {
                alert('Error loading bill details');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading bill details');
        });
}

function recordPayment(billId) {
    document.getElementById('paymentBillId').value = billId;
    document.getElementById('paymentForm').reset();
    new bootstrap.Modal(document.getElementById('paymentModal')).show();
}

document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('bill_id', document.getElementById('paymentBillId').value);
    formData.append('amount', document.getElementById('paymentAmount').value);
    formData.append('method', document.getElementById('paymentMethod').value);
    formData.append('notes', document.getElementById('paymentNotes').value);
    
    fetch('/billing/process-payment', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
            Swal.fire({
                icon: 'success',
                title: 'Payment Recorded',
                text: 'Payment has been recorded successfully.',
                timer: 2000,
                showConfirmButton: false
            });
            setTimeout(() => location.reload(), 2000);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Error recording payment'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error recording payment'
        });
    });
});
</script>
@endsection
