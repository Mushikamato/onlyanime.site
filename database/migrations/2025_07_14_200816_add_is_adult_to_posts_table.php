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
        // Make sure 'status' exists in your 'posts' table. Adjust 'after' if needed.
        $table->boolean('is_adult')->default(false)->after('status');
    });
}

public function down(): void
{
    Schema::table('posts', function (Blueprint $table) {
        $table->dropColumn('is_adult');
    });
}
};
