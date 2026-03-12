<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('managed_property_id')->constrained('managed_properties')->cascadeOnDelete();
            $table->string('unit_code');
            $table->string('floor')->nullable();
            $table->decimal('area', 25, 2)->default(0);
            $table->decimal('monthly_rent', 25, 2)->default(0);
            $table->string('status')->default('available');
            $table->text('notes')->nullable();
            $table->integer('created_by');
            $table->timestamps();

            $table->index(['created_by', 'status']);
            $table->unique(['managed_property_id', 'unit_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_units');
    }
};
