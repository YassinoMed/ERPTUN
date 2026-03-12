<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerRecoveriesTable extends Migration
{
    public function up()
    {
        Schema::create('customer_recoveries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('customer_id');
            $table->integer('invoice_id')->nullable();
            $table->string('reference')->nullable();
            $table->string('stage')->default('new');
            $table->string('priority')->default('medium');
            $table->decimal('due_amount', 20, 4)->default(0);
            $table->date('next_follow_up_date')->nullable();
            $table->date('last_contact_date')->nullable();
            $table->integer('assigned_to')->nullable();
            $table->string('status')->default('open');
            $table->text('notes')->nullable();
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_recoveries');
    }
}
