<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\EarningRule;
use App\Models\DeductionRule;

class EmployeeStatutoryController extends Controller
{
    public function getStatutory($id)
    {
        $employee = Employee::findOrFail($id);
        $basicPay = $employee->salary_rate;
        $earningRules = EarningRule::all();
        $deductionRules = DeductionRule::all();

        $earnings = [];
        foreach ($earningRules as $rule) {
            if ($rule->type === 'percentage') {
                $earnings[$rule->name] = ($basicPay * $rule->value) / 100;
            } else {
                $earnings[$rule->name] = $rule->default_value;
            }
        }
        $earnings['basic_pay'] = $basicPay;

        $deductions = [];
        foreach ($deductionRules as $rule) {
            if ($rule->type === 'percentage') {
                $deductions[$rule->name] = ($basicPay * $rule->value) / 100;
            } else {
                $deductions[$rule->name] = $rule->default_value;
            }
        }

        return response()->json([
            'earnings' => $earnings,
            'deductions' => $deductions
        ]);
    }
}
