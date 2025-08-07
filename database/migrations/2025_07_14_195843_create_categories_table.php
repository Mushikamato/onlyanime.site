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
    Schema::create('categories', function (Blueprint $table) {
        $table->id();
        $table->string('name')->unique(); // e.g., 'Cosplay', 'Anime'
        $table->string('slug')->unique(); // URL-friendly version, e.g., 'cosplay', 'hentai'
        $table->boolean('is_adult')->default(false); // To mark if a category is for adult content
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
