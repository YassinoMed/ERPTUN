<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_leases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('managed_property_id')->constrained('managed_properties')->cascadeOnDelete();
            $table->foreignId('property_unit_id')->constrained('property_units')->cascadeOnDelete();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('reference');
            $table->string('billing_cycle')->default('monthly');
            $table->string('status')->default('draft');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('renewal_date')->nullable();
            $table->decimal('rent_amount', 25, 2)->default(0);
            $table->decimal('deposit_amount', 25, 2)->default(0);
            $table->text('notes')->nullable();
            $table->integer('created_by');
            $table->timestamps();

            $table->index(['created_by', 'status']);
            $table->unique(['created_by', 'reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_leases');
    }
};
