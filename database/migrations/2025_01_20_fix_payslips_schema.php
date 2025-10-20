<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payslips', function (Blueprint $table) {
            // Remove unused individual fields
            $table->dropColumn([
                'basic_pay',
                'lunch_allowance', 
                'housing_allowance',
                'overtime',
                'paye',
                'napsa',
                'nhi',
                'total_income'
            ]);
            
            // Ensure JSON fields are properly indexed
            $table->json('earnings')->change();
            $table->json('deductions')->change();
            
            // Add constraints
            $table->decimal('gross_pay', 15, 2)->default(0)->change();
            $table->decimal('total_deductions', 15, 2)->default(0)->change();
            $table->decimal('net_pay', 15, 2)->default(0)->change();
            
            // Add check constraint to prevent negative net pay
            $table->check('net_pay >= 0', 'net_pay_non_negative');
        });
    }

    public function down(): void
    {
        Schema::table('payslips', function (Blueprint $table) {
            $table->dropCheck('net_pay_non_negative');
            
            // Restore individual fields if needed
            $table->decimal('basic_pay', 15, 2)->default(0);
            $table->decimal('lunch_allowance', 15, 2)->default(0);
            $table->decimal('housing_allowance', 15, 2)->default(0);
            $table->decimal('overtime', 15, 2)->default(0);
            $table->decimal('paye', 15, 2)->default(0);
            $table->decimal('napsa', 15, 2)->default(0);
            $table->decimal('nhi', 15, 2)->default(0);
            $table->decimal('total_income', 15, 2)->default(0);
        });
    }
};
