<?php

// database/migrations/xxxx_xx_xx_create_employees_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(){
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('fullnames');
            $table->string('employee_id')->unique();
            $table->date('date_engaged')->nullable();
            $table->decimal('salary_rate', 15, 2)->default(0); // monthly salary/base rate
            $table->string('company')->nullable();
            $table->string('branch')->nullable();
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->string('pay_method')->nullable(); // e.g., Bank, Cash
            $table->string('bank_acc_number')->nullable();
            $table->string('nrc_number')->nullable();
            $table->string('ssn')->nullable();
            $table->string('nhi_no')->nullable();
            $table->integer('leave_days')->default(0);
            $table->string('tpin')->nullable();
            $table->timestamps();
        });
    }

    public function down(){
        Schema::dropIfExists('employees');
    }
};
