<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopHentaiAdultPosts2Table extends Migration // Note the class name also reflects '2'
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Ensure the table name is correct here: top_hentai_adult_posts2
        Schema::create('top_hentai_adult_posts2', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id')->unique(); // Crucial columns
            $table->integer('rank')->nullable();            // Crucial columns
            $table->timestamp('calculated_at')->useCurrent(); // Crucial columns
            $table->timestamps();

            // Foreign key constraint to the 'posts' table (optional but good practice)
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('top_hentai_adult_posts2'); // Ensure the table name is correct here
    }
}