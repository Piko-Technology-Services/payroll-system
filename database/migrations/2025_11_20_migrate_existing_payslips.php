<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Migrate existing payslips that might have individual fields to JSON format
        $payslips = DB::table('payslips')->get();
        
        foreach ($payslips as $payslip) {
            // If earnings/deductions are null but individual fields exist, migrate them
            if (is_null($payslip->earnings) && !is_null($payslip->basic_pay)) {
                $earnings = [
                    'Basic Pay' => $payslip->basic_pay ?? 0,
                    'Lunch Allowance' => $payslip->lunch_allowance ?? 0,
                    'Housing Allowance' => $payslip->housing_allowance ?? 0,
                    'Overtime' => $payslip->overtime ?? 0,
                ];
                
                $deductions = [
                    'PAYE' => $payslip->paye ?? 0,
                    'NAPSA' => $payslip->napsa ?? 0,
                    'NHI' => $payslip->nhi ?? 0,
                ];
                
                DB::table('payslips')
                    ->where('id', $payslip->id)
                    ->update([
                        'earnings' => json_encode($earnings),
                        'deductions' => json_encode($deductions),
                        'gross_pay' => $payslip->total_income ?? 0,
                        'total_deductions' => $payslip->total_deductions ?? 0,
                        'net_pay' => $payslip->net_pay ?? 0,
                    ]);
            }
        }
    }

    public function down(): void
    {
        // This migration is not reversible
    }
};
