<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_access_scopes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('created_by')->index();
            $table->string('scope_type')->index();
            $table->unsignedBigInteger('scope_id')->index();
            $table->unsignedBigInteger('assigned_by')->nullable()->index();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'scope_type', 'scope_id'], 'user_access_scopes_unique_scope');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_access_scopes');
    }
};
