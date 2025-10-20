<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeductionRule;

class DeductionRuleController extends Controller
{
    public function index()
    {
        $deductionRules = DeductionRule::all();
        return view('settings.rules.index', compact('deductionRules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:fixed,percentage',
            'default_value' => 'required|numeric',
        ]);

        DeductionRule::create($request->all());
        return back()->with('success', 'Deduction rule added.');
    }

    public function update(Request $request, $id)
    {
        $rule = DeductionRule::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:fixed,percentage',
            'default_value' => 'required|numeric',
        ]);

        $rule->update($request->all());
        return back()->with('success', 'Deduction rule updated.');
    }

    public function destroy($id)
    {
        DeductionRule::destroy($id);
        return back()->with('success', 'Deduction rule deleted.');
    }
}

