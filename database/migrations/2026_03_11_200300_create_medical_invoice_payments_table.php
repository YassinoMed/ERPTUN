<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedicalInvoicePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('medical_invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medical_invoice_id');
            $table->date('payment_date');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method')->nullable();
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->index(['medical_invoice_id']);
            $table->index(['created_by', 'payment_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('medical_invoice_payments');
    }
}
