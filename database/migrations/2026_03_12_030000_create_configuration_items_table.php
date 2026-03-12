<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuration_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('ci_type')->nullable();
            $table->string('status')->default('active');
            $table->string('criticality')->nullable();
            $table->unsignedBigInteger('asset_id')->nullable();
            $table->unsignedBigInteger('owner_user_id')->nullable();
            $table->string('asset_tag')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('location')->nullable();
            $table->string('environment')->nullable();
            $table->string('vendor_name')->nullable();
            $table->date('acquired_at')->nullable();
            $table->text('notes')->nullable();
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuration_items');
    }
};
