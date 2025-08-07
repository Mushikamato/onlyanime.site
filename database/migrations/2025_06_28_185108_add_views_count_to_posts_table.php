<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddViewsCountToPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            // Add the views_count column as an integer, defaulting to 0
            // You can adjust 'after' to place it after any existing column you prefer,
            // e.g., after 'status', 'price', etc.
            $table->integer('views_count')->default(0)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            // This will remove the views_count column if you ever roll back this migration
            $table->dropColumn('views_count');
        });
    }
}