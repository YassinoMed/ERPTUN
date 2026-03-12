<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedicalServicesTable extends Migration
{
    public function up()
    {
        Schema::create('medical_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_service_id')->nullable();
            $table->string('code')->nullable();
            $table->string('name');
            $table->string('service_type')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('default_coverage_rate', 8, 2)->default(0);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->index(['created_by', 'code']);
            $table->index(['created_by', 'service_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('medical_services');
    }
}
