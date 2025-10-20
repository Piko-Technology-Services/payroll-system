<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DefaultEarning;

class DefaultEarningController extends Controller
{
    /**
     * Display a listing of default earnings
     */
    public function index()
    {
        $defaultEarnings = DefaultEarning::ordered()->get();
        return view('defaults.earnings.index', compact('defaultEarnings'));
    }

    /**
     * Store a newly created default earning
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:default_earnings,name',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:fixed,percentage',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        DefaultEarning::create([
            'name' => $request->name,
            'amount' => $request->amount,
            'type' => $request->type,
            'description' => $request->description,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => true
        ]);

        return back()->with('success', 'Default earning added successfully.');
    }

    /**
 * Fetch earning details (AJAX)
 */
public function edit($id)
{
    $earning = DefaultEarning::findOrFail($id);
    return response()->json($earning);
}


    /**
     * Update the specified default earning
     */
    public function update(Request $request, $id)
    {
        $defaultEarning = DefaultEarning::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:default_earnings,name,' . $id,
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:fixed,percentage',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $defaultEarning->update([
            'name' => $request->name,
            'amount' => $request->amount,
            'type' => $request->type,
            'description' => $request->description,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active')
        ]);

        return back()->with('success', 'Default earning updated successfully.');
    }

    /**
     * Remove the specified default earning
     */
    public function destroy($id)
    {
        $defaultEarning = DefaultEarning::findOrFail($id);
        $defaultEarning->delete();

        return back()->with('success', 'Default earning deleted successfully.');
    }

    /**
     * Toggle the active status of a default earning
     */
    public function toggleStatus($id)
    {
        $defaultEarning = DefaultEarning::findOrFail($id);
        $defaultEarning->update(['is_active' => !$defaultEarning->is_active]);

        $status = $defaultEarning->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Default earning {$status} successfully.");
    }
}
