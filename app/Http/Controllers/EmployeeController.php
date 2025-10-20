<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EmployeesImport;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::query();

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('fullnames', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%")
                  ->orWhere('branch', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department')) {
            $query->where('department', 'like', "%{$request->department}%");
        }

        if ($request->filled('position')) {
            $query->where('position', 'like', "%{$request->position}%");
        }

        if ($request->filled('branch')) {
            $query->where('branch', 'like', "%{$request->branch}%");
        }

        if ($request->filled('company')) {
            $query->where('company', 'like', "%{$request->company}%");
        }

        if ($request->filled('pay_method')) {
            $query->where('pay_method', 'like', "%{$request->pay_method}%");
        }

        if ($request->filled('salary_min')) {
            $query->where('salary_rate', '>=', $request->salary_min);
        }

        if ($request->filled('salary_max')) {
            $query->where('salary_rate', '<=', $request->salary_max);
        }

        $employees = $query->orderBy('fullnames')->get();

        // Get unique values for filter dropdowns
        $departments = Employee::whereNotNull('department')->distinct()->pluck('department')->filter()->sort();
        $positions = Employee::whereNotNull('position')->distinct()->pluck('position')->filter()->sort();
        $branches = Employee::whereNotNull('branch')->distinct()->pluck('branch')->filter()->sort();
        $companies = Employee::whereNotNull('company')->distinct()->pluck('company')->filter()->sort();
        $payMethods = Employee::whereNotNull('pay_method')->distinct()->pluck('pay_method')->filter()->sort();

        return view('employees.index', compact('employees', 'departments', 'positions', 'branches', 'companies', 'payMethods'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'fullnames' => 'required|string|max:255',
            'employee_id' => 'required|string|max:100|unique:employees',
            'salary_rate' => 'required|numeric',
            'company' => 'nullable|string',
            'branch' => 'nullable|string',
            'department' => 'nullable|string',
            'position' => 'nullable|string',
            'pay_method' => 'nullable|string',
            'bank_acc_number' => 'nullable|string',
            'nrc_number' => 'nullable|string',
            'ssn' => 'nullable|string',
            'nhi_no' => 'nullable|string',
            'leave_days' => 'nullable|integer',
            'tpin' => 'nullable|string',
            'date_engaged' => 'nullable|date',
        ]);

        Employee::create($data);
        return redirect()->route('employees.index')->with('success', 'Employee added successfully.');
    }

    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        try {
            $employee->update($request->all());
            return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('employees.index')->with('error', 'Failed to update employee: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            Employee::destroy($id);
            return back()->with('success', 'Employee deleted.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete employee: ' . $e->getMessage());
        }
    }

    public function importForm()
    {
        return view('employees.import');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,csv']);
        try {
            Excel::import(new EmployeesImport, $request->file('file'));
            return redirect()->route('employees.index')->with('success', 'Employees imported successfully!');
        } catch (\Exception $e) {
            return redirect()->route('employees.index')->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
}

