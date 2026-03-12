<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('software_licenses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('vendor_name')->nullable();
            $table->string('license_key')->nullable();
            $table->string('license_type')->nullable();
            $table->string('status')->default('active');
            $table->unsignedBigInteger('configuration_item_id')->nullable();
            $table->unsignedBigInteger('assigned_user_id')->nullable();
            $table->integer('seats_total')->default(1);
            $table->integer('seats_used')->default(0);
            $table->decimal('cost', 15, 2)->default(0);
            $table->date('renewal_date')->nullable();
            $table->text('notes')->nullable();
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('software_licenses');
    }
};
