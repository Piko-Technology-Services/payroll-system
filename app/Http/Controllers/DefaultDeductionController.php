<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DefaultDeduction;

class DefaultDeductionController extends Controller
{
    /**
     * Display a listing of default deductions
     */
    public function index()
    {
        $defaultDeductions = DefaultDeduction::ordered()->get();
        return view('defaults.deductions.index', compact('defaultDeductions'));
    }

    /**
     * Store a newly created default deduction
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:default_deductions,name',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:fixed,percentage',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_statutory' => 'boolean'
        ]);

        DefaultDeduction::create([
            'name' => $request->name,
            'amount' => $request->amount,
            'type' => $request->type,
            'description' => $request->description,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => true,
            'is_statutory' => $request->has('is_statutory')
        ]);

        return back()->with('success', 'Default deduction added successfully.');
    }

    public function edit($id)
{
    $deduction = DefaultDeduction::findOrFail($id);
    return response()->json($deduction);
}


    /**
     * Update the specified default deduction
     */
    public function update(Request $request, $id)
    {
        $defaultDeduction = DefaultDeduction::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:default_deductions,name,' . $id,
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:fixed,percentage',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_statutory' => 'boolean'
        ]);

        $defaultDeduction->update([
            'name' => $request->name,
            'amount' => $request->amount,
            'type' => $request->type,
            'description' => $request->description,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active'),
            'is_statutory' => $request->has('is_statutory')
        ]);

        return back()->with('success', 'Default deduction updated successfully.');
    }

    /**
     * Remove the specified default deduction
     */
    public function destroy($id)
    {
        $defaultDeduction = DefaultDeduction::findOrFail($id);
        $defaultDeduction->delete();

        return back()->with('success', 'Default deduction deleted successfully.');
    }

    /**
     * Toggle the active status of a default deduction
     */
    public function toggleStatus($id)
    {
        $defaultDeduction = DefaultDeduction::findOrFail($id);
        $defaultDeduction->update(['is_active' => !$defaultDeduction->is_active]);

        $status = $defaultDeduction->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Default deduction {$status} successfully.");
    }
}
