<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCapTablesTable extends Migration
{
    public function up()
    {
        Schema::create('cap_tables', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('holder_name');
            $table->string('holder_type')->default('individual');
            $table->string('share_class')->nullable();
            $table->decimal('share_count', 20, 4)->default(0);
            $table->decimal('issue_price', 20, 4)->default(0);
            $table->decimal('ownership_percentage', 8, 4)->default(0);
            $table->decimal('voting_percentage', 8, 4)->default(0);
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->text('notes')->nullable();
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cap_tables');
    }
}
