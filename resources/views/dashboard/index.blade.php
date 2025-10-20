@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="container-fluid">
    {{-- Welcome Section --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title text-white mb-1">Welcome back, {{ auth()->user()->name }}!</h4>
                            <p class="card-text mb-0">Here's what's happening with your payroll system today.</p>
                        </div>
                        <div class="text-end">
                            <i class="bi bi-calendar-date" style="font-size: 2rem; opacity: 0.7;"></i>
                            <div class="mt-1">{{ now()->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Stats Cards --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Employees
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalEmployees) }}</div>
                            @if($newEmployeesThisMonth > 0)
                                <small class="text-success">+{{ $newEmployeesThisMonth }} this month</small>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people-fill text-primary" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Payslips This Month
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($payslipsThisMonth) }}</div>
                            @if($pendingPayslips > 0)
                                <small class="text-warning">{{ $pendingPayslips }} pending</small>
                            @else
                                <small class="text-success">All completed</small>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-file-earmark-text-fill text-success" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Monthly Payroll
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">ZMW {{ number_format($totalPayrollThisMonth, 2) }}</div>
                            <small class="text-muted">{{ now()->format('F Y') }}</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-cash-stack text-info" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Yearly Payroll
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">ZMW {{ number_format($totalPayrollThisYear, 2) }}</div>
                            <small class="text-muted">{{ now()->year }}</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-graph-up text-warning" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Navigation Cards --}}
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3">Quick Actions</h5>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <a href="{{ route('employees.index') }}" class="text-decoration-none">
                <div class="card text-center h-100 shadow-sm hover-shadow">
                    <div class="card-body">
                        <i class="bi bi-people text-primary mb-2" style="font-size: 2.5rem;"></i>
                        <h6 class="card-title">Manage Employees</h6>
                        <p class="card-text small text-muted">Add, edit, or view employee records</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <a href="{{ route('payslips.index') }}" class="text-decoration-none">
                <div class="card text-center h-100 shadow-sm hover-shadow">
                    <div class="card-body">
                        <i class="bi bi-file-earmark-text text-success mb-2" style="font-size: 2.5rem;"></i>
                        <h6 class="card-title">Payslips</h6>
                        <p class="card-text small text-muted">Generate and manage payslips</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <a href="{{ route('payslips.monthly') }}" class="text-decoration-none">
                <div class="card text-center h-100 shadow-sm hover-shadow">
                    <div class="card-body">
                        <i class="bi bi-calendar-check text-warning mb-2" style="font-size: 2.5rem;"></i>
                        <h6 class="card-title">Monthly Review</h6>
                        <p class="card-text small text-muted">Review payslips by month</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <a href="{{ route('default-earnings.index') }}" class="text-decoration-none">
                <div class="card text-center h-100 shadow-sm hover-shadow">
                    <div class="card-body">
                        <i class="bi bi-cash-coin text-info mb-2" style="font-size: 2.5rem;"></i>
                        <h6 class="card-title">Earnings</h6>
                        <p class="card-text small text-muted">Manage default earnings</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <a href="{{ route('default-deductions.index') }}" class="text-decoration-none">
                <div class="card text-center h-100 shadow-sm hover-shadow">
                    <div class="card-body">
                        <i class="bi bi-dash-circle text-danger mb-2" style="font-size: 2.5rem;"></i>
                        <h6 class="card-title">Deductions</h6>
                        <p class="card-text small text-muted">Manage default deductions</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <a href="{{ route('payslips.export.pdf') }}" class="text-decoration-none">
                <div class="card text-center h-100 shadow-sm hover-shadow">
                    <div class="card-body">
                        <i class="bi bi-file-earmark-pdf text-secondary mb-2" style="font-size: 2.5rem;"></i>
                        <h6 class="card-title">Export PDF</h6>
                        <p class="card-text small text-muted">Export all payslips to PDF</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row">
        {{-- Recent Activity --}}
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Payslips</h6>
                </div>
                <div class="card-body">
                    @if($recentPayslips->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentPayslips as $payslip)
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <h6 class="mb-1">{{ $payslip->employee->fullnames ?? 'N/A' }}</h6>
                                        <small class="text-muted">{{ $payslip->pay_month }} - ZMW {{ number_format($payslip->net_pay, 2) }}</small>
                                    </div>
                                    <small class="text-muted">{{ $payslip->created_at->diffForHumans() }}</small>
                                </div>
                            @endforeach
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('payslips.index') }}" class="btn btn-primary btn-sm">View All Payslips</a>
                        </div>
                    @else
                        <p class="text-muted text-center">No payslips created yet.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Department Statistics --}}
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h6 class="m-0 font-weight-bold text-primary">Employees by Department</h6>
                </div>
                <div class="card-body">
                    @if($departmentStats->count() > 0)
                        @foreach($departmentStats as $dept)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>{{ $dept->department }}</span>
                                <div>
                                    <span class="badge bg-primary">{{ $dept->count }}</span>
                                    <div class="progress mt-1" style="height: 5px; width: 100px;">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: {{ ($dept->count / $totalEmployees) * 100 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div class="text-center mt-3">
                            <a href="{{ route('employees.index') }}" class="btn btn-primary btn-sm">View All Employees</a>
                        </div>
                    @else
                        <p class="text-muted text-center">No department data available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Monthly Trend Chart --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h6 class="m-0 font-weight-bold text-primary">Payroll Trend (Last 6 Months)</h6>
                </div>
                <div class="card-body">
                    <canvas id="payrollChart" width="400" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Payroll Trend Chart
    const ctx = document.getElementById('payrollChart').getContext('2d');
    const monthlyData = @json($monthlyTrend);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: monthlyData.map(item => item.month),
            datasets: [{
                label: 'Monthly Payroll (ZMW)',
                data: monthlyData.map(item => item.amount),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'ZMW ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
});
</script>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.hover-shadow:hover {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
    transform: translateY(-2px);
    transition: all 0.3s;
}
.card {
    transition: all 0.3s;
}
</style>
@endsection
