<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_addon_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('plan_addon_id');
            $table->unsignedBigInteger('requested_by');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->string('status')->default('pending');
            $table->string('billing_cycle')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->text('request_note')->nullable();
            $table->text('review_note')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['created_by', 'status']);
            $table->index(['plan_addon_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_addon_requests');
    }
};
