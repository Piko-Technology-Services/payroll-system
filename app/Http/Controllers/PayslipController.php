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
// use App\Exports\PayslipsExport;
// use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class PayslipController extends Controller
{
     public function index(Request $request)
    {
        $query = Payslip::with('employee');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function($q) use ($search) {
                $q->where('fullnames', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('pay_month')) {
            $query->where('pay_month', 'like', "%{$request->pay_month}%");
        }

        if ($request->filled('pay_date_from')) {
            $query->where('pay_date', '>=', $request->pay_date_from);
        }

        if ($request->filled('pay_date_to')) {
            $query->where('pay_date', '<=', $request->pay_date_to);
        }

        if ($request->filled('net_pay_min')) {
            $query->where('net_pay', '>=', $request->net_pay_min);
        }

        if ($request->filled('net_pay_max')) {
            $query->where('net_pay', '<=', $request->net_pay_max);
        }

        $payslips = $query->orderBy('pay_date', 'desc')->get();

        $employees = Employee::all();
        $earningRules = EarningRule::all();
        $deductionRules = DeductionRule::all();
        $defaultEarnings = DefaultEarning::active()->ordered()->get();
        $defaultDeductions = DefaultDeduction::active()->ordered()->get();

        // Get unique pay months for filter dropdown
        $payMonths = Payslip::distinct()->pluck('pay_month')->filter()->sort()->reverse();

        return view('payslips.index', compact('payslips', 'employees', 'earningRules', 'deductionRules', 'defaultEarnings', 'defaultDeductions', 'payMonths'));
    }

    /**
     * Get payslip defaults for an employee
     * Uses reverse calculation to determine Gross Pay from Net Pay (salary_rate)
     */
    public function getPayslipDefaults($employeeId)
    {
        try {
            $employee = Employee::findOrFail($employeeId);
            
            // Get default earnings with metadata
            $defaultEarnings = \App\Models\DefaultEarning::active()->ordered()->get();
            $earnings = [];
            $earningsData = [];
            
            // If no default earnings exist, create basic ones
            if ($defaultEarnings->isEmpty()) {
                $earnings['Basic Pay'] = $employee->salary_rate;
                $earningsData[] = [
                    'name' => 'Basic Pay',
                    'amount' => $employee->salary_rate,
                    'type' => 'fixed',
                    'description' => 'Basic monthly salary',
                    'enabled' => true
                ];
            } else {
                foreach ($defaultEarnings as $earning) {
                    if ($earning->name === 'Basic Pay') {
                        $amount = $employee->salary_rate;
                    } else {
                        $amount = $earning->calculateAmount($employee->salary_rate);
                    }
                    
                    $earnings[$earning->name] = $amount;
                    $earningsData[] = [
                        'name' => $earning->name,
                        'amount' => $amount,
                        'type' => $earning->type,
                        'description' => $earning->description,
                        'enabled' => true // Default to enabled
                    ];
                }
            }
            
            // Get default deductions with metadata
            $defaultDeductions = \App\Models\DefaultDeduction::active()->ordered()->get();
            $deductions = [];
            $deductionsData = [];
            $grossPay = array_sum($earnings);
            
            // If no default deductions exist, create basic ones
            if ($defaultDeductions->isEmpty()) {
                // Create basic statutory deductions
                $paye = $this->calculateBasicPAYE($grossPay);
                $napsa = round($grossPay * 0.05, 2);
                $nhis = round($grossPay * 0.02, 2);
                
                $deductions['PAYE'] = $paye;
                $deductions['NAPSA'] = $napsa;
                $deductions['NHIS'] = $nhis;
                
                $deductionsData[] = [
                    'name' => 'PAYE',
                    'amount' => $paye,
                    'type' => 'percentage',
                    'description' => 'Pay As You Earn Tax',
                    'is_statutory' => true,
                    'enabled' => true
                ];
                $deductionsData[] = [
                    'name' => 'NAPSA',
                    'amount' => $napsa,
                    'type' => 'percentage',
                    'description' => 'National Pension Scheme Authority',
                    'is_statutory' => true,
                    'enabled' => true
                ];
                $deductionsData[] = [
                    'name' => 'NHIS',
                    'amount' => $nhis,
                    'type' => 'percentage',
                    'description' => 'National Health Insurance Scheme',
                    'is_statutory' => true,
                    'enabled' => true
                ];
            } else {
                foreach ($defaultDeductions as $deduction) {
                    if ($deduction->is_statutory) {
                        // Handle statutory deductions with special calculations
                        switch ($deduction->name) {
                            case 'PAYE':
                                $amount = $this->calculateBasicPAYE($grossPay);
                                break;
                            case 'NAPSA':
                                $amount = round($grossPay * 0.05, 2);
                                break;
                            case 'NHIS':
                                $amount = round($grossPay * 0.02, 2);
                                break;
                            default:
                                $amount = $deduction->calculateAmount($grossPay);
                        }
                    } else {
                        $amount = $deduction->calculateAmount($grossPay);
                    }
                    
                    $deductions[$deduction->name] = $amount;
                    $deductionsData[] = [
                        'name' => $deduction->name,
                        'amount' => $amount,
                        'type' => $deduction->type,
                        'description' => $deduction->description,
                        'is_statutory' => $deduction->is_statutory,
                        'enabled' => true // Default to enabled
                    ];
                }
            }
            
            $totalDeductions = array_sum($deductions);
            $netPay = $grossPay - $totalDeductions;

            return response()->json([
                'success' => true,
                'earnings' => $earnings,
                'deductions' => $deductions,
                'earningsData' => $earningsData,
                'deductionsData' => $deductionsData,
                'gross_pay' => round($grossPay, 2),
                'total_deductions' => round($totalDeductions, 2),
                'net_pay' => round($netPay, 2),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching employee defaults: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate basic PAYE tax (simplified calculation)
     */
    private function calculateBasicPAYE($grossPay)
    {
        // Basic PAYE calculation for Zambia (simplified)
        if ($grossPay <= 4000) {
            return 0; // Tax-free threshold
        } elseif ($grossPay <= 6000) {
            return round(($grossPay - 4000) * 0.25, 2); // 25% on excess over 4000
        } else {
            return round(500 + ($grossPay - 6000) * 0.30, 2); // 500 + 30% on excess over 6000
        }
    }

    public function create()
    {
        $employees = Employee::all();
        return view('payslips.create', compact('employees'));
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'pay_month' => 'required|string',
                'pay_date' => 'required|date',
                'earnings' => 'required|array',
                'deductions' => 'array',
                'gross_pay' => 'required|numeric|min:0',
                'total_deductions' => 'required|numeric|min:0',
                'net_pay' => 'required|numeric|min:0',
            ]);
            
            $payslip = Payslip::create([
                'employee_id' => $data['employee_id'],
                'pay_month' => $data['pay_month'],
                'pay_date' => $data['pay_date'],
                'earnings' => $data['earnings'],
                'deductions' => $data['deductions'] ?? [],
                'gross_pay' => $data['gross_pay'],
                'total_deductions' => $data['total_deductions'],
                'net_pay' => $data['net_pay'],
            ]);
            
            // Handle JSON requests (from monthly review)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payslip created successfully.',
                    'payslip_id' => $payslip->id
                ]);
            }
            
            return redirect()->route('payslips.index')->with('success', 'Payslip created successfully.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating payslip: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    public function edit($id)
    {
        $payslip = Payslip::findOrFail($id);
        $employees = Employee::all();
        return view('payslips.edit', compact('payslip', 'employees'));
    }

    public function update(Request $request, $id)
    {
        try {
            $payslip = Payslip::findOrFail($id);
            $data = $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'pay_month' => 'required|string',
                'pay_date' => 'required|date',
                'earnings' => 'required|array',
                'deductions' => 'array',
                'gross_pay' => 'required|numeric|min:0',
                'total_deductions' => 'required|numeric|min:0',
                'net_pay' => 'required|numeric|min:0',
            ]);
            
            $payslip->update([
                'employee_id' => $data['employee_id'],
                'pay_month' => $data['pay_month'],
                'pay_date' => $data['pay_date'],
                'earnings' => $data['earnings'],
                'deductions' => $data['deductions'] ?? [],
                'gross_pay' => $data['gross_pay'],
                'total_deductions' => $data['total_deductions'],
                'net_pay' => $data['net_pay'],
            ]);
            
            // Handle JSON requests (from monthly review)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payslip updated successfully.',
                    'payslip_id' => $payslip->id
                ]);
            }
            
            return redirect()->route('payslips.index')->with('success', 'Payslip updated successfully.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating payslip: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    public function destroy($id)
    {
        Payslip::destroy($id);
        return back()->with('success', 'Payslip deleted.');
    }

    public function downloadPdf($id)
    {
        try {
            $payslip = Payslip::with('employee')->findOrFail($id);
            $pdf = Pdf::loadView('payslips.pdf', compact('payslip'));
            $pdf->setPaper('A4', 'portrait');
            
            $filename = "Payslip_" . str_replace(' ', '_', $payslip->employee->fullnames) . "_" . $payslip->pay_month . ".pdf";
            return $pdf->download($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Error generating PDF: ' . $e->getMessage());
        }
    }

    public function exportCsv()
    {
        $payslips = Payslip::with('employee')->get();
        
        $filename = 'payslips_' . date('Y_m_d_H_i_s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($payslips) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'Employee Name',
                'Employee ID', 
                'Pay Month',
                'Pay Date',
                'Gross Pay',
                'Total Deductions',
                'Net Pay',
                'Position',
                'Department'
            ]);
            
            // Add data rows
            foreach ($payslips as $payslip) {
                fputcsv($file, [
                    $payslip->employee->fullnames ?? 'N/A',
                    $payslip->employee->employee_id ?? 'N/A',
                    $payslip->pay_month,
                    $payslip->pay_date,
                    $payslip->gross_pay,
                    $payslip->total_deductions,
                    $payslip->net_pay,
                    $payslip->employee->position ?? 'N/A',
                    $payslip->employee->department ?? 'N/A'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function exportAllPdf(Request $request)
    {
        try {
            $query = Payslip::with('employee');
            
            // Apply filters if provided
            if ($request->filled('month')) {
                $query->where('pay_month', $request->month);
            }
            
            if ($request->filled('employee_id')) {
                $query->where('employee_id', $request->employee_id);
            }
            
            $payslips = $query->orderBy('pay_date', 'desc')->get();
            
            if ($payslips->isEmpty()) {
                return back()->with('error', 'No payslips found to export.');
            }
            
            $filename = 'All_Payslips';
            if ($request->filled('month')) {
                $filename = 'Payslips_' . str_replace('-', '_', $request->month);
            }
            
            $pdf = Pdf::loadView('payslips.all_pdf', compact('payslips'));
            $pdf->setPaper('A4', 'portrait');
            
            return $pdf->download($filename . '.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Error generating PDF: ' . $e->getMessage());
        }
    }

    public function exportMonthlyPdf(Request $request)
    {
        try {
            $request->validate([
                'month' => 'required|string'
            ]);

            $month = $request->month;
            $payslips = Payslip::with('employee')
                ->where('pay_month', $month)
                ->orderBy('pay_date', 'desc')
                ->get();

            if ($payslips->isEmpty()) {
                return back()->with('error', 'No payslips found for the selected month.');
            }

            $filename = 'Payslips_' . str_replace('-', '_', $month);
            $pdf = Pdf::loadView('payslips.all_pdf', compact('payslips'));
            $pdf->setPaper('A4', 'portrait');
            
            return $pdf->download($filename . '.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Error generating monthly PDF: ' . $e->getMessage());
        }
    }

    public function monthlyReview(Request $request)
    {
        $selectedMonth = $request->get('month', now()->format('Y-m'));
        
        // Get all employees
        $employees = Employee::orderBy('fullnames')->get();
        
        // Get existing payslips for the selected month
        $existingPayslips = Payslip::with('employee')
            ->where('pay_month', $selectedMonth)
            ->get()
            ->keyBy('employee_id');
        
        // Get unique pay months for dropdown
        $payMonths = Payslip::distinct()->pluck('pay_month')->filter()->sort()->reverse();
        if (!$payMonths->contains($selectedMonth)) {
            $payMonths->prepend($selectedMonth);
        }
        
        // Get default earnings and deductions
        $defaultEarnings = DefaultEarning::active()->ordered()->get();
        $defaultDeductions = DefaultDeduction::active()->ordered()->get();
        
        return view('payslips.monthly_payslips', compact(
            'employees', 
            'existingPayslips', 
            'selectedMonth', 
            'payMonths',
            'defaultEarnings',
            'defaultDeductions'
        ));
    }

    public function testPdf()
    {
        try {
            $payslips = Payslip::with('employee')->take(1)->get();
            
            if ($payslips->isEmpty()) {
                return response('No payslips found for testing', 404);
            }
            
            $pdf = Pdf::loadView('payslips.all_pdf', compact('payslips'));
            $pdf->setPaper('A4', 'portrait');
            
            return $pdf->stream('test_payslip.pdf');
        } catch (\Exception $e) {
            return response('PDF Test Error: ' . $e->getMessage(), 500);
        }
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
