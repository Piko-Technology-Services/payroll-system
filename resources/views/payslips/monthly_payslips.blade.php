@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Monthly Payslips Review</h4>
        <div class="btn-group">
            <a href="{{ route('payslips.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to Payslips
            </a>
            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#helpModal">
                <i class="bi bi-question-circle me-1"></i> Help
            </button>
        </div>
    </div>

    {{-- Filters Section --}}
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Filters</h6>
                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse" aria-expanded="false">
                    <i class="bi bi-chevron-down"></i>
                </button>
            </div>
        </div>
        <div class="collapse" id="filtersCollapse">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Search Employee</label>
                        <input type="text" id="employeeSearch" class="form-control form-control-sm" placeholder="Name or Employee ID...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Department</label>
                        <select id="departmentFilter" class="form-select form-select-sm">
                            <option value="">All Departments</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Position</label>
                        <select id="positionFilter" class="form-select form-select-sm">
                            <option value="">All Positions</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Branch</label>
                        <select id="branchFilter" class="form-select form-select-sm">
                            <option value="">All Branches</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select id="statusFilter" class="form-select form-select-sm">
                            <option value="">All</option>
                            <option value="saved">Saved</option>
                            <option value="new">New</option>
                        </select>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button id="clearFilters" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x-circle"></i> Clear
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Month Selection and Controls --}}
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-primary text-white">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <label class="form-label text-white mb-0">Select Month:</label>
                    <select id="monthSelector" class="form-select form-select-sm">
                        @foreach($payMonths as $month)
                            <option value="{{ $month }}" {{ $month == $selectedMonth ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}
                            </option>
                        @endforeach
                        <option value="{{ now()->format('Y-m') }}" {{ now()->format('Y-m') == $selectedMonth ? 'selected' : '' }}>
                            {{ now()->format('F Y') }} (Current)
                        </option>
                    </select>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <button id="prevEmployee" class="btn btn-outline-light btn-sm me-2">
                            <i class="bi bi-chevron-left"></i> Previous
                        </button>
                        <span id="employeeCounter" class="text-white mx-3">Employee 1 of {{ $employees->count() }}</span>
                        <button id="nextEmployee" class="btn btn-outline-light btn-sm">
                            Next <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-3 text-end">
                    <div class="btn-group">
                        <button id="savePayslip" class="btn btn-success btn-sm">
                            <i class="bi bi-check-circle me-1"></i> Save
                        </button>
                        <button id="saveAndExportPayslip" class="btn btn-warning btn-sm">
                            <i class="bi bi-file-earmark-pdf me-1"></i> Save & Export
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Employee Payslip Form --}}
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form id="payslipForm">
                @csrf
                <input type="hidden" id="currentEmployeeId" name="employee_id">
                <input type="hidden" id="payMonth" name="pay_month" value="{{ $selectedMonth }}">
                <input type="hidden" id="payslipId" name="payslip_id">

                {{-- Employee Info Header --}}
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="bg-light p-3 rounded">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Employee:</strong> <span id="employeeName"></span>
                                </div>
                                <div class="col-md-2">
                                    <strong>ID:</strong> <span id="employeeId"></span>
                                </div>
                                <div class="col-md-2">
                                    <strong>Position:</strong> <span id="employeePosition"></span>
                                </div>
                                <div class="col-md-2">
                                    <strong>Department:</strong> <span id="employeeDepartment"></span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Salary Rate:</strong> <span id="employeeSalaryRate"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Basic Details --}}
                    <div class="col-md-12 mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Pay Date</label>
                                <input type="date" name="pay_date" id="payDate" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>

                    {{-- Earnings Section --}}
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="text-success mb-0">
                                <i class="bi bi-plus-circle me-2"></i>Earnings
                            </h5>
                            <button type="button" class="btn btn-success btn-sm" id="addEarningBtn">
                                <i class="bi bi-plus"></i> Add
                            </button>
                        </div>
                        <div id="earningsSection">
                            <!-- Earnings will be populated by JavaScript -->
                        </div>
                        <div class="mt-3 p-2 bg-success bg-opacity-10 rounded">
                            <strong>Total Earnings: <span id="totalEarnings">0.00</span></strong>
                        </div>
                    </div>

                    {{-- Deductions Section --}}
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="text-danger mb-0">
                                <i class="bi bi-dash-circle me-2"></i>Deductions
                            </h5>
                            <button type="button" class="btn btn-danger btn-sm" id="addDeductionBtn">
                                <i class="bi bi-plus"></i> Add
                            </button>
                        </div>
                        <div id="deductionsSection">
                            <!-- Deductions will be populated by JavaScript -->
                        </div>
                        <div class="mt-3 p-2 bg-danger bg-opacity-10 rounded">
                            <strong>Total Deductions: <span id="totalDeductions">0.00</span></strong>
                        </div>
                    </div>
                </div>

                {{-- Net Pay Summary --}}
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h4 class="mb-0">Net Pay: <span id="netPay">0.00</span></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Loading Overlay --}}
<div id="loadingOverlay" class="d-none position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50" style="z-index: 9999;">
    <div class="d-flex justify-content-center align-items-center h-100">
        <div class="spinner-border text-light" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>

