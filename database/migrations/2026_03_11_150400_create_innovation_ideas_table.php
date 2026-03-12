<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInnovationIdeasTable extends Migration
{
    public function up()
    {
        Schema::create('innovation_ideas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('category')->nullable();
            $table->integer('submitted_by')->nullable();
            $table->string('status')->default('draft');
            $table->string('priority')->default('medium');
            $table->decimal('expected_value', 20, 4)->default(0);
            $table->text('description')->nullable();
            $table->text('business_case')->nullable();
            $table->text('implementation_notes')->nullable();
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('innovation_ideas');
    }
}
