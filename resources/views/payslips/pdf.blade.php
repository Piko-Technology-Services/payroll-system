<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payslip - {{ $payslip->employee->fullnames }}</title>
    <style>
        body {
            font-family: 'Consolas', monospace;
            margin: 30px;
            font-size: 12px;
        }
        h2, h3 {
            margin: 0;
        }
        .header, .footer {
            text-align: center;
            margin-bottom: 10px;
        }
        .header img {
            max-height: 50px;
            margin-bottom: 5px;
        }
        .company-info {
            font-size: 10px;
            margin-bottom: 15px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
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
            font-weight: bold;
            background: #e9ecef;
        }
        .footer {
            margin-top: 20px;
            font-size: 10px;
        }
        .signatures {
            margin-top: 30px;
            width: 100%;
        }
        .signatures td {
            border: none;
            text-align: center;
            padding-top: 30px;
        }
    </style>
</head>
<body>

<div class="header">
    <img src="{{ public_path('assets/images/logo-text.png') }}" alt="Company Logo">
    <h2>Best Choice Trading and Manufacturing Limited</h2>
    <h3>Payslip for {{ \Carbon\Carbon::parse($payslip->pay_date)->format('F Y') }}</h3>
</div>

<div class="company-info">
    <p>
        MANUFACTURING LTD | CELL: +260 772809898 / +260 975232444<br>
        EMAIL: info@bestchoicezambia.com / sohel@bestchoicezambia.com<br>
        PLOT: 10096/7 Off Mumbwa Rd., Chinika Industrial Area<br>
        Private Bag: E891-15, Post.Net Lusaka-Zambia
    </p>
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
    <tr>
        <td>Bank Name/Branch</td>
        <td>{{ $payslip->employee->bank_acc_number ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td>Date Engaged</td>
        <td>{{ $payslip->employee->date_engaged }}</td>
    </tr>
    <tr>
        <td>TPIN</td>
        <td>{{ $payslip->employee->tpin ?? 'N/A' }}</td>
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
        <td>Total Earnings</td>
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
        <td>Net Pay (Rounded)</td>
        <td>{{ number_format($payslip->net_pay, 2) }}</td>
    </tr>
</table>

<table class="signatures">
    <tr>
        <td>_________________________<br>Employer's Signature</td>
        <td>_________________________<br>Employee's Signature</td>
    </tr>
</table>

<div class="footer">
    <p>Generated on {{ \Carbon\Carbon::now()->format('d M Y') }}</p>
</div>

</body>
</html>
