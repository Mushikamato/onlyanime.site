<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopCosplayAdultPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('top_cosplay_adult_posts', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->unsignedBigInteger('post_id')->unique(); // Store the ID of the post
            $table->integer('rank')->nullable(); // Optional: Store the rank (1 to 100)
            $table->timestamp('calculated_at')->useCurrent(); // When this entry was calculated
            $table->timestamps(); // created_at and updated_at

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
        Schema::dropIfExists('top_cosplay_adult_posts');
    }
}