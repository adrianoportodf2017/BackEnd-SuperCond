<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 1000)->nullable();
            $table->binary('content')->nullable();
            $table->string('thumb', 1000)->nullable();
            $table->string('slug', 1000)->nullable();
            $table->string('category_id', 1000)->nullable();
            $table->string('comments_count', 1000)->nullable();
            $table->string('likes_count', 1000)->nullable();
            $table->string('views_count', 1000)->nullable();
            $table->string('author_id', 1000)->nullable();
            $table->string('tags', 1000)->nullable();
            $table->string('highlight', 1000)->nullable();
            $table->string('status', 1000)->default('published');
            $table->longText('additional_images')->nullable()->charset('utf8mb4')->collation('utf8mb4_bin');
            $table->string('external_url', 1000)->nullable();
            $table->string('shares_count', 1000)->default('0');
            $table->timestamps();
            $table->string('status_thumb', 1000)->default('1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pages');
    }
}
