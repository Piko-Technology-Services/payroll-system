<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Payslip;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get current month and year
        $currentMonth = now()->format('Y-m');
        $currentYear = now()->year;
        
        // Employee Statistics
        $totalEmployees = Employee::count();
        $newEmployeesThisMonth = Employee::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // Payslip Statistics
        $totalPayslips = Payslip::count();
        $payslipsThisMonth = Payslip::where('pay_month', $currentMonth)->count();
        $pendingPayslips = $totalEmployees - $payslipsThisMonth;
        
        // Financial Overview
        $totalPayrollThisMonth = Payslip::where('pay_month', $currentMonth)
            ->sum('net_pay');
        $totalPayrollThisYear = Payslip::whereYear('pay_date', $currentYear)
            ->sum('net_pay');
        
        // Recent Activity
        $recentPayslips = Payslip::with('employee')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        $recentEmployees = Employee::orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Department Statistics
        $departmentStats = Employee::select('department', DB::raw('count(*) as count'))
            ->whereNotNull('department')
            ->groupBy('department')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get();
        
        // Monthly Payroll Trend (last 6 months)
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            $monthlyTrend[] = [
                'month' => $date->format('M Y'),
                'amount' => Payslip::where('pay_month', $monthKey)->sum('net_pay')
            ];
        }
        
        // Salary Range Distribution
        $salaryRanges = [
            '0-2000' => Employee::whereBetween('salary_rate', [0, 2000])->count(),
            '2001-5000' => Employee::whereBetween('salary_rate', [2001, 5000])->count(),
            '5001-10000' => Employee::whereBetween('salary_rate', [5001, 10000])->count(),
            '10000+' => Employee::where('salary_rate', '>', 10000)->count(),
        ];
        
        return view('dashboard.index', compact(
            'totalEmployees',
            'newEmployeesThisMonth',
            'totalPayslips',
            'payslipsThisMonth',
            'pendingPayslips',
            'totalPayrollThisMonth',
            'totalPayrollThisYear',
            'recentPayslips',
            'recentEmployees',
            'departmentStats',
            'monthlyTrend',
            'salaryRanges',
            'currentMonth'
        ));
    }
}
