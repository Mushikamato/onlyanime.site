<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\Post; // Ensure this is the correct namespace for your Post model
use App\Model\TopCosplayAdultPost; // Ensure these models exist and map to their tables
use App\Model\TopCosplayNotAdultPost;
// Use TopHentaiAdultPost if it maps to top_hentai_adult_posts (without 2)
// OR create TopHentaiAdultPost2 model and use it if you stick with top_hentai_adult_posts2 table
use App\Model\TopHentaiAdultPost; // You might need to create App\Model\TopHentaiAdultPost2 if using that table
use App\Model\TopHentaiNotAdultPost;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CalculateTopPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:calculate-top-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculates and populates the top posts for various categories.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting top posts calculation...');

        $approvedStatus = Post::APPROVED_STATUS;

        $categories = [
            'cosplay_adult' => [
                'model' => TopCosplayAdultPost::class,
                'content_type' => 'cosplay',
                'is_adult_content' => 1,
            ],
            'cosplay_not_adult' => [
                'model' => TopCosplayNotAdultPost::class,
                'content_type' => 'cosplay',
                'is_adult_content' => 0,
            ],
            'hentai_adult' => [
                // IMPORTANT: If you are using 'top_hentai_adult_posts2' table,
                // you might need to create an 'App\Model\TopHentaiAdultPost2' model
                // and use 'TopHentaiAdultPost2::class' here.
                // Otherwise, ensure TopHentaiAdultPost maps to your intended 'hentai adult' table.
                'model' => TopHentaiAdultPost::class, // Assuming this model maps to top_hentai_adult_posts OR top_hentai_adult_posts2 via $table property
                'content_type' => 'hentai',
                'is_adult_content' => 1,
            ],
            'hentai_not_adult' => [
                'model' => TopHentaiNotAdultPost::class,
                'content_type' => 'hentai',
                'is_adult_content' => 0,
            ],
        ];

        foreach ($categories as $key => $config) {
            $this->info("Processing category: {$key}...");

            $topPostsQuery = Post::where('status', $approvedStatus)
                                ->where('content_type', $config['content_type'])
                                ->where('is_adult_content', $config['is_adult_content'])
                                // Consider if you want to include posts that are not yet released or already expired
                                // If not, keep scopeNotExpiredAndReleased. If yes, remove it.
                                ->notExpiredAndReleased() // This scope filters posts by release_date and expire_date
                                ->orderBy('views_count', 'desc') // Primary ranking: most viewed
                                ->orderBy('created_at', 'desc')  // Secondary ranking: most recent for ties
                                ->limit(100); // Limit to top N posts

            $topPostIds = $topPostsQuery->pluck('id')->toArray();

            // Use a transaction to ensure data integrity during update
            DB::beginTransaction();
            try {
                // Clear existing top posts for this category
                // Using delete() to ensure foreign key cascades (if configured) work correctly.
                $config['model']::query()->delete();

                // Insert new top posts
                $dataToInsert = [];
                foreach ($topPostIds as $index => $postId) {
                    $dataToInsert[] = [
                        'post_id' => $postId,
                        'rank' => $index + 1, // Rank from 1 to 100
                        'calculated_at' => Carbon::now(),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }

                if (!empty($dataToInsert)) {
                    $config['model']::insert($dataToInsert);
                    $this->info("Successfully updated {$key} with " . count($dataToInsert) . " posts.");
                } else {
                    $this->warn("No posts found for {$key} category to update.");
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Failed to update {$key}: " . $e->getMessage());
                return Command::FAILURE;
            }
        }

        $this->info('Top posts calculation completed.');
        return Command::SUCCESS;
    }
}