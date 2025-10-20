<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payslip;
use App\Models\Employee;
use App\Services\PayslipCalculator;
use App\Models\EarningRule;
use App\Models\DeductionRule;
use App\Models\DefaultEarning;
use App\Models\DefaultDeduction;
use App\Exports\PayslipsExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Illuminate\Support\Facades\DB;

class PayslipController extends Controller
{
     public function index()
    {
        $payslips = Payslip::with('employee')->get();
        $employees = Employee::all();
        $earningRules = EarningRule::all();
        $deductionRules = DeductionRule::all();
        $defaultEarnings = DefaultEarning::active()->ordered()->get();
        $defaultDeductions = DefaultDeduction::active()->ordered()->get();

        return view('payslips.index', compact('payslips', 'employees', 'earningRules', 'deductionRules', 'defaultEarnings', 'defaultDeductions'));
    }

    /**
     * Get payslip defaults for an employee
     * Uses reverse calculation to determine Gross Pay from Net Pay (salary_rate)
     */
    public function getPayslipDefaults($employeeId)
    {
        try {
            $employee = Employee::findOrFail($employeeId);
            
            // Use reverse calculation to get Gross Pay from Net Pay (salary_rate)
            $calc = PayslipCalculator::reverseCalculate($employee->salary_rate);

            return response()->json([
                'success' => true,
                'earnings' => $calc['earnings'],
                'deductions' => $calc['deductions'],
                'gross_pay' => $calc['gross_pay'],
                'total_deductions' => $calc['total_deductions'],
                'net_pay' => $calc['net_pay'],
                'converged' => $calc['converged'],
                'iterations' => $calc['iterations'] ?? 0,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching employee defaults: ' . $e->getMessage()
            ], 500);
        }
    }


