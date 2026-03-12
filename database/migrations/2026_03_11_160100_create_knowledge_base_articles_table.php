<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKnowledgeBaseArticlesTable extends Migration
{
    public function up()
    {
        Schema::create('knowledge_base_articles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('knowledge_base_category_id')->nullable();
            $table->string('title');
            $table->string('slug');
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->string('status')->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('knowledge_base_articles');
    }
}
