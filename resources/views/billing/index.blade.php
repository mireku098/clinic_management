@extends('layouts.app')

@section('title', 'Billing Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">
                <i class="fas fa-file-invoice-dollar text-success me-2"></i>
                Billing Management
            </h4>
            <p class="text-muted mb-0 mt-1">Manage patient bills and payments</p>
        </div>
        <div>
            <button class="btn btn-primary" onclick="loadBills()">
                <i class="fas fa-sync me-1"></i>Refresh
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-filter text-primary me-2"></i>
                Filters
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="filterPatient" class="form-label">Patient</label>
                    <select class="form-select" id="filterPatient">
                        <option value="">All Patients</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterBill" class="form-label">Bill</label>
                    <select class="form-select" id="filterBill">
                        <option value="">All Bills</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterStatus" class="form-label">Status</label>
                    <select class="form-select" id="filterStatus">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="partial">Partial</option>
                        <option value="paid">Paid</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterDateFrom" class="form-label">Date From</label>
                    <input type="date" class="form-control" id="filterDateFrom">
                </div>
                <div class="col-md-3">
                    <label for="filterDateTo" class="form-label">Date To</label>
                    <input type="date" class="form-control" id="filterDateTo">
                </div>
                <div class="col-md-3">
                    <label for="searchBill" class="form-label">Search</label>
                    <input type="text" class="form-control" id="searchBill" placeholder="Search bills...">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-outline-secondary me-2" onclick="clearFilters()">
                        <i class="fas fa-times me-1"></i>Clear
                    </button>
                    <button class="btn btn-success" onclick="applyFilters()">
                        <i class="fas fa-search me-1"></i>Apply
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bills Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list text-info me-2"></i>
                Bills
                <span class="badge bg-primary ms-2" id="billCount">0</span>
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="billsTable">
                    <thead>
                        <tr>
                            <th>Bill ID</th>
                            <th>Patient</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Total Amount</th>
                            <th>Paid</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="billsTableBody">
                        <!-- Bills will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-money-bill-wave me-2"></i>
                    Process Payment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="paymentForm">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Selected Bills</label>
                            <div id="selectedBillsList" class="border rounded p-3 mb-3">
                                <!-- Selected bills will be shown here -->
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="paymentAmount" class="form-label">Amount to Pay *</label>
                                <input type="number" class="form-control" id="paymentAmount" step="0.01" min="0.01" required>
                                <small class="text-muted">Enter amount to be paid</small>
                            </div>
                            <div class="col-md-4">
                                <label for="paymentMethod" class="form-label">Payment Method *</label>
                                <select class="form-select" id="paymentMethod" required>
                                    <option value="">Select Method</option>
                                    <option value="cash">💵 Cash</option>
                                    <option value="card">💳 Credit/Debit Card</option>
                                    <option value="bank_transfer">🏦 Bank Transfer</option>
                                    <option value="insurance">🛡️ Insurance</option>
                                    <option value="mobile_money">📱 Mobile Money</option>
                                    <option value="check">📋 Check</option>
                                    <option value="other">📝 Other</option>
                                </select>
                                <small class="text-muted">Select payment method</small>
                            </div>
                            <div class="col-md-4">
                                <label for="receivedBy" class="form-label">Received By *</label>
                                <input type="hidden" class="form-control" id="receivedBy" value="{{ auth()->user()->id ?? '' }}" required>
                                <input type="text" class="form-control" value="{{ auth()->user()->name ?? '' }}" disabled>
                                <small class="text-muted">Name of staff receiving payment (auto-populated)</small>
                            </div>
                            <div class="col-md-6">
                                <label for="paymentDate" class="form-label">Payment Date *</label>
                                <input type="date" class="form-control" id="paymentDate" required>
                                <small class="text-muted">Date of payment</small>
                            </div>
                            <div class="col-12">
                                <label for="paymentNotes" class="form-label">Payment Notes</label>
                                <textarea class="form-control" id="paymentNotes" rows="2" placeholder="Enter any additional notes about this payment"></textarea>
                                <small class="text-muted">Optional: Add any relevant notes</small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="processPayment()">Process Payment</button>
            </div>
        </div>
    </div>
</div>

<!-- Bill Details Modal -->
<div class="modal fade" id="billDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-invoice me-2"></i>
                    Bill Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="billDetailsContent">
                    <!-- Bill details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Alert Container -->
<div id="alert-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1050"></div>
@endsection

@section('css')
<style>
    .modal-lg {
        max-width: 800px;
    }
    
    .service-row {
        border: 1px solid #e2e8f0;
        border-radius: 0.375rem;
        padding: 1rem;
        margin-bottom: 0.5rem;
    }
    
    .selected-bill {
        background-color: #e7f5ff;
        border: 1px solid #0056b3;
    }
    
    .badge-pending { background-color: #ffc107; }
    .badge-partial { background-color: #17a2b8; }
    .badge-paid { background-color: #28a745; }
</style>
@endsection

@section('js')
<script>
let bills = [];
let selectedBills = [];
let patients = [];
let packages = [];
let services = [];

// Simple alert function
function showAlert(message, type = 'info') {
    let alertContainer = document.getElementById('alert-container');
    if (!alertContainer) {
        alertContainer = document.createElement('div');
        alertContainer.id = 'alert-container';
        alertContainer.className = 'position-fixed top-0 end-0 p-3';
        alertContainer.style.zIndex = '1050';
        document.body.appendChild(alertContainer);
    }
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show mb-2`;
    alert.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
            <div class="flex-grow-1">${message}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    alertContainer.appendChild(alert);
    
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}

// Load initial data
document.addEventListener('DOMContentLoaded', function() {
    loadBills();
    loadPatients();
    loadServices();
    loadPackages();
    
    // Set today's date as default
    document.getElementById('paymentDate').valueAsDate = new Date();
});

// Load bills
function loadBills() {
    fetch('/billing/get-bills')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bills = data.data;
                displayBills();
                updateBillCount();
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            showAlert('Error loading bills: ' + error.message, 'danger');
        });
}

// Load patients
function loadPatients() {
    fetch('/api/patients')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                patients = data.data;
                populatePatientSelect();
            }
        });
}

// Load services
function loadServices() {
    fetch('/api/services')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                services = data.data;
            }
        });
}

// Load packages
function loadPackages() {
    fetch('/packages')
        .then(response => response.text())
        .then(html => {
            // Extract packages from HTML (simplified approach)
            // In a real implementation, you'd have a dedicated API endpoint
        });
}

// Populate patient select
function populatePatientSelect() {
    const select = document.getElementById('filterPatient');
    
    // Clear existing options except the first one
    while (select.children.length > 1) {
        select.removeChild(select.lastChild);
    }
    
    patients.forEach(patient => {
        const option = new Option(`${patient.first_name} ${patient.last_name} (${patient.patient_code})`, patient.id);
        select.add(option);
    });
}

// Display bills
function displayBills() {
    const tbody = document.getElementById('billsTableBody');
    tbody.innerHTML = '';
    
    bills.forEach(bill => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>#${bill.id}</td>
            <td>${bill.patient ? bill.patient.first_name + ' ' + bill.patient.last_name : 'N/A'}</td>
            <td>${new Date(bill.created_at).toLocaleDateString()}</td>
            <td><span class="badge bg-${bill.bill_type === 'package' ? 'primary' : 'info'}">${bill.bill_type.toUpperCase()}</span></td>
            <td>GH₵${parseFloat(bill.total_amount).toFixed(2)}</td>
            <td>GH₵${parseFloat(bill.amount_paid).toFixed(2)}</td>
            <td>GH₵${parseFloat(bill.balance).toFixed(2)}</td>
            <td><span class="badge badge-${bill.status}">${bill.status.toUpperCase()}</span></td>
            <td>
                <button class="btn btn-sm btn-outline-info me-1" onclick="viewBillDetails(${bill.id})">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-success" onclick="openPaymentModal([${bill.id}])" ${bill.balance <= 0 ? 'disabled' : ''}>
                    <i class="fas fa-money-bill-wave"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Update bill count
function updateBillCount() {
    document.getElementById('billCount').textContent = bills.length;
}

// View bill details
function viewBillDetails(billId) {
    fetch(`/billing/get-bill-details/${billId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayBillDetails(data.data);
                const modal = new bootstrap.Modal(document.getElementById('billDetailsModal'));
                modal.show();
            } else {
                showAlert(data.message, 'danger');
            }
        });
}

// Display bill details
function displayBillDetails(bill) {
    const content = document.getElementById('billDetailsContent');
    
    let itemsHtml = '';
    if (bill.items && bill.items.length > 0) {
        itemsHtml = bill.items.map(item => `
            <tr>
                <td>${item.service ? item.service.service_name : (item.package ? item.package.package_name : 'N/A')}</td>
                <td>${item.description}</td>
                <td>${item.quantity}</td>
                <td>GH₵${parseFloat(item.unit_price).toFixed(2)}</td>
                <td>GH₵${parseFloat(item.total_price).toFixed(2)}</td>
            </tr>
        `).join('');
    }
    
    let paymentsHtml = '';
    if (bill.payments && bill.payments.length > 0) {
        paymentsHtml = bill.payments.map(payment => `
            <tr>
                <td>${new Date(payment.payment_date).toLocaleDateString()}</td>
                <td>${payment.payment_method}</td>
                <td>GH₵${parseFloat(payment.amount_paid).toFixed(2)}</td>
                <td>${payment.staff ? payment.staff.name : 'N/A'}</td>
            </tr>
        `).join('');
    }
    
    content.innerHTML = `
        <div class="row mb-3">
            <div class="col-md-6">
                <strong>Bill ID:</strong> #${bill.id}
            </div>
            <div class="col-md-6">
                <strong>Type:</strong> <span class="badge bg-primary">${bill.bill_type.toUpperCase()}</span>
            </div>
            <div class="col-md-6">
                <strong>Status:</strong> <span class="badge badge-${bill.status}">${bill.status.toUpperCase()}</span>
            </div>
            <div class="col-md-6">
                <strong>Date:</strong> ${new Date(bill.created_at).toLocaleDateString()}
            </div>
        </div>
        
        <h6 class="mb-3">Bill Items</h6>
        <div class="table-responsive mb-4">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Service/Package</th>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    ${itemsHtml}
                </tbody>
            </table>
        </div>
        
        <h6 class="mb-3">Payment History</h6>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Method</th>
                        <th>Amount</th>
                        <th>Received By</th>
                    </tr>
                </thead>
                <tbody>
                    ${paymentsHtml}
                </tbody>
            </table>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <strong>Total Amount:</strong> GH₵${parseFloat(bill.total_amount).toFixed(2)}
            </div>
            <div class="col-md-4">
                <strong>Amount Paid:</strong> GH₵${parseFloat(bill.amount_paid).toFixed(2)}
            </div>
            <div class="col-md-4">
                <strong>Balance:</strong> GH₵${parseFloat(bill.balance).toFixed(2)}
            </div>
        </div>
    `;
}

// Open payment modal
function openPaymentModal(billIds) {
    selectedBills = billIds;
    displaySelectedBills();
    const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
    modal.show();
}

// Display selected bills
function displaySelectedBills() {
    const container = document.getElementById('selectedBillsList');
    let totalAmount = 0;
    
    let html = selectedBills.map(billId => {
        const bill = bills.find(b => b.id === billId);
        if (bill) {
            totalAmount += parseFloat(bill.balance);
            return `
                <div class="selected-bill p-2 mb-2">
                    <div class="d-flex justify-content-between">
                        <span>Bill #${bill.id} (${bill.patient ? bill.patient.first_name + ' ' + bill.patient.last_name : 'N/A'})</span>
                        <span>Balance: GH₵${parseFloat(bill.balance).toFixed(2)}</span>
                    </div>
                </div>
            `;
        }
        return '';
    }).join('');
    
    container.innerHTML = html;
    document.getElementById('paymentAmount').value = totalAmount.toFixed(2);
}

// Process payment
function processPayment() {
    if (!selectedBills || selectedBills.length === 0) {
        showAlert('No bills selected for payment', 'danger');
        return;
    }
    
    const paymentData = {
        bill_ids: selectedBills,
        amount_paid: parseFloat(document.getElementById('paymentAmount').value),
        payment_method: document.getElementById('paymentMethod').value,
        received_by: document.getElementById('receivedBy').value,
        payment_date: document.getElementById('paymentDate').value,
        notes: document.getElementById('paymentNotes').value
    };
    
    // Validation
    if (!paymentData.amount_paid || paymentData.amount_paid <= 0) {
        showAlert('Please enter a valid payment amount', 'danger');
        return;
    }
    
    if (!paymentData.payment_method || !paymentData.received_by || !paymentData.payment_date) {
        showAlert('Please fill in all required payment details', 'danger');
        return;
    }
    
    fetch('/billing/process-payment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(paymentData)
    })
    .then(response => response.json())
    .then(data => {
            if (data.success) {
                showAlert(`Payment processed successfully! GH₵${data.data.total_paid} paid for ${data.data.bills_updated} bill(s).`, 'success');
                bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
                document.getElementById('paymentForm').reset();
                loadBills();
            } else {
                showAlert(data.message, 'danger');
            }
        })
    .catch(error => {
        showAlert('Error processing payment: ' + error.message, 'danger');
    });
}

// Apply filters
function applyFilters() {
    const filters = {
        patient_id: document.getElementById('filterPatient').value,
        status: document.getElementById('filterStatus').value,
        date_from: document.getElementById('filterDateFrom').value,
        date_to: document.getElementById('filterDateTo').value
    };
    
    const queryString = new URLSearchParams(filters).toString();
    
    fetch(`/billing/get-bills?${queryString}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bills = data.data;
                displayBills();
                updateBillCount();
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            showAlert('Error applying filters: ' + error.message, 'danger');
        });
}

// Clear filters
function clearFilters() {
    document.getElementById('filterPatient').value = '';
    document.getElementById('filterStatus').value = '';
    document.getElementById('filterDateFrom').value = '';
    document.getElementById('filterDateTo').value = '';
    
    loadBills();
}
</script>
@endsection
