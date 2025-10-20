<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('default_earnings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->decimal('amount', 15, 2)->default(0);
            $table->enum('type', ['fixed', 'percentage'])->default('fixed');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('default_earnings');
    }
};
