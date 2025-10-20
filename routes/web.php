<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\RulesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\EmployeeStatutoryController;
use App\Http\Controllers\EarningRuleController;
use App\Http\Controllers\DeductionRuleController;
use App\Http\Controllers\DefaultEarningController;
use App\Http\Controllers\DefaultDeductionController;

// Earnings
Route::resource('earningRules', EarningRuleController::class)->except(['show','create','edit']);

// Deductions
Route::resource('deductionRules', DeductionRuleController::class)->except(['show','create','edit']);


Route::post('/earning-rules', [EarningRuleController::class, 'store'])->name('earningRules.store');
Route::post('/deduction-rules', [DeductionRuleController::class, 'store'])->name('deductionRules.store');

Route::get('/', function () {
    return auth()->check() ? redirect('/employees') : redirect()->route('login');
});

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
// web.php
Route::get('/employees/{employee}/payslip-defaults', [PayslipController::class, 'getPayslipDefaults']);

Route::middleware('auth')->prefix('employees')->group(function () {
    Route::get('/', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/import', [EmployeeController::class, 'importForm'])->name('employees.import.form');
    Route::post('/import', [EmployeeController::class, 'import'])->name('employees.import');
    Route::get('/{id}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/{id}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
});

Route::middleware('auth')->prefix('payslips')->group(function () {
    Route::get('/', [PayslipController::class, 'index'])->name('payslips.index');
    Route::get('/create', [PayslipController::class, 'create'])->name('payslips.create');
    Route::post('/', [PayslipController::class, 'store'])->name('payslips.store');
    Route::get('/{id}/edit', [PayslipController::class, 'edit'])->name('payslips.edit');
    Route::put('/{id}', [PayslipController::class, 'update'])->name('payslips.update');
    Route::delete('/{id}', [PayslipController::class, 'destroy'])->name('payslips.destroy');

    Route::get('/{id}/pdf', [PayslipController::class, 'downloadPdf'])->name('payslips.pdf');
    Route::get('/export/csv', [PayslipController::class, 'exportCsv'])->name('payslips.export.csv');
    Route::get('/export/pdf', [PayslipController::class, 'exportAllPdf'])->name('payslips.export.pdf');
    Route::post('/generate-all', [PayslipController::class, 'generateAll'])->name('payslips.generateAll');
    Route::post('/generate-from-net-pay', [PayslipController::class, 'generateAllFromNetPay'])->name('payslips.generateFromNetPay');
});

Route::post('/rules/update', [RulesController::class, 'update'])->name('rules.update')->middleware('auth');

// Default Earnings Management
Route::middleware('auth')->prefix('default-earnings')->group(function () {
    Route::get('/', [DefaultEarningController::class, 'index'])->name('default-earnings.index');
    Route::post('/', [DefaultEarningController::class, 'store'])->name('default-earnings.store');
    Route::get('/{id}/edit', [DefaultEarningController::class, 'edit'])->name('default-earnings.edit'); // 👈 added
    Route::put('/{id}', [DefaultEarningController::class, 'update'])->name('default-earnings.update');
    Route::delete('/{id}', [DefaultEarningController::class, 'destroy'])->name('default-earnings.destroy');
    Route::patch('/{id}/toggle-status', [DefaultEarningController::class, 'toggleStatus'])->name('default-earnings.toggle-status');
});


// Default Deductions Management
Route::middleware('auth')->prefix('default-deductions')->group(function () {
    Route::get('/', [DefaultDeductionController::class, 'index'])->name('default-deductions.index');
    Route::post('/', [DefaultDeductionController::class, 'store'])->name('default-deductions.store');
    Route::put('/{id}', [DefaultDeductionController::class, 'update'])->name('default-deductions.update');
    Route::delete('/{id}', [DefaultDeductionController::class, 'destroy'])->name('default-deductions.destroy');
    Route::patch('/{id}/toggle-status', [DefaultDeductionController::class, 'toggleStatus'])->name('default-deductions.toggle-status');
});

Route::get('/api/employees/{id}/statutory', [EmployeeStatutoryController::class, 'getStatutory']);
