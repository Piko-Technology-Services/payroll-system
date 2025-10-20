<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Payslips</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        h2 {
            margin-bottom: 5px;
        }
        h3 {
            margin-top: 0;
            margin-bottom: 10px;
            font-weight: normal;
        }
        .payslip {
            page-break-after: always;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #333;
            padding: 5px 8px;
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
        .header, .footer {
            text-align: center;
        }
        .header img {
            max-height: 40px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

@foreach($payslips as $payslip)
<div class="payslip">
    <div class="header">
        @if(file_exists(public_path('assets/images/logo-text.png')))
            <img src="{{ public_path('assets/images/logo-text.png') }}" alt="Company Logo">
        @endif
        <h2>Best Choice Trading and Manufacturing Limited</h2>
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
        @if($payslip->earnings)
            @foreach($payslip->earnings as $earning => $amount)
                <tr>
                    <td>{{ $earning }}</td>
                    <td>{{ number_format($amount, 2) }}</td>
                </tr>
            @endforeach
        @endif
        <tr class="totals">
            <td>Total Earnings</td>
            <td>{{ number_format($payslip->gross_pay ?? 0, 2) }}</td>
        </tr>
    </table>

    <table>
        <tr class="section-title">
            <th>Deductions</th>
            <th>Amount (ZMW)</th>
        </tr>
        @if($payslip->deductions)
            @foreach($payslip->deductions as $deduction => $amount)
                <tr>
                    <td>{{ $deduction }}</td>
                    <td>{{ number_format($amount, 2) }}</td>
                </tr>
            @endforeach
        @endif
        <tr class="totals">
            <td>Total Deductions</td>
            <td>{{ number_format($payslip->total_deductions ?? 0, 2) }}</td>
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
</div>
@endforeach

</body>
</html>
