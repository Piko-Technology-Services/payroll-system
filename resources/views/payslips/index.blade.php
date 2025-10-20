@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Payslips</h4>
        <div class="btn-group" style="gap: 0.5rem;">
            <button class="btn btn-primary btn-sm" type="button" onclick="openPayslipModal();">
            <i class="bi bi-plus-circle me-1"></i> Add Payslip
            </button>
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#generateFromNetPayModal">
            <i class="bi bi-calculator me-1"></i> Generate from Net Pay
            </button>
            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#generateAllModal">
            <i class="bi bi-gear me-1"></i> Generate All (Legacy)
            </button>
            <a href="{{ route('payslips.monthly') }}" class="btn btn-warning btn-sm">
            <i class="bi bi-calendar-check me-1"></i> Monthly Review
            </a>
            <a href="{{ route('payslips.export.csv') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
            </a>
            <div class="btn-group">
                <a href="{{ route('payslips.export.pdf') }}" class="btn btn-danger btn-sm">
                    <i class="bi bi-file-earmark-pdf"></i> Export All PDF
                </a>
                <button class="btn btn-danger btn-sm dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                    <span class="visually-hidden">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu">
                    <li><h6 class="dropdown-header">Export by Month</h6></li>
                    @foreach($payMonths as $month)
                        <li><a class="dropdown-item" href="{{ route('payslips.export.monthly.pdf', ['month' => $month]) }}">{{ $month }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="btn-group" style="gap: 0.5rem;">
            <a href="{{ route('default-earnings.index') }}" class="btn btn-info btn-sm">
                <i class="bi bi-cash-coin"></i> Default Earnings
            </a>
            <a href="{{ route('default-deductions.index') }}" class="btn btn-dark btn-sm">
                <i class="bi bi-dash-circle"></i> Default Deductions
            </a>
            </div>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Filters</h6>
                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#payslipFilterCollapse" aria-expanded="false">
                    <i class="bi bi-chevron-down"></i>
                </button>
            </div>
        </div>
        <div class="collapse" id="payslipFilterCollapse">
            <div class="card-body">
                <form method="GET" action="{{ route('payslips.index') }}" id="payslipFilterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Search Employee</label>
                            <input type="text" name="search" class="form-control" placeholder="Employee name or ID..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Employee</label>
                            <select name="employee_id" class="form-select">
                                <option value="">All Employees</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->fullnames }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Pay Month</label>
                            <select name="pay_month" class="form-select">
                                <option value="">All Months</option>
                                @foreach($payMonths as $month)
                                    <option value="{{ $month }}" {{ request('pay_month') == $month ? 'selected' : '' }}>{{ $month }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Pay Date From</label>
                            <input type="date" name="pay_date_from" class="form-control" value="{{ request('pay_date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Pay Date To</label>
                            <input type="date" name="pay_date_to" class="form-control" value="{{ request('pay_date_to') }}">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">Min Net Pay</label>
                            <input type="number" name="net_pay_min" class="form-control" placeholder="0" value="{{ request('net_pay_min') }}" step="0.01">
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-2">
                            <label class="form-label">Max Net Pay</label>
                            <input type="number" name="net_pay_max" class="form-control" placeholder="999999" value="{{ request('net_pay_max') }}" step="0.01">
                        </div>
                        <div class="col-md-10 d-flex align-items-end">
                            <div class="btn-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search me-1"></i>Filter
                                </button>
                                <a href="{{ route('payslips.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-1"></i>Clear
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Actions</th>
                        <th>Employee</th>
                        <th>Pay Month</th>
                        <th>Pay Date</th>
                        <th>Basic Pay</th>
                        <th>Total Earnings</th>
                        <th>Total Deductions</th>
                        <th>Net Pay</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payslips as $payslip)
                        <tr>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary" 
                                        onclick='openPayslipModal(@json($payslip));'>
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <a href="{{ route('payslips.pdf', $payslip->id) }}" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                    <form action="{{ route('payslips.destroy', $payslip->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this payslip?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            <td>{{ $payslip->employee->fullnames ?? 'N/A' }}</td>
                            <td>{{ $payslip->pay_month }}</td>
                            <td>{{ \Carbon\Carbon::parse($payslip->pay_date)->format('d M Y') }}</td>
                            <td>{{ number_format($payslip->earnings['Basic Pay'] ?? 0, 2) }}</td>
                            <td>{{ number_format(array_sum($payslip->earnings ?? []), 2) }}</td>
                            <td>{{ number_format(array_sum($payslip->deductions ?? []), 2) }}</td>
                            <td><strong>{{ number_format($payslip->net_pay ?? 0, 2) }}</strong></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No payslips yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add/Edit Payslip Modal --}}
<x-payslip-modal :employees="$employees" :earning-rules="$earningRules" :deduction-rules="$deductionRules" type="add" />

{{-- Generate from Net Pay Modal --}}
<div class="modal fade" id="generateFromNetPayModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate Payslips from Net Pay</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('payslips.generateFromNetPay') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        This will generate payslips for all employees using their <strong>salary_rate as Net Pay</strong>.
                        The system will reverse-calculate Gross Pay and statutory deductions.
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Pay Month</label>
                            <input type="text" name="pay_month" class="form-control" 
                                   value="{{ now()->format('Y-m') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pay Date</label>
                            <input type="date" name="pay_date" class="form-control" 
                                   value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        
                        <div class="col-12">
                            <h6>Default Allowances</h6>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Lunch Allowance</label>
                            <input type="number" step="0.01" name="lunch_allowance" 
                                   class="form-control" value="0" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Transport Allowance</label>
                            <input type="number" step="0.01" name="transport_allowance" 
                                   class="form-control" value="0" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Housing Allowance</label>
                            <input type="number" step="0.01" name="housing_allowance" 
                                   class="form-control" value="0" min="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-calculator me-1"></i> Generate Payslips
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Generate All (Legacy) Modal --}}
<div class="modal fade" id="generateAllModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate All Payslips (Legacy)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('payslips.generateAll') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Legacy Method:</strong> This uses salary_rate as Gross Pay (old method).
                        Consider using "Generate from Net Pay" for more accurate calculations.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Pay Month</label>
                        <input type="text" name="pay_month" class="form-control" 
                               value="{{ now()->format('Y-m') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info">
                        <i class="bi bi-gear me-1"></i> Generate All
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('payslips.rules_modal')
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Auto-expand filter if any filters are active
    const urlParams = new URLSearchParams(window.location.search);
    const hasFilters = Array.from(urlParams.keys()).some(key => 
        ['search', 'employee_id', 'pay_month', 'pay_date_from', 'pay_date_to', 'net_pay_min', 'net_pay_max'].includes(key) && urlParams.get(key)
    );
    
    if (hasFilters) {
        document.getElementById('payslipFilterCollapse').classList.add('show');
    }

    // Real-time search functionality
    let searchTimeout;
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.length >= 3 || this.value.length === 0) {
                    document.getElementById('payslipFilterForm').submit();
                }
            }, 500);
        });
    }
});
</script>
@endsection
