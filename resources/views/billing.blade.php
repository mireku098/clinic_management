@extends('layouts.app')
@section('title', 'Billing')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
      <h1>Billing (Admin)</h1>
      <p class="text-muted">Global billing overview and administrative reporting</p>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-primary">
            <i class="fas fa-file-invoice"></i>
          </div>
          <div class="stat-details">
            <h3>342</h3>
            <p>Total Bills</p>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-success">
            <i class="fas fa-dollar-sign"></i>
          </div>
          <div class="stat-details">
            <h3>$125,450</h3>
            <p>Total Revenue</p>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-warning">
            <i class="fas fa-exclamation-triangle"></i>
          </div>
          <div class="stat-details">
            <h3>$45,780</h3>
            <p>Outstanding Balance</p>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-info">
            <i class="fas fa-percentage"></i>
          </div>
          <div class="stat-details">
            <h3>63.5%</h3>
            <p>Collection Rate</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Advanced Filters -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="card-title mb-0">
          <i class="fas fa-filter me-2"></i>Filters & Search
        </h5>
      </div>
      <div class="card-body">
        <form id="billingFiltersForm" class="row g-3">
          <div class="col-md-3">
            <label for="patient_filter" class="form-label">Patient</label>
            <input type="text" class="form-control" id="patient_filter" placeholder="Search by name or ID..." />
          </div>
          <div class="col-md-2">
            <label for="status_filter" class="form-label">Status</label>
            <select class="form-select" id="status_filter">
              <option value="">All Status</option>
              <option value="paid">Paid</option>
              <option value="unpaid">Unpaid</option>
              <option value="partial">Partially Paid</option>
            </select>
          </div>
          <div class="col-md-2">
            <label for="bill_type_filter" class="form-label">Bill Type</label>
            <select class="form-select" id="bill_type_filter">
              <option value="">All Types</option>
              <option value="package">Package</option>
              <option value="service">Service</option>
              <option value="combined">Combined</option>
            </select>
          </div>
          <div class="col-md-2">
            <label for="date_from_filter" class="form-label">Date From</label>
            <input type="date" class="form-control" id="date_from_filter" />
          </div>
          <div class="col-md-2">
            <label for="date_to_filter" class="form-label">Date To</label>
            <input type="date" class="form-control" id="date_to_filter" />
          </div>
          <div class="col-md-1 d-flex align-items-end">
            <button type="button" class="btn btn-primary w-100" onclick="applyBillingFilters()">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Global Bills Table -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
          <i class="fas fa-list me-2"></i>All Bills (Administrative View)
        </h5>
        <div class="d-flex gap-2">
          <input type="text" class="form-control" placeholder="Quick search..." style="width: 200px" id="quickSearchInput" onkeyup="quickSearchBills()" />
          <button class="btn btn-outline-secondary btn-sm" onclick="clearFilters()">
            <i class="fas fa-refresh me-1"></i>Clear
          </button>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover" id="globalBillsTable">
            <thead>
              <tr>
                <th>Bill ID</th>
                <th>Patient</th>
                <th>Bill Type</th>
                <th>Total Amount</th>
                <th>Amount Paid</th>
                <th>Balance</th>
                <th>Status</th>
                <th>Bill Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="billsTableBody">
              <tr>
                <td><span class="badge bg-primary">B001</span></td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="patient-avatar me-2">
                      <i class="fas fa-user"></i>
                    </div>
                    <div>
                      <div class="fw-bold">John Doe</div>
                      <small class="text-muted">P001</small>
                    </div>
                  </div>
                </td>
                <td><span class="badge bg-info">Package</span></td>
                <td class="fw-bold text-primary">$500.00</td>
                <td class="text-success">$200.00</td>
                <td class="text-warning fw-bold">$300.00</td>
                <td><span class="badge bg-warning">Partially Paid</span></td>
                <td>2024-01-25</td>
                <td>
                  <button class="btn btn-sm btn-outline-primary" onclick="viewBillDetails('B001')" title="View Details">
                    <i class="fas fa-eye"></i>
                  </button>
                </td>
              </tr>
              <tr>
                <td><span class="badge bg-primary">B002</span></td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="patient-avatar me-2">
                      <i class="fas fa-user"></i>
                    </div>
                    <div>
                      <div class="fw-bold">Jane Smith</div>
                      <small class="text-muted">P002</small>
                    </div>
                  </div>
                </td>
                <td><span class="badge bg-secondary">Service</span></td>
                <td class="fw-bold text-primary">$150.00</td>
                <td class="text-success">$150.00</td>
                <td class="text-success fw-bold">$0.00</td>
                <td><span class="badge bg-success">Fully Paid</span></td>
                <td>2024-01-24</td>
                <td>
                  <button class="btn btn-sm btn-outline-primary" onclick="viewBillDetails('B002')" title="View Details">
                    <i class="fas fa-eye"></i>
                  </button>
                </td>
              </tr>
              <tr>
                <td><span class="badge bg-primary">B003</span></td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="patient-avatar me-2">
                      <i class="fas fa-user"></i>
                    </div>
                    <div>
                      <div class="fw-bold">Michael Johnson</div>
                      <small class="text-muted">P003</small>
                    </div>
                  </div>
                </td>
                <td><span class="badge bg-warning">Combined</span></td>
                <td class="fw-bold text-primary">$750.00</td>
                <td class="text-success">$0.00</td>
                <td class="text-danger fw-bold">$750.00</td>
                <td><span class="badge bg-danger">Unpaid</span></td>
                <td>2024-01-23</td>
                <td>
                  <button class="btn btn-sm btn-outline-primary" onclick="viewBillDetails('B003')" title="View Details">
                    <i class="fas fa-eye"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
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
      <div class="modal-body">
        <div class="row mb-3">
          <div class="col-md-6">
            <strong>Bill ID:</strong> <span id="modalBillId"></span>
          </div>
          <div class="col-md-6">
            <strong>Status:</strong> <span id="modalBillStatus"></span>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-md-6">
            <strong>Patient:</strong> <span id="modalBillPatient"></span>
          </div>
          <div class="col-md-6">
            <strong>Bill Type:</strong> <span id="modalBillType"></span>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-md-6">
            <strong>Bill Date:</strong> <span id="modalBillDate"></span>
          </div>
          <div class="col-md-6">
            <strong>Due Date:</strong> <span id="modalDueDate"></span>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-md-6">
            <strong>Total Amount:</strong> <span id="modalTotalAmount"></span>
          </div>
          <div class="col-md-6">
            <strong>Amount Paid:</strong> <span id="modalAmountPaid"></span>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-md-12">
            <strong>Balance:</strong> <span id="modalBalance"></span>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-md-12">
            <strong>Notes:</strong> <span id="modalNotes"></span>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="printBillDetails()">
          <i class="fas fa-print me-1"></i>Print Bill
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Alert Container -->
<div id="alert_container" class="position-fixed top-0 end-0 p-3" style="z-index: 1050"></div>
@endsection

