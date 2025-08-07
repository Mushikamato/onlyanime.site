<?php

namespace App\Http\Controllers;

use App\Providers\InstallerServiceProvider;
use App\Providers\MembersHelperServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Model\Post;
use Illuminate\Support\Facades\DB; 

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Redirect if a homepage redirect is set
        if (getSetting('site.homepage_redirect')) {
            return Redirect::to(getSetting('site.homepage_redirect'), 301)
                ->header('Cache-Control', 'no-store, no-cache');
        }

        // Logic for the 'landing' page type
        if (getSetting('site.homepage_type') === 'landing') {
            $featuredMembers = MembersHelperServiceProvider::getFeaturedMembers(9);

            // --- START: FINAL CORRECTED LOGIC ---

            // Correctly check the user's dedicated 'show_adult_content' column.
            $applyNsfwFilter = !auth()->check() || !auth()->user()->show_adult_content;
            
            $getTopPostsForCategory = function($categoryName) use ($applyNsfwFilter) {
                
                // Use the CORRECT column name 'is_adult_content' in the filter.
                $nsfwFilterSql = $applyNsfwFilter ? "AND p.is_adult_content = 0" : "";

                // This query gets the post IDs, ordered by like count. It now includes
                // the NSFW filter and uses LEFT JOIN to include posts with zero likes.
                $topPostIdsQuery = "
                    SELECT p.id
                    FROM posts p
                    LEFT JOIN reactions r ON p.id = r.post_id
                    WHERE p.content_type = ? AND p.status = 1 {$nsfwFilterSql}
                    GROUP BY p.id
                    ORDER BY COUNT(r.id) DESC
                    LIMIT 30
                ";

                // Execute the query to get the ordered IDs
                $orderedPostIds = array_column(DB::select($topPostIdsQuery, [$categoryName]), 'id');

                // If no posts are found, return an empty collection
                if (empty($orderedPostIds)) {
                    return collect();
                }

                // Fetch the full Post models, forcing the order to match our query result
                return Post::with(['user', 'attachments'])
                    ->whereIn('id', $orderedPostIds)
                    ->orderByRaw('FIELD(id, ' . implode(',', $orderedPostIds) . ')')
                    ->get();
            };

            // Fetch the ordered posts for each category
            $cosplayPosts = $getTopPostsForCategory('cosplay');
            $animePosts   = $getTopPostsForCategory('anime');

            // --- END: FINAL CORRECTED LOGIC ---

            return view('pages.home', [
                'featuredMembers' => $featuredMembers,
                'cosplayPosts' => $cosplayPosts,
                'animePosts'   => $animePosts,
            ]);
        }

        // Fallback for other homepage types
        return view('pages.home', [
            'featuredMembers' => MembersHelperServiceProvider::getFeaturedMembers(9),
        ]);
    }
}