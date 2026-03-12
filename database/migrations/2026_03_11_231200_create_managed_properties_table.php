<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('managed_properties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('property_code');
            $table->string('property_type')->nullable();
            $table->string('status')->default('draft');
            $table->unsignedBigInteger('manager_employee_id')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->text('notes')->nullable();
            $table->integer('created_by');
            $table->timestamps();

            $table->index(['created_by', 'status']);
            $table->unique(['created_by', 'property_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('managed_properties');
    }
};
