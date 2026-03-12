<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurance_policies', function (Blueprint $table) {
            $table->id();
            $table->string('policy_number');
            $table->string('provider_name');
            $table->string('policy_name');
            $table->string('coverage_type')->nullable();
            $table->string('insured_party')->nullable();
            $table->string('insured_asset')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('premium_amount', 25, 2)->default(0);
            $table->decimal('coverage_amount', 25, 2)->default(0);
            $table->string('status')->default('draft');
            $table->text('notes')->nullable();
            $table->integer('created_by');
            $table->timestamps();

            $table->index(['created_by', 'status']);
            $table->unique(['created_by', 'policy_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_policies');
    }
};
