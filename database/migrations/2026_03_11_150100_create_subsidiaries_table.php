<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubsidiariesTable extends Migration
{
    public function up()
    {
        Schema::create('subsidiaries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('registration_number')->nullable();
            $table->string('country')->nullable();
            $table->string('currency')->nullable();
            $table->decimal('ownership_percentage', 8, 4)->default(0);
            $table->string('consolidation_method')->default('full');
            $table->string('status')->default('active');
            $table->string('parent_company')->nullable();
            $table->text('notes')->nullable();
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('subsidiaries');
    }
}
