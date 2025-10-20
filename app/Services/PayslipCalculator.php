<?php

namespace App\Services;

use App\Models\DefaultEarning;
use App\Models\DefaultDeduction;

class PayslipCalculator
{
    /**
     * Calculate payroll for an employee using database-driven defaults.
     * Standard calculation: Gross Pay = Basic Pay + Allowances + Optional Earnings
     * Net Pay = Gross Pay - Total Deductions
     *
     * @param array $inputEarnings Optional earnings from form
     * @param array $inputDeductions Optional deductions from form
     * @param \App\Models\Employee $employee The employee model
     * @return array
     */
    public static function calculate(array $inputEarnings, array $inputDeductions, $employee)
    {
        // 1. Get default earnings from database
        $defaultEarnings = DefaultEarning::active()->ordered()->get();
        
        // 2. Calculate earnings
        $earnings = [];
        $basicPay = $employee->salary_rate; // Employee's basic salary
        
        foreach ($defaultEarnings as $defaultEarning) {
            if ($defaultEarning->name === 'Basic Pay') {
                $earnings[$defaultEarning->name] = $basicPay;
            } else {
                // Calculate allowance based on type (fixed or percentage)
                $amount = $defaultEarning->calculateAmount($basicPay);
                $earnings[$defaultEarning->name] = $amount;
            }
        }
        
        // Add optional earnings (do not overwrite defaults)
        foreach ($inputEarnings as $key => $value) {
            if (!array_key_exists($key, $earnings)) {
                $earnings[$key] = (float) $value;
            }
        }
        
        // Ensure all earnings are numeric
        $earnings = array_map(function($value) {
            return is_numeric($value) ? (float) $value : 0.0;
        }, $earnings);
        
        $grossPay = array_sum($earnings);

        // 3. Get default deductions from database
        $defaultDeductions = DefaultDeduction::active()->ordered()->get();
        
        // 4. Calculate deductions
        $deductions = [];
        
        foreach ($defaultDeductions as $defaultDeduction) {
            if ($defaultDeduction->is_statutory) {
                // Handle statutory deductions with special calculations
                switch ($defaultDeduction->name) {
                    case 'PAYE':
                        $deductions['PAYE'] = self::calculatePAYE($grossPay);
                        break;
                    case 'NAPSA':
                        $deductions['NAPSA'] = round($grossPay * 0.05, 2);
                        break;
                    case 'NHIS':
                        $deductions['NHIS'] = round($grossPay * 0.02, 2);
                        break;
                    default:
                        $deductions[$defaultDeduction->name] = $defaultDeduction->calculateAmount($grossPay);
                }
            } else {
                // Regular deductions
                $deductions[$defaultDeduction->name] = $defaultDeduction->calculateAmount($grossPay);
            }
        }
        
        // Add optional deductions (do not overwrite defaults)
        foreach ($inputDeductions as $key => $value) {
            if (!array_key_exists($key, $deductions)) {
                $deductions[$key] = (float) $value;
            }
        }
        
        // Ensure all deductions are numeric
        $deductions = array_map(function($value) {
            return is_numeric($value) ? (float) $value : 0.0;
        }, $deductions);
        
        $totalDeductions = array_sum($deductions);
        $netPay = $grossPay - $totalDeductions;

        // Ensure net pay is not negative
        if ($netPay < 0) {
            $netPay = 0;
        }

        return [
            'earnings' => $earnings,
            'deductions' => $deductions,
            'gross_pay' => round($grossPay, 2),
            'total_deductions' => round($totalDeductions, 2),
            'net_pay' => round($netPay, 2)
        ];
    }

