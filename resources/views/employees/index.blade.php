@extends('layouts.app')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">Employees</h4>
    <div class="btn-group">
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
            <i class="bi bi-plus-circle me-1"></i> Add Employee
        </button>

        {{-- Import Form --}}
        <form action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data" class="d-inline mx-3" id="employeesImportForm">
            @csrf
            <input type="file" name="file" class="d-none" id="employeeFile" accept=".csv, .xlsx" onchange="this.form.submit()">
            <label for="employeeFile" class="btn btn-secondary btn-sm" id="importEmployeesBtn">
                <span class="spinner-border spinner-border-sm me-1 d-none" role="status" aria-hidden="true"></span>
                <i class="bi bi-upload me-1"></i><span class="import-text">Import</span>
            </label>
        </form>

       <a href="{{ asset('templates/employees_template.xlsx') }}" class="btn btn-success btn-sm" download>
            <i class="bi bi-download"></i> Download Excel Template
        </a>

    </div>
</div>

{{-- Filter Section --}}
<div class="card shadow-sm border-0 mb-3">
    <div class="card-header bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Filters</h6>
            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false">
                <i class="bi bi-chevron-down"></i>
            </button>
        </div>
    </div>
    <div class="collapse" id="filterCollapse">
        <div class="card-body">
            <form method="GET" action="{{ route('employees.index') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Name, ID, Position..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Department</label>
                        <select name="department" class="form-select">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Position</label>
                        <select name="position" class="form-select">
                            <option value="">All Positions</option>
                            @foreach($positions as $pos)
                                <option value="{{ $pos }}" {{ request('position') == $pos ? 'selected' : '' }}>{{ $pos }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Branch</label>
                        <select name="branch" class="form-select">
                            <option value="">All Branches</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch }}" {{ request('branch') == $branch ? 'selected' : '' }}>{{ $branch }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Company</label>
                        <select name="company" class="form-select">
                            <option value="">All Companies</option>
                            @foreach($companies as $company)
                                <option value="{{ $company }}" {{ request('company') == $company ? 'selected' : '' }}>{{ $company }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Pay Method</label>
                        <select name="pay_method" class="form-select">
                            <option value="">All</option>
                            @foreach($payMethods as $method)
                                <option value="{{ $method }}" {{ request('pay_method') == $method ? 'selected' : '' }}>{{ $method }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-2">
                        <label class="form-label">Min Salary</label>
                        <input type="number" name="salary_min" class="form-control" placeholder="0" value="{{ request('salary_min') }}" step="0.01">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Max Salary</label>
                        <input type="number" name="salary_max" class="form-control" placeholder="999999" value="{{ request('salary_max') }}" step="0.01">
                    </div>
                    <div class="col-md-8 d-flex align-items-end">
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Filter
                            </button>
                            <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
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
                        <th>Full Name</th>
                        <th>Employee ID</th>
                        <th>Salary Rate</th>
                        <th>Position</th>
                        <th>Department</th>
                        <th>Branch</th>
                        <th>Pay Method</th>
                        <th>Bank Account</th>
                        <th>Company</th>
                        <th>Date Engaged</th>
                        <th>NRC Number</th>
                        <th>SSN</th>
                        <th>NHI No</th>
                        <th>Leave Days</th>
                        <th>TPIN</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                        <tr>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#editEmployeeModal{{ $employee->id }}"><i class="bi bi-pencil"></i></button>
                                    <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this employee?')"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                            <td>{{ $employee->fullnames }}</td>
                            <td>{{ $employee->employee_id }}</td>
                            <td>{{ number_format($employee->salary_rate, 2) }}</td>
                            <td>{{ $employee->position }}</td>
                            <td>{{ $employee->department }}</td>
                            <td>{{ $employee->branch }}</td>
                            <td>{{ $employee->pay_method }}</td>
                            <td>{{ $employee->bank_acc_number }}</td>
                            <td>{{ $employee->company }}</td>
                            <td>{{ $employee->date_engaged }}</td>
                            <td>{{ $employee->nrc_number }}</td>
                            <td>{{ $employee->ssn }}</td>
                            <td>{{ $employee->nhi_no }}</td>
                            <td>{{ $employee->leave_days }}</td>
                            <td>{{ $employee->tpin }}</td>
                        </tr>

                        {{-- Edit Modal --}}
                        <div class="modal fade" id="editEmployeeModal{{ $employee->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Employee</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('employees.update', $employee->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Full Names</label>
                                                    <input type="text" name="fullnames" value="{{ $employee->fullnames }}" class="form-control" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Employee ID</label>
                                                    <input type="text" name="employee_id" value="{{ $employee->employee_id }}" class="form-control" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Date Engaged</label>
                                                    <input type="date" name="date_engaged" value="{{ $employee->date_engaged }}" class="form-control">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Salary Rate</label>
                                                    <input type="number" name="salary_rate" value="{{ $employee->salary_rate }}" class="form-control" step="0.01">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Department</label>
                                                    <input type="text" name="department" value="{{ $employee->department }}" class="form-control">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Position</label>
                                                    <input type="text" name="position" value="{{ $employee->position }}" class="form-control">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Branch</label>
                                                    <input type="text" name="branch" value="{{ $employee->branch }}" class="form-control">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Company</label>
                                                    <input type="text" name="company" value="{{ $employee->company }}" class="form-control">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Pay Method</label>
                                                    <input type="text" name="pay_method" value="{{ $employee->pay_method }}" class="form-control">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Bank Acc Number</label>
                                                    <input type="text" name="bank_acc_number" value="{{ $employee->bank_acc_number }}" class="form-control">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">NRC Number</label>
                                                    <input type="text" name="nrc_number" value="{{ $employee->nrc_number }}" class="form-control">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">SSN</label>
                                                    <input type="text" name="ssn" value="{{ $employee->ssn }}" class="form-control">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">NHI No</label>
                                                    <input type="text" name="nhi_no" value="{{ $employee->nhi_no }}" class="form-control">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Leave Days</label>
                                                    <input type="number" name="leave_days" value="{{ $employee->leave_days }}" class="form-control">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">TPIN</label>
                                                    <input type="text" name="tpin" value="{{ $employee->tpin }}" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success" id="editEmployeeSubmitBtn{{ $employee->id }}">
                                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                                <span class="submit-text">Save Changes</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">No employees found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add Modal --}}
<div class="modal fade" id="addEmployeeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('employees.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Names</label>
                            <input type="text" name="fullnames" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Employee ID</label>
                            <input type="text" name="employee_id" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date Engaged</label>
                            <input type="date" name="date_engaged" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Salary Rate</label>
                            <input type="number" name="salary_rate" class="form-control" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Company</label>
                            <input type="text" name="company" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Branch</label>
                            <input type="text" name="branch" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Department</label>
                            <input type="text" name="department" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Position</label>
                            <input type="text" name="position" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pay Method</label>
                            <input type="text" name="pay_method" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bank Acc Number</label>
                            <input type="text" name="bank_acc_number" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NRC Number</label>
                            <input type="text" name="nrc_number" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">SSN</label>
                            <input type="text" name="ssn" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NHI No</label>
                            <input type="text" name="nhi_no" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Leave Days</label>
                            <input type="number" name="leave_days" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">TPIN</label>
                            <input type="text" name="tpin" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" id="addEmployeeSubmitBtn" type="submit">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span class="submit-text">Save Employee</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('employeesImportForm');
    var btn = document.getElementById('importEmployeesBtn');
    var spinner = btn ? btn.querySelector('.spinner-border') : null;
    var textEl = btn ? btn.querySelector('.import-text') : null;
    var fileInput = document.getElementById('employeeFile');

    function startLoading() {
        if (!btn || !spinner || !textEl) return;
        btn.classList.add('disabled');
        spinner.classList.remove('d-none');
        textEl.textContent = 'Importing...';
    }

    if (form) {
        form.addEventListener('submit', function () {
            startLoading();
        });
    }

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            // In case submit is delayed by validation, show loading as soon as file is chosen
            startLoading();
        });
    }

    // Add loading spinner for Add Employee form
    var addForm = document.querySelector('#addEmployeeModal form');
    var addBtn = document.getElementById('addEmployeeSubmitBtn');
    var addSpinner = addBtn ? addBtn.querySelector('.spinner-border') : null;
    var addText = addBtn ? addBtn.querySelector('.submit-text') : null;
    if (addForm && addBtn && addSpinner && addText) {
        addForm.addEventListener('submit', function () {
            addBtn.classList.add('disabled');
            addSpinner.classList.remove('d-none');
            addText.textContent = 'Saving...';
        });
    }
    // Add loading spinner for Edit Employee forms
    document.querySelectorAll('[id^="editEmployeeModal"] form').forEach(function(editForm) {
        var btn = editForm.querySelector('button[type="submit"]');
        var spinner = btn ? btn.querySelector('.spinner-border') : null;
        var text = btn ? btn.querySelector('.submit-text') : null;
        if (btn && spinner && text) {
            editForm.addEventListener('submit', function () {
                btn.classList.add('disabled');
                spinner.classList.remove('d-none');
                text.textContent = 'Saving...';
            });
        }
    });

    // Auto-expand filter if any filters are active
    const urlParams = new URLSearchParams(window.location.search);
    const hasFilters = Array.from(urlParams.keys()).some(key => 
        ['search', 'department', 'position', 'branch', 'company', 'pay_method', 'salary_min', 'salary_max'].includes(key) && urlParams.get(key)
    );
    
    if (hasFilters) {
        document.getElementById('filterCollapse').classList.add('show');
    }

    // Real-time search functionality
    let searchTimeout;
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.length >= 3 || this.value.length === 0) {
                    document.getElementById('filterForm').submit();
                }
            }, 500);
        });
    }
});
</script>
@endsection
