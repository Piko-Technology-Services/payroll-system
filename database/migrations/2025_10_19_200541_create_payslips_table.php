<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payslips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->json('earnings')->nullable();
            $table->json('deductions')->nullable();
            $table->decimal('gross_pay', 12, 2)->default(0);
            $table->decimal('total_income', 15, 2)->default(0);
            $table->decimal('total_deductions', 15, 2)->default(0);
            $table->decimal('net_pay', 15, 2)->default(0);
            $table->string('pay_month');
            $table->date('pay_date');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('payslips');
    }
};
