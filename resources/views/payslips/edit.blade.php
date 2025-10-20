@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Edit Payslip</h4>
        <a href="{{ route('payslips.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Payslips
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('payslips.update', $payslip->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Employee</label>
                        <select name="employee_id" class="form-select" required>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ $payslip->employee_id == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->fullnames }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Pay Month</label>
                        <input type="text" name="pay_month" class="form-control" value="{{ $payslip->pay_month }}" required>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Pay Date</label>
                        <input type="date" name="pay_date" class="form-control" value="{{ $payslip->pay_date }}" required>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row g-3">
                    <div class="col-md-6">
                        <h6>Earnings</h6>
                        @foreach($payslip->earnings ?? [] as $earning => $amount)
                        <div class="mb-3">
                            <label class="form-label">{{ $earning }}</label>
                            <input type="number" step="0.01" name="earnings[{{ $earning }}]" 
                                   class="form-control" value="{{ $amount }}">
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="col-md-6">
                        <h6>Deductions</h6>
                        @foreach($payslip->deductions ?? [] as $deduction => $amount)
                        <div class="mb-3">
                            <label class="form-label">{{ $deduction }}</label>
                            <input type="number" step="0.01" name="deductions[{{ $deduction }}]" 
                                   class="form-control" value="{{ $amount }}">
                        </div>
                        @endforeach
                    </div>
                </div>

                <hr class="my-4">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Gross Pay</label>
                        <input type="number" step="0.01" class="form-control" value="{{ $payslip->gross_pay }}" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Total Deductions</label>
                        <input type="number" step="0.01" class="form-control" value="{{ $payslip->total_deductions }}" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Net Pay</label>
                        <input type="number" step="0.01" class="form-control" value="{{ $payslip->net_pay }}" readonly>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Update Payslip</button>
                    <a href="{{ route('payslips.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
