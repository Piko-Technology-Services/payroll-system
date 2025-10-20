<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('deduction_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['fixed', 'percentage'])->default('fixed');
            $table->decimal('default_value', 8, 2)->default(0);
            $table->timestamps();
        });


    }

    public function down(): void {
        Schema::dropIfExists('deduction_rules');
    }
};
