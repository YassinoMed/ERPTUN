<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentRepositoriesTable extends Migration
{
    public function up()
    {
        Schema::create('document_repositories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('document_repository_category_id')->nullable();
            $table->string('title');
            $table->string('reference')->nullable();
            $table->string('version')->default('1.0');
            $table->string('status')->default('draft');
            $table->string('document')->nullable();
            $table->text('description')->nullable();
            $table->date('effective_date')->nullable();
            $table->date('expires_at')->nullable();
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_repositories');
    }
}