{{-- Help Modal --}}
<div class="modal fade" id="helpModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Monthly Payslips Review - Help</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>How to Use:</h6>
                <ul>
                    <li><strong>Filter Employees:</strong> Use the filters section to narrow down employees by search, department, position, branch, or status</li>
                    <li><strong>Select Month:</strong> Choose the month you want to review payslips for</li>
                    <li><strong>Navigate:</strong> Use Previous/Next buttons or keyboard shortcuts to move between employees</li>
                    <li><strong>Review:</strong> Each employee's payslip will be auto-populated with default values</li>
                    <li><strong>Edit:</strong> Modify any earnings or deductions as needed</li>
                    <li><strong>Save:</strong> Click Save to store the payslip, or Save & Export to also generate PDF</li>
                </ul>
                
                <h6>Filtering Options:</h6>
                <ul>
                    <li><strong>Search:</strong> Find employees by name or employee ID</li>
                    <li><strong>Department/Position/Branch:</strong> Filter by specific organizational units</li>
                    <li><strong>Status:</strong> Show only employees with saved payslips or new (unsaved) ones</li>
                    <li><strong>Clear:</strong> Reset all filters to show all employees</li>
                </ul>
                
                <h6>Keyboard Shortcuts:</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <td><kbd>Ctrl</kbd> + <kbd>←</kbd></td>
                                <td>Previous Employee</td>
                            </tr>
                            <tr>
                                <td><kbd>Ctrl</kbd> + <kbd>→</kbd></td>
                                <td>Next Employee</td>
                            </tr>
                            <tr>
                                <td><kbd>Ctrl</kbd> + <kbd>S</kbd></td>
                                <td>Save Payslip</td>
                            </tr>
                            <tr>
                                <td><kbd>Ctrl</kbd> + <kbd>E</kbd></td>
                                <td>Save & Export PDF</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <h6>Status Indicators:</h6>
                <ul>
                    <li><span class="badge bg-success">Saved</span> - Payslip already exists for this employee</li>
                    <li><span class="badge bg-warning">New</span> - New payslip (not yet saved)</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const allEmployees = @json($employees);
    const existingPayslips = @json($existingPayslips);
    const selectedMonth = @json($selectedMonth);
    
    let employees = [...allEmployees]; // Filtered employees array
    let currentEmployeeIndex = 0;
    let currentPayslipData = null;

    // Initialize filters
    initializeFilters();
    
    // Initialize
    loadEmployee(currentEmployeeIndex);

    // Month selector change
    document.getElementById('monthSelector').addEventListener('change', function() {
        const newMonth = this.value;
        window.location.href = `{{ route('payslips.monthly') }}?month=${newMonth}`;
    });

    // Navigation buttons
    document.getElementById('prevEmployee').addEventListener('click', function() {
        if (currentEmployeeIndex > 0) {
            currentEmployeeIndex--;
            loadEmployee(currentEmployeeIndex);
        }
    });

    document.getElementById('nextEmployee').addEventListener('click', function() {
        if (currentEmployeeIndex < employees.length - 1) {
            currentEmployeeIndex++;
            loadEmployee(currentEmployeeIndex);
        }
    });

    // Save button
    document.getElementById('savePayslip').addEventListener('click', function() {
        savePayslip(false);
    });

    // Save and Export button
    document.getElementById('saveAndExportPayslip').addEventListener('click', function() {
        savePayslip(true);
    });

    // Filter event listeners
    document.getElementById('employeeSearch').addEventListener('input', applyFilters);
    document.getElementById('departmentFilter').addEventListener('change', applyFilters);
    document.getElementById('positionFilter').addEventListener('change', applyFilters);
    document.getElementById('branchFilter').addEventListener('change', applyFilters);
    document.getElementById('statusFilter').addEventListener('change', applyFilters);
    
    // Clear filters button
    document.getElementById('clearFilters').addEventListener('click', function() {
        document.getElementById('employeeSearch').value = '';
        document.getElementById('departmentFilter').value = '';
        document.getElementById('positionFilter').value = '';
        document.getElementById('branchFilter').value = '';
        document.getElementById('statusFilter').value = '';
        applyFilters();
    });

    // Add earning button
    document.getElementById('addEarningBtn').addEventListener('click', function() {
        addEarningItem();
    });

    // Add deduction button
    document.getElementById('addDeductionBtn').addEventListener('click', function() {
        addDeductionItem();
    });

    function initializeFilters() {
        // Populate department filter
        const departments = [...new Set(allEmployees.map(emp => emp.department).filter(Boolean))].sort();
        const departmentSelect = document.getElementById('departmentFilter');
        departments.forEach(dept => {
            const option = document.createElement('option');
            option.value = dept;
            option.textContent = dept;
            departmentSelect.appendChild(option);
        });

        // Populate position filter
        const positions = [...new Set(allEmployees.map(emp => emp.position).filter(Boolean))].sort();
        const positionSelect = document.getElementById('positionFilter');
        positions.forEach(pos => {
            const option = document.createElement('option');
            option.value = pos;
            option.textContent = pos;
            positionSelect.appendChild(option);
        });

        // Populate branch filter
        const branches = [...new Set(allEmployees.map(emp => emp.branch).filter(Boolean))].sort();
        const branchSelect = document.getElementById('branchFilter');
        branches.forEach(branch => {
            const option = document.createElement('option');
            option.value = branch;
            option.textContent = branch;
            branchSelect.appendChild(option);
        });
    }

    function applyFilters() {
        const searchTerm = document.getElementById('employeeSearch').value.toLowerCase();
        const departmentFilter = document.getElementById('departmentFilter').value;
        const positionFilter = document.getElementById('positionFilter').value;
        const branchFilter = document.getElementById('branchFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;

        employees = allEmployees.filter(employee => {
            // Search filter
            if (searchTerm && !employee.fullnames.toLowerCase().includes(searchTerm) && 
                !employee.employee_id.toLowerCase().includes(searchTerm)) {
                return false;
            }

            // Department filter
            if (departmentFilter && employee.department !== departmentFilter) {
                return false;
            }

            // Position filter
            if (positionFilter && employee.position !== positionFilter) {
                return false;
            }

            // Branch filter
            if (branchFilter && employee.branch !== branchFilter) {
                return false;
            }

            // Status filter
            if (statusFilter) {
                const hasPayslip = existingPayslips[employee.id];
                if (statusFilter === 'saved' && !hasPayslip) return false;
                if (statusFilter === 'new' && hasPayslip) return false;
            }

            return true;
        });

        // Reset to first employee if current index is out of bounds
        if (currentEmployeeIndex >= employees.length) {
            currentEmployeeIndex = 0;
        }

        // Load the current employee (or first if none selected)
        if (employees.length > 0) {
            loadEmployee(currentEmployeeIndex);
        } else {
            // No employees match the filter
            showNoEmployeesMessage();
        }

        // Update filter collapse state
        const hasActiveFilters = searchTerm || departmentFilter || positionFilter || branchFilter || statusFilter;
        if (hasActiveFilters) {
            document.getElementById('filtersCollapse').classList.add('show');
        }
    }

    function showNoEmployeesMessage() {
        document.getElementById('employeeName').textContent = 'No employees match the current filters';
        document.getElementById('employeeId').textContent = '-';
        document.getElementById('employeePosition').textContent = '-';
        document.getElementById('employeeDepartment').textContent = '-';
        document.getElementById('employeeSalaryRate').textContent = '-';
        document.getElementById('employeeCounter').textContent = 'No employees found';
        
        // Clear form sections
        document.getElementById('earningsSection').innerHTML = '<p class="text-muted">No employee selected</p>';
        document.getElementById('deductionsSection').innerHTML = '<p class="text-muted">No employee selected</p>';
        
        // Clear totals
        document.getElementById('totalEarnings').textContent = '0.00';
        document.getElementById('totalDeductions').textContent = '0.00';
        document.getElementById('netPay').textContent = '0.00';
        
        // Disable navigation and action buttons
        document.getElementById('prevEmployee').disabled = true;
        document.getElementById('nextEmployee').disabled = true;
        document.getElementById('savePayslip').disabled = true;
        document.getElementById('saveAndExportPayslip').disabled = true;
    }

    function loadEmployee(index) {
        if (!employees[index]) return;
        
        const employee = employees[index];
        const existingPayslip = existingPayslips[employee.id];
        
        // Update employee info
        document.getElementById('employeeName').textContent = employee.fullnames;
        document.getElementById('employeeId').textContent = employee.employee_id;
        document.getElementById('employeePosition').textContent = employee.position || 'N/A';
        document.getElementById('employeeDepartment').textContent = employee.department || 'N/A';
        document.getElementById('employeeSalaryRate').textContent = parseFloat(employee.salary_rate).toFixed(2);
        
        // Update form fields
        document.getElementById('currentEmployeeId').value = employee.id;
        document.getElementById('payslipId').value = existingPayslip ? existingPayslip.id : '';
        
        // Update counter
        document.getElementById('employeeCounter').textContent = `Employee ${index + 1} of ${employees.length}`;
        
        // Update navigation buttons
        document.getElementById('prevEmployee').disabled = index === 0;
        document.getElementById('nextEmployee').disabled = index === employees.length - 1;
        
        // Enable action buttons
        document.getElementById('savePayslip').disabled = false;
        document.getElementById('saveAndExportPayslip').disabled = false;
        
        // Load payslip data
        if (existingPayslip) {
            loadExistingPayslip(existingPayslip);
        } else {
            loadDefaultPayslip(employee);
        }
    }

    function loadExistingPayslip(payslip) {
        document.getElementById('payDate').value = payslip.pay_date;
        
        // Clear existing items
        document.getElementById('earningsSection').innerHTML = '';
        document.getElementById('deductionsSection').innerHTML = '';
        
        // Load earnings
        Object.entries(payslip.earnings || {}).forEach(([name, amount]) => {
            addEarningItem(name, amount);
        });
        
        // Load deductions
        Object.entries(payslip.deductions || {}).forEach(([name, amount]) => {
            addDeductionItem(name, amount);
        });
        
        // Update totals
        updateTotals();
    }

    function loadDefaultPayslip(employee) {
        showLoading();
        
        fetch(`/employees/${employee.id}/payslip-defaults`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear existing items
                    document.getElementById('earningsSection').innerHTML = '';
                    document.getElementById('deductionsSection').innerHTML = '';
                    
                    // Load default earnings
                    data.earningsData.forEach(earning => {
                        addEarningItem(earning.name, earning.amount);
                    });
                    
                    // Load default deductions
                    data.deductionsData.forEach(deduction => {
                        addDeductionItem(deduction.name, deduction.amount);
                    });
                    
                    // Update totals
                    updateTotals();
                } else {
                    alert('Error loading payslip defaults: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading payslip defaults');
            })
            .finally(() => {
                hideLoading();
            });
    }

    function addEarningItem(name = '', amount = 0) {
        const earningsSection = document.getElementById('earningsSection');
        const itemId = 'earning_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        
        const itemDiv = document.createElement('div');
        itemDiv.className = 'mb-2 earning-item';
        itemDiv.id = itemId;
        itemDiv.innerHTML = `
            <div class="input-group">
                <input type="text" class="form-control earning-name" placeholder="Earning name..." value="${name}" style="max-width: 40%;">
                <input type="number" name="earnings[${name || 'New Earning'}]" class="form-control earning-input" 
                       value="${amount}" step="0.01" min="0" placeholder="Amount">
                <button type="button" class="btn btn-outline-danger btn-sm remove-earning" title="Remove">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        
        earningsSection.appendChild(itemDiv);
        
        // Add event listeners
        const nameInput = itemDiv.querySelector('.earning-name');
        const amountInput = itemDiv.querySelector('.earning-input');
        const removeBtn = itemDiv.querySelector('.remove-earning');
        
        nameInput.addEventListener('input', function() {
            const newName = this.value || 'New Earning';
            amountInput.name = `earnings[${newName}]`;
        });
        
        amountInput.addEventListener('input', updateTotals);
        
        removeBtn.addEventListener('click', function() {
            itemDiv.remove();
            updateTotals();
        });
        
        updateTotals();
    }

    function addDeductionItem(name = '', amount = 0) {
        const deductionsSection = document.getElementById('deductionsSection');
        const itemId = 'deduction_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        
        const itemDiv = document.createElement('div');
        itemDiv.className = 'mb-2 deduction-item';
        itemDiv.id = itemId;
        itemDiv.innerHTML = `
            <div class="input-group">
                <input type="text" class="form-control deduction-name" placeholder="Deduction name..." value="${name}" style="max-width: 40%;">
                <input type="number" name="deductions[${name || 'New Deduction'}]" class="form-control deduction-input" 
                       value="${amount}" step="0.01" min="0" placeholder="Amount">
                <button type="button" class="btn btn-outline-danger btn-sm remove-deduction" title="Remove">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        
        deductionsSection.appendChild(itemDiv);
        
        // Add event listeners
        const nameInput = itemDiv.querySelector('.deduction-name');
        const amountInput = itemDiv.querySelector('.deduction-input');
        const removeBtn = itemDiv.querySelector('.remove-deduction');
        
        nameInput.addEventListener('input', function() {
            const newName = this.value || 'New Deduction';
            amountInput.name = `deductions[${newName}]`;
        });
        
        amountInput.addEventListener('input', updateTotals);
        
        removeBtn.addEventListener('click', function() {
            itemDiv.remove();
            updateTotals();
        });
        
        updateTotals();
    }

    function attachCalculationListeners() {
        // This function is now handled by individual item event listeners
        // Keep for backward compatibility but functionality moved to add functions
    }

    function updateTotals() {
        let totalEarnings = 0;
        let totalDeductions = 0;
        
        // Calculate total earnings
        document.querySelectorAll('.earning-input').forEach(input => {
            totalEarnings += parseFloat(input.value) || 0;
        });
        
        // Calculate total deductions
        document.querySelectorAll('.deduction-input').forEach(input => {
            totalDeductions += parseFloat(input.value) || 0;
        });
        
        const netPay = totalEarnings - totalDeductions;
        
        // Update display
        document.getElementById('totalEarnings').textContent = totalEarnings.toFixed(2);
        document.getElementById('totalDeductions').textContent = totalDeductions.toFixed(2);
        document.getElementById('netPay').textContent = netPay.toFixed(2);
    }

    function savePayslip(exportAfterSave = false) {
        showLoading();
        
        const formData = new FormData(document.getElementById('payslipForm'));
        const payslipId = document.getElementById('payslipId').value;
        
        // Collect earnings and deductions
        const earnings = {};
        const deductions = {};
        
        console.log('Found earning inputs:', document.querySelectorAll('.earning-input').length);
        console.log('Found deduction inputs:', document.querySelectorAll('.deduction-input').length);
        
        document.querySelectorAll('.earning-input').forEach(input => {
            console.log('Processing earning input:', input.name, input.value);
            const match = input.name.match(/earnings\[(.*?)\]/);
            if (match) {
                const name = match[1];
                const value = parseFloat(input.value) || 0;
                if (value >= 0) { // Include zero values too
                    earnings[name] = value;
                }
            }
        });
        
        document.querySelectorAll('.deduction-input').forEach(input => {
            console.log('Processing deduction input:', input.name, input.value);
            const match = input.name.match(/deductions\[(.*?)\]/);
            if (match) {
                const name = match[1];
                const value = parseFloat(input.value) || 0;
                if (value >= 0) { // Include zero values too
                    deductions[name] = value;
                }
            }
        });
        
        console.log('Collected earnings:', earnings);
        console.log('Collected deductions:', deductions);
        
        const totalEarnings = Object.values(earnings).reduce((sum, val) => sum + val, 0);
        const totalDeductions = Object.values(deductions).reduce((sum, val) => sum + val, 0);
        const netPay = totalEarnings - totalDeductions;
        
        // Ensure we have at least some earnings
        if (Object.keys(earnings).length === 0) {
            showAlert('Please add at least one earning item before saving.', 'warning');
            hideLoading();
            return;
        }
        
        // Ensure we have all required data
        const employeeId = document.getElementById('currentEmployeeId').value;
        const payMonth = document.getElementById('payMonth').value;
        const payDate = document.getElementById('payDate').value;
        const token = document.querySelector('input[name="_token"]').value;
        
        if (!employeeId || !payMonth || !payDate || !token) {
            showAlert('Missing required form data. Please refresh and try again.', 'danger');
            hideLoading();
            return;
        }
        
        const data = {
            employee_id: employeeId,
            pay_month: payMonth,
            pay_date: payDate,
            earnings: earnings,
            deductions: deductions,
            gross_pay: totalEarnings,
            total_deductions: totalDeductions,
            net_pay: netPay,
            _token: token
        };
        
        console.log('Saving payslip data:', data);
        console.log('Payslip ID:', payslipId);
        
        const url = payslipId ? `/payslips/${payslipId}` : '/payslips';
        const method = payslipId ? 'PUT' : 'POST';
        
        if (payslipId) {
            data._method = 'PUT';
        }
        
        console.log('Request URL:', url);
        console.log('Request method:', method);
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': data._token
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Server error response:', text);
                    throw new Error(`HTTP ${response.status}: ${text}`);
                });
            }
            
            return response.json();
        })
        .then(result => {
            console.log('Save result:', result);
            
            if (result.success) {
                // Update payslip ID if it was a new payslip
                if (!payslipId && result.payslip_id) {
                    document.getElementById('payslipId').value = result.payslip_id;
                }
                
                if (exportAfterSave) {
                    // Export the payslip
                    const exportPayslipId = payslipId || result.payslip_id;
                    window.open(`/payslips/${exportPayslipId}/pdf`, '_blank');
                }
                
                // Show success message
                showAlert('Payslip saved successfully!', 'success');
            } else {
                let errorMsg = result.message || result.error || 'Unknown error';
                
                // Handle validation errors
                if (result.errors) {
                    const validationErrors = Object.values(result.errors).flat();
                    errorMsg = validationErrors.join(', ');
                }
                
                console.error('Save failed:', result);
                showAlert('Error saving payslip: ' + errorMsg, 'danger');
            }
        })
        .catch(error => {
            console.error('Save error details:', error);
            showAlert('Error saving payslip: ' + error.message, 'danger');
        })
        .finally(() => {
            hideLoading();
        });
    }

    function showLoading() {
        document.getElementById('loadingOverlay').classList.remove('d-none');
    }

    function hideLoading() {
        document.getElementById('loadingOverlay').classList.add('d-none');
    }

    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 10000; min-width: 300px;';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl + Arrow Left - Previous employee
        if (e.ctrlKey && e.key === 'ArrowLeft') {
            e.preventDefault();
            document.getElementById('prevEmployee').click();
        }
        
        // Ctrl + Arrow Right - Next employee
        if (e.ctrlKey && e.key === 'ArrowRight') {
            e.preventDefault();
            document.getElementById('nextEmployee').click();
        }
        
        // Ctrl + S - Save payslip
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            document.getElementById('savePayslip').click();
        }
        
        // Ctrl + E - Save and Export
        if (e.ctrlKey && e.key === 'e') {
            e.preventDefault();
            document.getElementById('saveAndExportPayslip').click();
        }
    });

    // Add status indicator for saved payslips
    function updateEmployeeStatus() {
        const employee = employees[currentEmployeeIndex];
        const existingPayslip = existingPayslips[employee.id];
        const statusIndicator = document.getElementById('employeeCounter');
        
        if (existingPayslip) {
            statusIndicator.innerHTML += ' <span class="badge bg-success ms-2">Saved</span>';
        } else {
            statusIndicator.innerHTML += ' <span class="badge bg-warning ms-2">New</span>';
        }
    }

    // Update the loadEmployee function to include status
    const originalLoadEmployee = loadEmployee;
    loadEmployee = function(index) {
        originalLoadEmployee(index);
        setTimeout(updateEmployeeStatus, 100); // Small delay to ensure counter is updated
    };
});
</script>
@endsection
