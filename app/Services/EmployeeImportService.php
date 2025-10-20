<?php

namespace App\Services;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeImportService
{
    /**
     * Import employees from a CSV/XLSX file path or UploadedFile.
     * Returns an array of per-row results: [ row_number, status, message, employee_id, data ].
     *
     * @param string|UploadedFile $file
     * @return array<int, array<string, mixed>>
     */
    public function import(string|UploadedFile $file): array
    {
        $results = [];

        $path = $this->resolvePath($file);
        if ($path === null) {
            return [[
                'row_number' => null,
                'status' => 'error',
                'message' => 'File not found or unreadable',
                'employee_id' => null,
                'data' => null,
            ]];
        }

        try {
            $collections = Excel::toCollection(null, $path);
        } catch (\Throwable $e) {
            Log::error('Employee import: failed reading file', ['error' => $e->getMessage()]);
            return [[
                'row_number' => null,
                'status' => 'error',
                'message' => 'Failed to read file: ' . $e->getMessage(),
                'employee_id' => null,
                'data' => null,
            ]];
        }

        if ($collections->isEmpty()) {
            return [[
                'row_number' => null,
                'status' => 'error',
                'message' => 'No sheets found in file',
                'employee_id' => null,
                'data' => null,
            ]];
        }

        $sheet = $collections->first();
        if (!$sheet instanceof Collection || $sheet->isEmpty()) {
            return [[
                'row_number' => null,
                'status' => 'error',
                'message' => 'File is empty',
                'employee_id' => null,
                'data' => null,
            ]];
        }

        $headerRow = $sheet->first();
        $hasHeadingRow = is_array($headerRow) || $headerRow instanceof \ArrayAccess;
        $startIndex = 1;

        // Normalize to a collection of associative arrays with headings
        if ($hasHeadingRow && $this->rowLooksLikeHeading($headerRow)) {
            $rows = $sheet->skip(1)->values()->map(function ($row) use ($headerRow) {
                $assoc = [];
                foreach ($headerRow as $index => $heading) {
                    $key = $this->normalizeHeading((string) $heading);
                    $assoc[$key] = is_array($row) || $row instanceof \ArrayAccess ? ($row[$index] ?? null) : null;
                }
                return $assoc;
            });
            $startIndex = 2; // data starts from line 2
        } else {
            // Assume Laravel Excel already provided heading row mapping
            $rows = $sheet;
            $startIndex = 1;
        }

        foreach ($rows as $offset => $row) {
            $rowNumber = $startIndex + $offset;
            $data = $this->transformRowToEmployeeAttributes((array) $row);

            $validator = Validator::make($data, [
                'fullnames' => ['required', 'string'],
                'employee_id' => ['required', 'string'],
                'salary_rate' => ['required', 'numeric'],
                'date_engaged' => ['nullable', 'date'],
                'leave_days' => ['nullable', 'numeric'],
            ]);

            if ($validator->fails()) {
                $results[] = [
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

                $results[] = [
                    'row_number' => $rowNumber,
                    'status' => 'success',
                    'message' => 'Employee ' . $employee->employee_id . ' imported',
                    'employee_id' => $employee->employee_id,
                    'data' => $data,
                ];
            } catch (\Throwable $e) {
                Log::error('Employee import: failed to save row', [
                    'row' => $rowNumber,
                    'error' => $e->getMessage(),
                ]);
                $results[] = [
                    'row_number' => $rowNumber,
                    'status' => 'error',
                    'message' => 'Database error: ' . $e->getMessage(),
                    'employee_id' => Arr::get($data, 'employee_id'),
                    'data' => $data,
                ];
            }
        }

        return $results;
    }

    private function resolvePath(string|UploadedFile $file): ?string
    {
        if ($file instanceof UploadedFile) {
            return $file->getRealPath() ?: $file->getPathname();
        }

        if (is_string($file) && file_exists($file) && is_readable($file)) {
            return $file;
        }

        return null;
    }

    private function rowLooksLikeHeading($row): bool
    {
        $sample = collect($row)->take(5)->map(function ($v) {
            return is_string($v) ? strtolower(trim($v)) : $v;
        })->all();

        $known = [
            'full name', 'employee id', 'salary rate', 'position', 'department', 'branch',
            'pay method', 'bank account', 'company', 'date engaged', 'nrc number', 'ssn',
            'nhi no', 'leave days', 'tpin'
        ];

        foreach ($sample as $cell) {
            if (is_string($cell) && in_array($cell, $known, true)) {
                return true;
            }
        }
        return false;
    }

    private function normalizeHeading(string $heading): string
    {
        return strtolower(trim($heading));
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
                // Excel serialized date
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
        // Remove common formatting like commas
        $normalized = str_replace([',', ' '], '', (string) $value);
        return is_numeric($normalized) ? (float) $normalized : null;
    }
}


