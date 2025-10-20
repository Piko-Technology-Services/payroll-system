<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EarningRule;

class EarningRuleController extends Controller
{
    public function index()
    {
        $earningRules = EarningRule::all();
        return view('settings.rules.index', compact('earningRules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:fixed,percentage',
            'default_value' => 'required|numeric',
        ]);

        EarningRule::create($request->all());
        return back()->with('success', 'Earning rule added.');
    }

    public function update(Request $request, $id)
    {
        $rule = EarningRule::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:fixed,percentage',
            'default_value' => 'required|numeric',
        ]);

        $rule->update($request->all());
        return back()->with('success', 'Earning rule updated.');
    }

    public function destroy($id)
    {
        EarningRule::destroy($id);
        return back()->with('success', 'Earning rule deleted.');
    }
}

