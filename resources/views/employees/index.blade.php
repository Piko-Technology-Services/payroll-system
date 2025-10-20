@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Employees</h4>
        <div>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                <i class="bi bi-plus-circle me-1"></i> Add Employee
            </button>
            <form action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data" class="d-inline">
                @csrf
                <input type="file" name="file" class="d-none" id="employeeFile" accept=".csv, .xlsx" onchange="this.form.submit()">
                <label for="employeeFile" class="btn btn-secondary btn-sm"><i class="bi bi-upload"></i> Import</label>
            </form>
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
                                            <button type="submit" class="btn btn-success">Save Changes</button>
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
                    <button class="btn btn-primary">Save Employee</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
