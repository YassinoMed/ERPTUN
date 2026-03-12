<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_consents', function (Blueprint $table) {
            $table->id();
            $table->string('subject_type')->nullable();
            $table->string('subject_name');
            $table->string('subject_reference')->nullable();
            $table->string('purpose')->nullable();
            $table->string('channel')->nullable();
            $table->string('status')->default('granted');
            $table->date('consented_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->string('evidence_reference')->nullable();
            $table->text('notes')->nullable();
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_consents');
    }
};