@section('js')
<script>
  // Mock global bills data
  const globalBillsData = {
    B001: {
      patient: 'John Doe (P001)',
      type: 'Package',
      date: '2024-01-25',
      totalAmount: 500.00,
      amountPaid: 200.00,
      balance: 300.00,
      status: 'Partially Paid',
      dueDate: '2024-02-25',
      notes: 'Initial consultation and lab tests package'
    },
    B002: {
      patient: 'Jane Smith (P002)',
      type: 'Service',
      date: '2024-01-24',
      totalAmount: 150.00,
      amountPaid: 150.00,
      balance: 0.00,
      status: 'Fully Paid',
      dueDate: '2024-02-23',
      notes: 'Physiotherapy session'
    },
    B003: {
      patient: 'Michael Johnson (P003)',
      type: 'Combined',
      date: '2024-01-23',
      totalAmount: 750.00,
      amountPaid: 0.00,
      balance: 750.00,
      status: 'Unpaid',
      dueDate: '2024-02-22',
      notes: 'Emergency room visit and medications'
    }
  };

  // View bill details (read-only)
  function viewBillDetails(billId) {
    const bill = globalBillsData[billId];
    if (bill) {
      document.getElementById('modalBillId').textContent = billId;
      document.getElementById('modalBillPatient').textContent = bill.patient;
      document.getElementById('modalBillType').textContent = bill.type;
      document.getElementById('modalBillDate').textContent = bill.date;
      document.getElementById('modalDueDate').textContent = bill.dueDate;
      document.getElementById('modalTotalAmount').textContent = `$${bill.totalAmount.toFixed(2)}`;
      document.getElementById('modalAmountPaid').textContent = `$${bill.amountPaid.toFixed(2)}`;
      document.getElementById('modalBalance').textContent = `$${bill.balance.toFixed(2)}`;
      document.getElementById('modalNotes').textContent = bill.notes;
      
      // Update status badge
      const statusBadge = document.getElementById('modalBillStatus');
      let statusClass = 'bg-secondary';
      if (bill.status === 'Fully Paid') statusClass = 'bg-success';
      else if (bill.status === 'Partially Paid') statusClass = 'bg-warning';
      else if (bill.status === 'Unpaid') statusClass = 'bg-danger';
      
      statusBadge.innerHTML = `<span class="badge ${statusClass}">${bill.status}</span>`;
      
      const modal = new bootstrap.Modal(document.getElementById('billDetailsModal'));
      modal.show();
    }
  }

  // Filter functions
  function applyBillingFilters() {
    const patientFilter = document.getElementById('patient_filter').value.toLowerCase();
    const statusFilter = document.getElementById('status_filter').value;
    const typeFilter = document.getElementById('bill_type_filter').value;
    const dateFrom = document.getElementById('date_from_filter').value;
    const dateTo = document.getElementById('date_to_filter').value;

    const rows = document.querySelectorAll('#billsTableBody tr');
    let visibleCount = 0;

    rows.forEach(row => {
      const patientName = row.querySelector('.fw-bold').textContent.toLowerCase();
      const status = row.querySelector('.badge').textContent.toLowerCase();
      const type = row.cells[2].textContent.toLowerCase();
      const date = row.cells[7].textContent;

      let showRow = true;

      if (patientFilter && !patientName.includes(patientFilter)) showRow = false;
      if (statusFilter && !status.includes(statusFilter.toLowerCase())) showRow = false;
      if (typeFilter && !type.includes(typeFilter.toLowerCase())) showRow = false;
      if (dateFrom && date < dateFrom) showRow = false;
      if (dateTo && date > dateTo) showRow = false;

      row.style.display = showRow ? '' : 'none';
      if (showRow) visibleCount++;
    });

    // Show results count
    if (visibleCount === 0) {
      Swal.fire({
        icon: 'info',
        title: 'No Results',
        text: 'No bills match the applied filters',
        timer: 2000,
        showConfirmButton: false
      });
    }
  }

  function clearFilters() {
    document.getElementById('billingFiltersForm').reset();
    document.getElementById('quickSearchInput').value = '';
    
    const rows = document.querySelectorAll('#billsTableBody tr');
    rows.forEach(row => {
      row.style.display = '';
    });
  }

  function quickSearchBills() {
    const searchTerm = document.getElementById('quickSearchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#billsTableBody tr');

    rows.forEach(row => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
  }

  // Export and print functions
  function exportBillingData() {
    Swal.fire({
      title: 'Exporting Billing Data...',
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });

    setTimeout(() => {
      Swal.close();
      Swal.fire({
        icon: 'success',
        title: 'Export Complete',
        text: 'Billing data has been exported successfully',
        timer: 2000,
        showConfirmButton: false
      });
    }, 1500);
  }

  function printBillingReport() {
    window.print();
  }

  function printBillDetails() {
    const modalContent = document.querySelector('#billDetailsModal .modal-body').innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
      <html>
        <head>
          <title>Bill Details</title>
          <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body>
          <div class="container mt-4">
            <h3 class="text-center mb-4">Bill Details</h3>
            ${modalContent}
          </div>
        </body>
      </html>
    `);
    printWindow.document.close();
    printWindow.print();
  }

  // Initialize page
  document.addEventListener('DOMContentLoaded', function() {
    // Set today's date as default for date filters
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('date_to_filter').value = today;
    
    // Set date from to 30 days ago
    const thirtyDaysAgo = new Date();
    thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
    document.getElementById('date_from_filter').value = thirtyDaysAgo.toISOString().split('T')[0];
  });
</script>
