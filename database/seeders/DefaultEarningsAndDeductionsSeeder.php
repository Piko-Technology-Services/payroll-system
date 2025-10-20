<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DefaultEarning;
use App\Models\DefaultDeduction;

class DefaultEarningsAndDeductionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default Earnings
        $defaultEarnings = [
            [
                'name' => 'Basic Pay',
                'amount' => 0,
                'type' => 'fixed',
                'description' => 'Employee basic salary',
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'name' => 'Lunch Allowance',
                'amount' => 0,
                'type' => 'fixed',
                'description' => 'Monthly lunch allowance',
                'sort_order' => 2,
                'is_active' => true
            ],
            [
                'name' => 'Transport Allowance',
                'amount' => 0,
                'type' => 'fixed',
                'description' => 'Monthly transport allowance',
                'sort_order' => 3,
                'is_active' => true
            ],
            [
                'name' => 'Housing Allowance',
                'amount' => 0,
                'type' => 'fixed',
                'description' => 'Monthly housing allowance',
                'sort_order' => 4,
                'is_active' => true
            ],
            [
                'name' => 'Overtime',
                'amount' => 0,
                'type' => 'fixed',
                'description' => 'Overtime pay',
                'sort_order' => 5,
                'is_active' => true
            ],
            [
                'name' => 'Bonus',
                'amount' => 0,
                'type' => 'fixed',
                'description' => 'Performance bonus',
                'sort_order' => 6,
                'is_active' => true
            ]
        ];

        foreach ($defaultEarnings as $earning) {
            DefaultEarning::updateOrCreate(
                ['name' => $earning['name']],
                $earning
            );
        }

        // Default Deductions
        $defaultDeductions = [
            [
                'name' => 'PAYE',
                'amount' => 0,
                'type' => 'fixed',
                'description' => 'Pay As You Earn tax (calculated based on ZRA 2025 rates)',
                'sort_order' => 1,
                'is_active' => true,
                'is_statutory' => true
            ],
            [
                'name' => 'NAPSA',
                'amount' => 5,
                'type' => 'percentage',
                'description' => 'National Pension Scheme Authority (5% of gross pay)',
                'sort_order' => 2,
                'is_active' => true,
                'is_statutory' => true
            ],
            [
                'name' => 'NHIS',
                'amount' => 2,
                'type' => 'percentage',
                'description' => 'National Health Insurance Scheme (2% of gross pay)',
                'sort_order' => 3,
                'is_active' => true,
                'is_statutory' => true
            ],
            [
                'name' => 'Personal Levy',
                'amount' => 3,
                'type' => 'fixed',
                'description' => 'Personal levy (fixed K3)',
                'sort_order' => 4,
                'is_active' => true,
                'is_statutory' => true
            ],
            [
                'name' => 'Loan Deduction',
                'amount' => 0,
                'type' => 'fixed',
                'description' => 'Staff loan repayment',
                'sort_order' => 5,
                'is_active' => true,
                'is_statutory' => false
            ],
            [
                'name' => 'Advance Deduction',
                'amount' => 0,
                'type' => 'fixed',
                'description' => 'Salary advance repayment',
                'sort_order' => 6,
                'is_active' => true,
                'is_statutory' => false
            ],
            [
                'name' => 'Union Dues',
                'amount' => 0,
                'type' => 'fixed',
                'description' => 'Trade union membership dues',
                'sort_order' => 7,
                'is_active' => true,
                'is_statutory' => false
            ]
        ];

        foreach ($defaultDeductions as $deduction) {
            DefaultDeduction::updateOrCreate(
                ['name' => $deduction['name']],
                $deduction
            );
        }

        $this->command->info('Default earnings and deductions seeded successfully!');
    }
}
