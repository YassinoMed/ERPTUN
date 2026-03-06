<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChequesTable extends Migration
{
    public function up()
    {
        Schema::create('cheques', function (Blueprint $table) {
            $table->id();
            $table->string('beneficiary_name');
            $table->decimal('amount', 16, 3);
            $table->string('amount_text')->nullable();
            $table->string('currency')->default('TND');
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_agency')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('rib')->nullable();
            $table->string('chequebook_number')->nullable();
            $table->string('cheque_number')->nullable();
            $table->string('status')->default('issued');
            $table->date('status_date')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->index(['created_by', 'status']);
            $table->index(['created_by', 'cheque_number']);
            $table->index(['created_by', 'beneficiary_name']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('cheques');
    }
}