    /**
     * Reverse calculate Gross Pay from Net Pay using iterative method
     * Uses database-driven default deductions for accurate calculation
     * 
     * @param float $netPay The desired net pay amount
     * @param array $allowances Array of allowances (Lunch, Transport, Housing)
     * @return array Complete payslip calculation
     */
    public static function reverseCalculate($netPay, $allowances = [])
    {
        // Get default deductions from database
        $defaultDeductions = DefaultDeduction::active()->ordered()->get();
        
        // Start with an initial guess for gross pay
        $grossPay = $netPay * 1.3; // Start with 30% higher than net pay
        $tolerance = 0.01; // 1 cent tolerance
        $maxIterations = 100;
        $iteration = 0;
        
        do {
            $iteration++;
            
            // Calculate deductions based on current gross pay using database defaults
            $deductions = [];
            
            foreach ($defaultDeductions as $defaultDeduction) {
                if ($defaultDeduction->is_statutory) {
                    // Handle statutory deductions with special calculations
                    switch ($defaultDeduction->name) {
                        case 'PAYE':
                            $deductions['PAYE'] = self::calculatePAYE($grossPay);
                            break;
                        case 'NAPSA':
                            $deductions['NAPSA'] = round($grossPay * 0.05, 2);
                            break;
                        case 'NHIS':
                            $deductions['NHIS'] = round($grossPay * 0.02, 2);
                            break;
                        default:
                            $deductions[$defaultDeduction->name] = $defaultDeduction->calculateAmount($grossPay);
                    }
                } else {
                    // Regular deductions
                    $deductions[$defaultDeduction->name] = $defaultDeduction->calculateAmount($grossPay);
                }
            }
            
            $totalDeductions = array_sum($deductions);
            $calculatedNetPay = $grossPay - $totalDeductions;
            
            // Check if we're close enough
            $difference = abs($calculatedNetPay - $netPay);
            
            if ($difference <= $tolerance) {
                break;
            }
            
            // Adjust gross pay based on the difference
            $adjustment = ($netPay - $calculatedNetPay) * 1.1; // 1.1 multiplier for faster convergence
            $grossPay += $adjustment;
            
            // Ensure gross pay doesn't go negative
            if ($grossPay < 0) {
                $grossPay = $netPay;
            }
            
        } while ($iteration < $maxIterations && $difference > $tolerance);
        
        // Calculate earnings using database defaults
        $defaultEarnings = DefaultEarning::active()->ordered()->get();
        $earnings = [];
        
        foreach ($defaultEarnings as $defaultEarning) {
            if ($defaultEarning->name === 'Basic Pay') {
                // Calculate Basic Pay as Gross Pay minus allowances
                $totalAllowances = 0;
                foreach ($allowances as $allowanceName => $allowanceValue) {
                    $totalAllowances += $allowanceValue;
                }
                $earnings['Basic Pay'] = max(0, $grossPay - $totalAllowances);
            } else {
                // Use provided allowances or calculate from defaults
                if (isset($allowances[$defaultEarning->name])) {
                    $earnings[$defaultEarning->name] = $allowances[$defaultEarning->name];
                } else {
                    $earnings[$defaultEarning->name] = $defaultEarning->calculateAmount($grossPay);
                }
            }
        }
        
        // Ensure Basic Pay is not negative
        if ($earnings['Basic Pay'] < 0) {
            $earnings['Basic Pay'] = 0;
            $grossPay = array_sum($earnings);
            
            // Recalculate deductions with adjusted gross pay
            $deductions = [];
            foreach ($defaultDeductions as $defaultDeduction) {
                if ($defaultDeduction->is_statutory) {
                    switch ($defaultDeduction->name) {
                        case 'PAYE':
                            $deductions['PAYE'] = self::calculatePAYE($grossPay);
                            break;
                        case 'NAPSA':
                            $deductions['NAPSA'] = round($grossPay * 0.05, 2);
                            break;
                        case 'NHIS':
                            $deductions['NHIS'] = round($grossPay * 0.02, 2);
                            break;
                        default:
                            $deductions[$defaultDeduction->name] = $defaultDeduction->calculateAmount($grossPay);
                    }
                } else {
                    $deductions[$defaultDeduction->name] = $defaultDeduction->calculateAmount($grossPay);
                }
            }
            $totalDeductions = array_sum($deductions);
        }
        
        return [
            'earnings' => $earnings,
            'deductions' => $deductions,
            'gross_pay' => round($grossPay, 2),
            'total_deductions' => round($totalDeductions, 2),
            'net_pay' => round($netPay, 2),
            'iterations' => $iteration,
            'converged' => $iteration < $maxIterations
        ];
    }

    /**
     * ZRA PAYE calculation for 2025
     */
    public static function calculatePAYE($basicPay)
    {
        if ($basicPay <= 4400) {
            return 0;
        } elseif ($basicPay <= 4500) {
            return ($basicPay - 4400) * 0.25;
        } elseif ($basicPay <= 5000) {
            return (100 * 0.25) + (($basicPay - 4500) * 0.30);
        } else {
            return (100 * 0.25) + (500 * 0.30) + (($basicPay - 5000) * 0.375);
        }
    }
}
