<?php

namespace App\Console\Commands;

use App\Services\EmployeeImportService;
use Illuminate\Console\Command;

class ImportEmployees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employees:import {file : Path to CSV/XLSX file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import employees from a CSV or Excel file';

    public function handle(EmployeeImportService $service): int
    {
        $filePath = (string) $this->argument('file');

        $results = $service->import($filePath);

        foreach ($results as $res) {
            $prefix = $res['row_number'] !== null ? 'Row ' . $res['row_number'] . ': ' : '';
            if ($res['status'] === 'success') {
                $this->info($prefix . $res['message']);
            } else {
                $this->error($prefix . $res['message']);
            }
        }

        $hasErrors = collect($results)->contains(fn ($r) => $r['status'] !== 'success');
        return $hasErrors ? self::FAILURE : self::SUCCESS;
    }
}