    public function create()
    {
        $employees = Employee::all();
        return view('payslips.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'pay_month' => 'required|string',
            'pay_date' => 'required|date',
            'earnings' => 'required|array',
            'deductions' => 'array',
        ]);
        $employee = Employee::findOrFail($data['employee_id']);
        $calc = \App\Services\PayslipCalculator::calculate(
            $data['earnings'],
            $data['deductions'] ?? [],
            $employee
        );
        Payslip::create([
            'employee_id' => $data['employee_id'],
            'pay_month' => $data['pay_month'],
            'pay_date' => $data['pay_date'],
            'earnings' => $calc['earnings'],
            'deductions' => $calc['deductions'],
            'gross_pay' => $calc['gross_pay'],
            'net_pay' => $calc['net_pay'],
        ]);
        return redirect()->route('payslips.index')->with('success', 'Payslip created successfully.');
    }

    public function edit($id)
    {
        $payslip = Payslip::findOrFail($id);
        $employees = Employee::all();
        return view('payslips.edit', compact('payslip', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $payslip = Payslip::findOrFail($id);
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'pay_month' => 'required|string',
            'pay_date' => 'required|date',
            'earnings' => 'required|array',
            'deductions' => 'array',
        ]);
        $employee = Employee::findOrFail($data['employee_id']);
        $calc = \App\Services\PayslipCalculator::calculate(
            $data['earnings'],
            $data['deductions'] ?? [],
            $employee
        );
        $payslip->update([
            'employee_id' => $data['employee_id'],
            'pay_month' => $data['pay_month'],
            'pay_date' => $data['pay_date'],
            'earnings' => $calc['earnings'],
            'deductions' => $calc['deductions'],
            'gross_pay' => $calc['gross_pay'],
            'net_pay' => $calc['net_pay'],
        ]);
        return redirect()->route('payslips.index')->with('success', 'Payslip updated successfully.');
    }

    public function destroy($id)
    {
        Payslip::destroy($id);
        return back()->with('success', 'Payslip deleted.');
    }

    public function downloadPdf($id)
    {
        $payslip = Payslip::with('employee')->findOrFail($id);
        $pdf = PDF::loadView('payslips.pdf', compact('payslip'));
        return $pdf->download("Payslip_{$payslip->employee->fullnames}.pdf");
    }

    public function exportCsv()
    {
        return Excel::download(new PayslipsExport, 'payslips.csv');
    }

    public function exportAllPdf()
    {
        $payslips = Payslip::with('employee')->get();
        $pdf = PDF::loadView('payslips.all_pdf', compact('payslips'));
        return $pdf->download('All_Payslips.pdf');
    }

    /**
     * Generate payslips for all employees using their salary_rate as Net Pay
     * This method reverse-calculates Gross Pay and statutory deductions
     */
    public function generateAllFromNetPay(Request $request)
    {
        $request->validate([
            'pay_month' => 'required|string',
            'pay_date' => 'required|date',
            'lunch_allowance' => 'nullable|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'housing_allowance' => 'nullable|numeric|min:0',
        ]);

        $period = $request->input('pay_month');
        $payDate = $request->input('pay_date');
        $allowances = [
            'Lunch Allowance' => $request->input('lunch_allowance', 0),
            'Transport Allowance' => $request->input('transport_allowance', 0),
            'Housing Allowance' => $request->input('housing_allowance', 0),
        ];

        $employees = Employee::all();
        $created = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($employees as $employee) {
                // Check if payslip already exists for this period
                $exists = Payslip::where('employee_id', $employee->id)
                    ->where('pay_month', $period)
                    ->exists();
                
                if ($exists) {
                    $skipped++;
                    continue;
                }

                // Skip employees with zero or negative salary
                if ($employee->salary_rate <= 0) {
                    $errors[] = "Employee {$employee->fullnames} has invalid salary rate: {$employee->salary_rate}";
                    continue;
                }

                // Use reverse calculation to get Gross Pay from Net Pay (salary_rate)
                $calc = PayslipCalculator::reverseCalculate($employee->salary_rate, $allowances);

                // Validate calculation converged
                if (!$calc['converged']) {
                    $errors[] = "Failed to converge calculation for employee {$employee->fullnames}";
                    continue;
                }

                // Create payslip
                Payslip::create([
                    'employee_id' => $employee->id,
                    'pay_month' => $period,
                    'pay_date' => $payDate,
                    'earnings' => $calc['earnings'],
                    'deductions' => $calc['deductions'],
                    'gross_pay' => $calc['gross_pay'],
                    'total_deductions' => $calc['total_deductions'],
                    'net_pay' => $calc['net_pay'],
                ]);
                
                $created++;
            }

            DB::commit();

            $message = "Successfully generated {$created} payslips, {$skipped} skipped (already exist).";
            if (!empty($errors)) {
                $message .= " Errors: " . implode('; ', $errors);
            }

            return redirect()->route('payslips.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('payslips.index')
                ->with('error', 'Error generating payslips: ' . $e->getMessage());
        }
    }

    /**
     * Legacy method - kept for backward compatibility
     * Generate payslips using salary_rate as Gross Pay (old method)
     */
    public function generateAll(Request $request)
    {
        $period = $request->input('pay_month') ?? now()->format('Y-m');
        $employees = Employee::all();
        $created = 0;
        $skipped = 0;
        DB::beginTransaction();
        try {
            foreach ($employees as $employee) {
                $exists = Payslip::where('employee_id', $employee->id)
                    ->where('pay_month', $period)
                    ->exists();
                if ($exists) {
                    $skipped++;
                    continue;
                }
                $earnings = [
                    'Basic Pay' => $employee->salary_rate,
                    'Lunch Allowance' => 0,
                    'Transport Allowance' => 0,
                    'Housing Allowance' => 0,
                ];
                $deductions = [];
                $deductions['PAYE'] = PayslipCalculator::calculatePAYE($employee->salary_rate);
                $deductions['NAPSA'] = round($employee->salary_rate * 0.05, 2);
                $deductions['Personal Levy'] = 3;
                $deductions['NHIS'] = round($employee->salary_rate * 0.02, 2);
                $grossPay = array_sum($earnings);
                $totalDeductions = array_sum($deductions);
                $netPay = $grossPay - $totalDeductions;
                Payslip::create([
                    'employee_id' => $employee->id,
                    'pay_month' => $period,
                    'pay_date' => now()->format('Y-m-d'),
                    'earnings' => $earnings,
                    'deductions' => $deductions,
                    'gross_pay' => $grossPay,
                    'total_deductions' => $totalDeductions,
                    'net_pay' => $netPay,
                ]);
                $created++;
            }
            DB::commit();
            return redirect()->route('payslips.index')->with('success', "$created payslips generated, $skipped skipped (already exist for this period).");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('payslips.index')->with('error', 'Error generating payslips: ' . $e->getMessage());
        }
    }
}
