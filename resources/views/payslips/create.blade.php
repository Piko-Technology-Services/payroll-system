@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Create Payslip</h4>
        <a href="{{ route('payslips.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Payslips
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('payslips.store') }}" method="POST">
                @csrf
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Employee</label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">-- Select Employee --</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->fullnames }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Pay Month</label>
                        <input type="text" name="pay_month" class="form-control" required>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Pay Date</label>
                        <input type="date" name="pay_date" class="form-control" required>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row g-3">
                    <div class="col-md-6">
                        <h6>Earnings</h6>
                        <div class="mb-3">
                            <label class="form-label">Basic Pay</label>
                            <input type="number" step="0.01" name="earnings[Basic Pay]" class="form-control" value="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lunch Allowance</label>
                            <input type="number" step="0.01" name="earnings[Lunch Allowance]" class="form-control" value="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Transport Allowance</label>
                            <input type="number" step="0.01" name="earnings[Transport Allowance]" class="form-control" value="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Housing Allowance</label>
                            <input type="number" step="0.01" name="earnings[Housing Allowance]" class="form-control" value="0">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <h6>Deductions</h6>
                        <div class="mb-3">
                            <label class="form-label">PAYE</label>
                            <input type="number" step="0.01" name="deductions[PAYE]" class="form-control" value="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">NAPSA</label>
                            <input type="number" step="0.01" name="deductions[NAPSA]" class="form-control" value="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Personal Levy</label>
                            <input type="number" step="0.01" name="deductions[Personal Levy]" class="form-control" value="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">NHIS</label>
                            <input type="number" step="0.01" name="deductions[NHIS]" class="form-control" value="0">
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Create Payslip</button>
                    <a href="{{ route('payslips.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
