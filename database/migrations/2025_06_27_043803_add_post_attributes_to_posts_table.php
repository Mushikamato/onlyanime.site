<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Add column for 18+ content flag
            $table->boolean('is_adult_content')->default(false)->after('id'); // You can adjust 'after' to place it after a specific existing column, e.g., 'description'

            // Add column for content type (cosplay/hentai)
            $table->string('content_type')->nullable()->after('is_adult_content'); // Place it after the new 18+ column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Drop the columns if the migration is rolled back
            $table->dropColumn('is_adult_content');
            $table->dropColumn('content_type');
        });
    }
};