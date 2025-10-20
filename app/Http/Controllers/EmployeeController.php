<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EmployeesImport;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::all();
        return view('employees.index', compact('employees'));
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

