@extends('layouts.app')

@section('title', 'Reports & Analytics')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1>Reports & Analytics</h1>
            <p class="text-muted">Comprehensive clinic performance and patient analytics</p>
        </div>
        <div>
            <button class="btn btn-outline-secondary" onclick="printReport()">
                <i class="fas fa-print me-2"></i>
                Print Report
            </button>
            <button class="btn btn-outline-success" onclick="exportToCSV()">
                <i class="fas fa-download me-2"></i>
                Export CSV
            </button>
        </div>
    </div>

    <!-- Report Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="reportType" class="form-label">Report Type</label>
                    <select class="form-select" id="reportType">
                        <option value="overview">Clinic Overview</option>
                        <option value="patients">Patient Statistics</option>
                        <option value="visits">Visit Reports</option>
                        <option value="billing">Billing & Revenue</option>
                        <option value="services">Service Usage</option>
                        <option value="packages">Package Analytics</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="dateRange" class="form-label">Date Range</label>
                    <select class="form-select" id="dateRange" onchange="toggleCustomDates()">
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="quarter">This Quarter</option>
                        <option value="year">This Year</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
                <div class="col-md-2" id="startDateDiv" style="display: none">
                    <label for="startDate" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="startDate" />
                </div>
                <div class="col-md-2" id="endDateDiv" style="display: none">
                    <label for="endDate" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="endDate" />
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button class="btn btn-primary w-100" onclick="generateReport()">
                        <i class="fas fa-sync me-2"></i>
                        Generate
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Overview Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-details">
                    <h3>248 <span class="change-badge text-success">+12%</span></h3>
                    <p>Total Patients</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-calendar"></i>
                </div>
                <div class="stat-details">
                    <h3>1,247 <span class="change-badge text-success">+8%</span></h3>
                    <p>Total Visits</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-info">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-details">
                    <h3>GH₵2.4M <span class="change-badge text-success">+15%</span></h3>
                    <p>Revenue</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-details">
                    <h3>45 <span class="change-badge text-success">+3%</span></h3>
                    <p>Active Packages</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-4">
        <!-- Revenue Trend Chart -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-chart-line me-2"></i>Revenue Trend</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Service Distribution Chart -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-chart-doughnut me-2"></i>Service Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="serviceChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Report Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-table me-2"></i>Detailed Report Data</h5>
            <small class="text-muted">Showing 5 records</small>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Period</th>
                        <th>Visits</th>
                        <th>Patients</th>
                        <th>Revenue</th>
                        <th>Avg. Per Visit</th>
                        <th>Growth Rate</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>January 2024</strong></td>
                        <td>142</td>
                        <td>56</td>
                        <td>GH₵280,000</td>
                        <td>GH₵1,972</td>
                        <td><span class="badge bg-success">+8%</span></td>
                    </tr>
                    <tr>
                        <td><strong>December 2023</strong></td>
                        <td>131</td>
                        <td>52</td>
                        <td>GH₵258,000</td>
                        <td>GH₵1,969</td>
                        <td><span class="badge bg-success">+5%</span></td>
                    </tr>
                    <tr>
                        <td><strong>November 2023</strong></td>
                        <td>125</td>
                        <td>48</td>
                        <td>GH₵245,000</td>
                        <td>GH₵1,960</td>
                        <td><span class="badge bg-warning">+2%</span></td>
                    </tr>
                    <tr>
                        <td><strong>October 2023</strong></td>
                        <td>122</td>
                        <td>45</td>
                        <td>GH₵240,000</td>
                        <td>GH₵1,967</td>
                        <td><span class="badge bg-success">+3%</span></td>
                    </tr>
                    <tr>
                        <td><strong>September 2023</strong></td>
                        <td>118</td>
                        <td>42</td>
                        <td>GH₵233,000</td>
                        <td>GH₵1,975</td>
                        <td><span class="badge bg-warning">+1%</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="card-footer">
            <nav>
                <ul class="pagination justify-content-center mb-0">
                    <li class="page-item disabled">
                        <a class="page-link" href="#">Previous</a>
                    </li>
                    <li class="page-item active">
                        <a class="page-link" href="#">1</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">3</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<div id="alert-container"></div>
@endsection

@section('css')
<style>
    .stat-card {
        background: white;
        border-radius: 0.5rem;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        margin-right: 1rem;
    }

    .change-badge {
        font-size: 0.8rem;
        margin-left: 0.5rem;
    }

    .table-responsive {
        border-top: 1px solid #e2e8f0;
    }

    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Initialize charts
    document.addEventListener("DOMContentLoaded", function () {
        initializeRevenueChart();
        initializeServiceChart();
    });

    function initializeRevenueChart() {
        const ctx = document.getElementById("revenueChart").getContext("2d");
        const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        const data = [190000, 210000, 245000, 258000, 240000, 280000, 298000, 275000, 233000, 240000, 258000, 280000];

        new Chart(ctx, {
            type: "line",
            data: {
                labels: months,
                datasets: [
                    {
                        label: "Monthly Revenue (GH₵)",
                        data: data,
                        borderColor: "#3498db",
                        backgroundColor: "rgba(52, 152, 219, 0.1)",
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointBackgroundColor: "#3498db",
                        pointHoverRadius: 7,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: "bottom",
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return "GH₵" + value.toLocaleString();
                            },
                        },
                    },
                },
            },
        });
    }

    function initializeServiceChart() {
        const ctx = document.getElementById("serviceChart").getContext("2d");

        new Chart(ctx, {
            type: "doughnut",
            data: {
                labels: ["Physiotherapy", "Massage", "Consultation", "Package", "Other"],
                datasets: [
                    {
                        data: [35, 25, 20, 15, 5],
                        backgroundColor: ["#3498db", "#2ecc71", "#f39c12", "#e74c3c", "#95a5a6"],
                        borderColor: "white",
                        borderWidth: 2,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: "bottom",
                    },
                },
            },
        });
    }

    function toggleCustomDates() {
        const dateRange = document.getElementById("dateRange").value;
        const startDateDiv = document.getElementById("startDateDiv");
        const endDateDiv = document.getElementById("endDateDiv");

        if (dateRange === "custom") {
            startDateDiv.style.display = "block";
            endDateDiv.style.display = "block";
        } else {
            startDateDiv.style.display = "none";
            endDateDiv.style.display = "none";
        }
    }

    function generateReport() {
        const reportType = document.getElementById("reportType").value;
        const dateRange = document.getElementById("dateRange").value;

        console.log("Generating report:", reportType, dateRange);
        window.clinicSystem.showAlert("Report generated successfully!", "success");
    }

    function printReport() {
        window.print();
    }

    function exportToCSV() {
        const table = document.querySelector("table");
        let csv = "";
        const rows = table.querySelectorAll("tr");

        rows.forEach((row) => {
            const cells = row.querySelectorAll("td, th");
            const rowData = Array.from(cells)
                .map((cell) => '"' + cell.textContent.trim() + '"')
                .join(",");
            csv += rowData + "\n";
        });

        const blob = new Blob([csv], { type: "text/csv" });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = "report-" + new Date().toISOString().split("T")[0] + ".csv";
        a.click();

        window.clinicSystem.showAlert("Report exported successfully!", "success");
    }
</script>
@endsection
