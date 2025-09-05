<?php

namespace App\Http\Controllers;

use App\Model\Post;
use App\Model\UserList;
use App\Providers\MembersHelperServiceProvider;
use App\Providers\PostsHelperServiceProvider;
use Cookie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JavaScript;
use View;

class FeedController extends Controller
{
    public function index(Request $request)
    {
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0 ');

        $user = Auth::user();
        $postsFilter = $request->get('filter') ? $request->get('filter') : false;

        $followingList = $user->lists()->where('type', 'following')->first();
        $followingIds = [];
        if ($followingList) {
            $followingIds = $followingList->members()->pluck('user_id')->toArray();
        }
        $followingIds[] = $user->id;

        $postsQuery = Post::query()->whereIn('user_id', $followingIds);

        if (!$user->show_adult_content) {
            $postsQuery->where('is_adult_content', false);
        }

        if ($postsFilter) {
             $postsQuery->where('content_type', $postsFilter);
        }

        $posts = $postsQuery->latest()->paginate(10);

        $ajaxUrl = route('feed.posts');
        $nextPageUrl = null;
        if ($posts->nextPageUrl()) {
            $queryParams = parse_url($posts->nextPageUrl(), PHP_URL_QUERY);
            $nextPageUrl = $queryParams ? $ajaxUrl . '?' . $queryParams : $ajaxUrl;
        }
        $prevPageUrl = null;
        if ($posts->previousPageUrl()) {
            $queryParams = parse_url($posts->previousPageUrl(), PHP_URL_QUERY);
            $prevPageUrl = $queryParams ? $ajaxUrl . '?' . $queryParams : $ajaxUrl;
        }

        JavaScript::put([
            'paginatorConfig' => [
                'next_page_url' => $nextPageUrl,
                'prev_page_url' => $prevPageUrl,
                'current_page' => $posts->currentPage(),
                'total' => $posts->total(),
                'per_page' => $posts->perPage(),
                'hasMore' => $posts->hasMorePages(),
            ],
            'initialPostIDs' => $posts->pluck('id')->toArray(),
            'sliderConfig' => [
                'suggestions' => ['autoslide'=> getSetting('feed.feed_suggestions_autoplay') ? true : false],
                'expiredSubs' => ['autoslide'=> getSetting('feed.expired_subs_widget_autoplay') ? true : false],
            ],
            'user' => [
                'username' => Auth::user()->username,
                'user_id' => Auth::user()->id,
                'lists' => [
                    'blocked'=>Auth::user()->lists->firstWhere('type', 'blocked')->id,
                    'following'=>Auth::user()->lists->firstWhere('type', 'following')->id,
                ],
            ],
        ]);

        $data = [
            'posts' => $posts,
        ];
        if (!getSetting('feed.hide_suggestions_slider')){
            $data['suggestions'] = MembersHelperServiceProvider::getSuggestedMembers();
        }
        if (!getSetting('feed.expired_subs_widget_hide')){
            $data['expiredSubscriptions'] = MembersHelperServiceProvider::getExpiredSubscriptions();
        }
        return view('pages.feed', $data);
    }

    public function getFeedPosts(Request $request)
    {
        $user = Auth::user();
        $postsFilter = $request->get('filter') ? $request->get('filter') : false;

        $followingList = $user->lists()->where('type', 'following')->first();
        $followingIds = [];
        if ($followingList) {
            $followingIds = $followingList->members()->pluck('user_id')->toArray();
        }
        $followingIds[] = $user->id;

        $postsQuery = Post::query()->with('user')->whereIn('user_id', $followingIds);

        if (!$user->show_adult_content) {
            $postsQuery->where('is_adult_content', false);
        }
        
        if ($postsFilter) {
             $postsQuery->where('content_type', $postsFilter);
        }
        
        $posts = $postsQuery->latest()->paginate(10);
        
        // FIX: Check subscription for each post instead of hardcoding true
        $postsData = $posts->map(function ($post) use ($user) {
            $viewerId = $user->id;
            $postOwnerId = $post->user_id;
            
            // Check subscription status for each post
            if ($viewerId === $postOwnerId || $user->role_id === 1) {
                // Owner or admin
                $post->setAttribute('isSubbed', true);
            } elseif (getSetting('profiles.allow_users_enabling_open_profiles') && $post->user->open_profile) {
                // Open profile
                $post->setAttribute('isSubbed', true);
            } elseif (!$post->user->paid_profile) {
                // Free profile
                $post->setAttribute('isSubbed', true);
            } else {
                // Paid profile - check actual subscription
                $hasActiveSubscription = PostsHelperServiceProvider::hasActiveSub($viewerId, $postOwnerId);
                $post->setAttribute('isSubbed', $hasActiveSubscription);
            }
            
            return ['id' => $post->id, 'html' => View::make('elements.feed.post-box')->with('post', $post)->render()];
        });

        // Build next page URL like your index() method does
        $ajaxUrl = route('feed.posts');
        $nextPageUrl = null;
        if ($posts->nextPageUrl()) {
            $queryParams = parse_url($posts->nextPageUrl(), PHP_URL_QUERY);
            $nextPageUrl = $queryParams ? $ajaxUrl . '?' . $queryParams : $ajaxUrl;
        }
        $prevPageUrl = null;
        if ($posts->previousPageUrl()) {
            $queryParams = parse_url($posts->previousPageUrl(), PHP_URL_QUERY);
            $prevPageUrl = $queryParams ? $ajaxUrl . '?' . $queryParams : $ajaxUrl;
        }

        $data = [
            'posts' => $postsData,
            'hasMore' => $posts->hasMorePages(),
            'next_page_url' => $nextPageUrl,
            'prev_page_url' => $prevPageUrl,
            'current_page' => $posts->currentPage(),
            'total' => $posts->total(),
            'per_page' => $posts->perPage(),
        ];
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function filterSuggestedMembers(Request $request)
    {
        return response()->json(['success'=>true, 'data'=>MembersHelperServiceProvider::getSuggestedMembers(true, $request->get('filters'))]);
    }
}