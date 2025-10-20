<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EarningRule;
use App\Models\DeductionRule;

class RulesController extends Controller
{
    public function update(Request $request)
    {
        foreach ($request->input('earnings', []) as $id => $value) {
            $rule = EarningRule::find($id);
            if ($rule) {
                $rule->default_value = $value;
                $rule->type = $request->input('earning_types.' . $id, $rule->type);
                $rule->save();
            }
        }
        foreach ($request->input('deductions', []) as $id => $value) {
            $rule = DeductionRule::find($id);
            if ($rule) {
                $rule->default_value = $value;
                $rule->type = $request->input('deduction_types.' . $id, $rule->type);
                $rule->save();
            }
        }
        return redirect()->route('payslips.index')->with('success', 'Rules updated for all employees.');
    }
}
