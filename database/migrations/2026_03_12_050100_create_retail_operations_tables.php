<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->integer('created_by')->default(0);
            $table->timestamps();
        });

        Schema::create('cash_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cash_register_id');
            $table->enum('type', ['in', 'out']);
            $table->decimal('amount', 15, 2);
            $table->date('movement_date');
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->integer('created_by')->default(0);
            $table->timestamps();
        });

        Schema::create('loyalty_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('code');
            $table->integer('points_balance')->default(0);
            $table->string('tier', 64)->default('standard');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->integer('created_by')->default(0);
            $table->timestamps();
            $table->unique(['code', 'created_by']);
        });

        Schema::create('delivery_routes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('delivery_note_id')->nullable();
            $table->string('name');
            $table->string('driver_name')->nullable();
            $table->string('vehicle_no')->nullable();
            $table->date('route_date');
            $table->enum('status', ['planned', 'in_transit', 'completed'])->default('planned');
            $table->text('notes')->nullable();
            $table->integer('created_by')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_routes');
        Schema::dropIfExists('loyalty_accounts');
        Schema::dropIfExists('cash_movements');
        Schema::dropIfExists('cash_registers');
    }
};
