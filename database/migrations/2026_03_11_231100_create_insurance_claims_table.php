<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurance_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insurance_policy_id')->constrained('insurance_policies')->cascadeOnDelete();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->string('claim_number');
            $table->date('incident_date')->nullable();
            $table->date('reported_date')->nullable();
            $table->decimal('amount_claimed', 25, 2)->default(0);
            $table->decimal('amount_settled', 25, 2)->default(0);
            $table->string('priority')->default('medium');
            $table->string('status')->default('draft');
            $table->string('incident_type')->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->integer('created_by');
            $table->timestamps();

            $table->index(['created_by', 'status']);
            $table->index(['created_by', 'priority']);
            $table->unique(['created_by', 'claim_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_claims');
    }
};
