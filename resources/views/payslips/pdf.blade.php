<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payslip - {{ $payslip->employee->fullnames }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 30px;
            font-size: 14px;
        }
        h2, h3 {
            margin: 0;
        }
        .header, .footer {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            max-height: 50px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #333;
            padding: 8px 10px;
            text-align: left;
        }
        th {
            background: #f4f4f4;
        }
        .totals {
            font-weight: bold;
        }
        .section-title {
            background: #e9ecef;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="header">
    <img src="{{ public_path('assets/images/logo-text.png') }}" alt="Company Logo">
    <h2>Company Name</h2>
    <h3>Payslip for {{ \Carbon\Carbon::parse($payslip->pay_date)->format('F, Y') }}</h3>
</div>

<table>
    <tr class="section-title">
        <th colspan="2">Employee Details</th>
    </tr>
    <tr>
        <td>Full Name</td>
        <td>{{ $payslip->employee->fullnames }}</td>
    </tr>
    <tr>
        <td>Employee ID</td>
        <td>{{ $payslip->employee->employee_id }}</td>
    </tr>
    <tr>
        <td>Position</td>
        <td>{{ $payslip->employee->position ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td>Department</td>
        <td>{{ $payslip->employee->department ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td>Pay Method</td>
        <td>{{ $payslip->employee->pay_method ?? 'N/A' }}</td>
    </tr>
</table>

<table>
    <tr class="section-title">
        <th>Earnings</th>
        <th>Amount (ZMW)</th>
    </tr>
    @foreach($payslip->earnings ?? [] as $earning => $amount)
    <tr>
        <td>{{ $earning }}</td>
        <td>{{ number_format($amount, 2) }}</td>
    </tr>
    @endforeach
    <tr class="totals">
        <td>Total Income</td>
        <td>{{ number_format($payslip->gross_pay, 2) }}</td>
    </tr>
</table>

<table>
    <tr class="section-title">
        <th>Deductions</th>
        <th>Amount (ZMW)</th>
    </tr>
    @foreach($payslip->deductions ?? [] as $deduction => $amount)
    <tr>
        <td>{{ $deduction }}</td>
        <td>{{ number_format($amount, 2) }}</td>
    </tr>
    @endforeach
    <tr class="totals">
        <td>Total Deductions</td>
        <td>{{ number_format($payslip->total_deductions, 2) }}</td>
    </tr>
</table>

<table>
    <tr class="totals">
        <td>Net Pay</td>
        <td>{{ number_format($payslip->net_pay, 2) }}</td>
    </tr>
</table>

<div class="footer">
    <p>Generated on {{ \Carbon\Carbon::now()->format('d M Y') }}</p>
    <p>Company Address | Contact Info</p>
</div>

</body>
</html>
