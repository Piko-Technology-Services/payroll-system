<?php

namespace App\Imports;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeesImport implements ToCollection, WithHeadingRow
{
    /** @var array<int, array<string, mixed>> */
    public array $results = [];

    public function collection(Collection $rows): void
    {
        $rowNumber = 1; // includes heading row implicitly; WithHeadingRow starts data at row 2
        foreach ($rows as $row) {
            $rowNumber++;
            $rowArray = (array) $row;
            $data = $this->transformRowToEmployeeAttributes($rowArray);

            $validator = Validator::make($data, [
                'fullnames' => ['required', 'string'],
                'employee_id' => ['required', 'string'],
                'salary_rate' => ['required', 'numeric'],
                'date_engaged' => ['nullable', 'date'],
                'leave_days' => ['nullable', 'numeric'],
            ]);

            if ($validator->fails()) {
                $this->results[] = [
                    'row_number' => $rowNumber,
                    'status' => 'error',
                    'message' => 'Validation failed: ' . implode('; ', $validator->errors()->all()),
                    'employee_id' => Arr::get($data, 'employee_id'),
                    'data' => $data,
                ];
                continue;
            }

            try {
                $employee = Employee::updateOrCreate(
                    ['employee_id' => $data['employee_id']],
                    $data
                );
                $this->results[] = [
                    'row_number' => $rowNumber,
                    'status' => 'success',
                    'message' => 'Employee ' . $employee->employee_id . ' imported',
                    'employee_id' => $employee->employee_id,
                    'data' => $data,
                ];
            } catch (\Throwable $e) {
                Log::error('EmployeesImport save failed', ['row' => $rowNumber, 'error' => $e->getMessage()]);
                $this->results[] = [
                    'row_number' => $rowNumber,
                    'status' => 'error',
                    'message' => 'Database error: ' . $e->getMessage(),
                    'employee_id' => Arr::get($data, 'employee_id'),
                    'data' => $data,
                ];
            }
        }
    }

    /**
     * Map input row headings to Employee model attributes and coerce values.
     *
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    private function transformRowToEmployeeAttributes(array $row): array
    {
        $get = function (array $aliases) use ($row) {
            foreach ($aliases as $alias) {
                if (array_key_exists($alias, $row)) {
                    return $row[$alias];
                }
            }
            return null;
        };

        $fullnames = $get(['full name', 'fullname', 'fullnames', 'name']);
        $employeeId = $get(['employee id', 'employee_id', 'id']);
        $salaryRate = $get(['salary rate', 'salary_rate', 'rate']);
        $position = $get(['position']);
        $department = $get(['department']);
        $branch = $get(['branch']);
        $payMethod = $get(['pay method', 'pay_method']);
        $bankAccount = $get(['bank account', 'bank_acc_number', 'bank account number']);
        $company = $get(['company']);
        $dateEngagedRaw = $get(['date engaged', 'date_engaged', 'date']);
        $nrcNumber = $get(['nrc number', 'nrc_number']);
        $ssn = $get(['ssn']);
        $nhiNo = $get(['nhi no', 'nhi_no']);
        $leaveDays = $get(['leave days', 'leave_days']);
        $tpin = $get(['tpin']);

        $dateEngaged = $this->parseDateNullable($dateEngagedRaw);

        return [
            'fullnames' => $this->stringOrNull($fullnames),
            'employee_id' => $this->stringOrNull($employeeId),
            'salary_rate' => $this->numericOrNull($salaryRate),
            'position' => $this->stringOrNull($position),
            'department' => $this->stringOrNull($department),
            'branch' => $this->stringOrNull($branch),
            'pay_method' => $this->stringOrNull($payMethod),
            'bank_acc_number' => $this->stringOrNull($bankAccount),
            'company' => $this->stringOrNull($company),
            'date_engaged' => $dateEngaged,
            'nrc_number' => $this->stringOrNull($nrcNumber),
            'ssn' => $this->stringOrNull($ssn),
            'nhi_no' => $this->stringOrNull($nhiNo),
            'leave_days' => $this->numericOrNull($leaveDays),
            'tpin' => $this->stringOrNull($tpin),
        ];
    }

    private function parseDateNullable(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        try {
            if (is_numeric($value)) {
                $date = Carbon::createFromTimestampUTC(((int) $value - 25569) * 86400);
            } else {
                $date = Carbon::parse((string) $value);
            }
            return $date->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    private function stringOrNull(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $str = trim((string) $value);
        return $str === '' ? null : $str;
    }

    private function numericOrNull(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_numeric($value)) {
            return (float) $value;
        }
        $normalized = str_replace([',', ' '], '', (string) $value);
        return is_numeric($normalized) ? (float) $normalized : null;
    }
}


