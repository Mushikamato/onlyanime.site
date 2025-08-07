<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Model\Category; // <--- Make sure this line is present to import the Category model

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or find 'Cosplay' category (non-adult)
        Category::firstOrCreate(
            ['slug' => 'cosplay'], // Search criteria: try to find a category with this slug
            ['name' => 'Cosplay', 'is_adult' => false] // Data to create if not found
        );

        // Create or find 'Anime' category (non-adult), as you changed your mind from Hentai
        Category::firstOrCreate(
            ['slug' => 'anime'],
            ['name' => 'Anime', 'is_adult' => false]
        );

        // If you still want 'Hentai' as an option for other purposes (e.g., if some posts might still be 'Hentai' though not displayed on the main slider), you can include it.
        // If not, simply omit this block.
        Category::firstOrCreate(
            ['slug' => 'hentai'],
            ['name' => 'Hentai', 'is_adult' => true] // Marking as adult
        );
    }
}