<?php

namespace App\Http\Controllers;

use App\Model\UserGender;
use App\Model\Post;
use App\Model\Stream;
use App\User;
use App\Providers\MembersHelperServiceProvider;
use App\Providers\PostsHelperServiceProvider;
use App\Providers\StreamsServiceProvider;
use App\Providers\AttachmentServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use JavaScript;

class SearchController extends Controller
{
    /**
     * Main search/discover page - always shows discover content
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Avoid browser page caching
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0 ');

        // Get search term if provided
        $searchTerm = $request->get('query', '');
        $category = $request->get('category', ''); // For tag filtering

        // Get discovery content
        $discoveryData = $this->getDiscoveryContent($searchTerm, $category);
        
        // Available category tags (like Fansly)
        $categoryTags = [
            'cosplay' => __('Cosplay'),
            'anime' => __('Anime'), 
            'gaming' => __('Gaming'),
            'art' => __('Art'),
            'music' => __('Music'),
            'fitness' => __('Fitness'),
            'food' => __('Food'),
            'travel' => __('Travel'),
        ];

        JavaScript::put([
            'searchType' => 'discover',
            'sliderConfig' => [
                'madeForYou' => ['autoSlide' => true],
                'liveStreams' => ['autoSlide' => false],
                'featuredCreators' => ['autoSlide' => true],
                'suggestions' => ['autoslide' => getSetting('feed.feed_suggestions_autoplay') ? true : false],
                'expiredSubs' => ['autoslide' => getSetting('feed.expired_subs_widget_autoplay') ? true : false],
            ],
        ]);

        $viewData = array_merge($discoveryData, [
            'searchTerm' => $searchTerm,
            'categoryTags' => $categoryTags,
            'activeCategory' => $category,
        ]);

        if (!getSetting('feed.hide_suggestions_slider')){
            $viewData['suggestions'] = MembersHelperServiceProvider::getSuggestedMembers();
        }
        if (!getSetting('feed.expired_subs_widget_hide') && Auth::check()){
            $viewData['expiredSubscriptions'] = MembersHelperServiceProvider::getExpiredSubscriptions();
        }

        return view('pages.search', $viewData);
    }

    /**
     * Get discovery content for the main discovery feed
     * 
     * @param string $searchTerm
     * @param string $category
     * @return array
     */
    private function getDiscoveryContent($searchTerm = '', $category = '')
    {
        $data = [];

        // 1. "Made For You" - Top posts with search/category filtering
        $madeForYouPosts = $this->getMadeForYouContent($searchTerm, $category);
        
        // Process posts to add metadata for the blade template
        $madeForYouPosts->each(function ($post) {
            $post->setAttribute('isSubbed', true); // Make sure buttons work
            $post->load('user'); // Ensure user relationship is loaded
        });
        
        $data['madeForYouPosts'] = $madeForYouPosts;

        // 2. Live Streams
        $liveStreams = $this->getLiveStreams($searchTerm);
        $data['liveStreams'] = $liveStreams;

        // 3. Random Users (not just popular ones)
        $randomUsers = $this->getRandomUsers($searchTerm);
        $data['randomUsers'] = $randomUsers;

        // 4. Trending Videos with category filter
        $trendingVideos = $this->getTrendingVideos($searchTerm, $category);
        
        // Process trending videos to add metadata
        $trendingVideos->each(function ($post) {
            $post->setAttribute('isSubbed', true); // Make sure buttons work
            $post->load('user'); // Ensure user relationship is loaded
        });
        
        $data['trendingVideos'] = $trendingVideos;

        return $data;
    }

    /**
     * Get "Made For You" content - Top posts with video preference, respecting 18+ settings
     */
    private function getMadeForYouContent($searchTerm = '', $category = '')
    {
        $query = Post::with(['user', 'attachments'])
            ->where('status', Post::APPROVED_STATUS);

        // Apply 18+ content filter based on user settings
        if (Auth::check()) {
            $user = Auth::user();
            $settings = $user->settings;
            if (is_string($settings)) $settings = json_decode($settings, true);
            $settings = (array) $settings;
            $showAdultContent = isset($settings['show_adult_content']) && in_array($settings['show_adult_content'], [true, 'true', 1], true);
            
            if (!$showAdultContent) {
                $query->where('is_adult_content', false);
            }
            // If showAdultContent is true, we show both SFW and NSFW content (no filter needed)
        } else {
            // Guests never see adult content
            $query->where('is_adult_content', false);
        }

        // Apply search term filter
        if ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('text', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('user', function($userQuery) use ($searchTerm) {
                      $userQuery->where('username', 'like', '%' . $searchTerm . '%')
                               ->orWhere('name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        // Apply category filter
        if ($category) {
            $query->where('content_type', $category);
        }

        // Get TOP posts (most reactions and comments) with video preference
        $videoExtensions = AttachmentServiceProvider::getTypeByExtension('video');
        
        $posts = $query->whereHas('attachments', function ($q) use ($videoExtensions) {
                $q->whereIn('type', $videoExtensions);
            })
            ->withCount(['reactions', 'comments'])
            ->orderByDesc('reactions_count')
            ->orderByDesc('comments_count')
            ->orderByDesc('created_at')
            ->limit(12)
            ->get();

        // If not enough video posts, fill with TOP image posts
        if ($posts->count() < 12) {
            $imageExtensions = AttachmentServiceProvider::getTypeByExtension('image');
            $additionalQuery = Post::with(['user', 'attachments'])
                ->where('status', Post::APPROVED_STATUS)
                ->whereNotIn('id', $posts->pluck('id'));

            // Apply same 18+ filter to additional posts
            if (Auth::check()) {
                $user = Auth::user();
                $settings = $user->settings;
                if (is_string($settings)) $settings = json_decode($settings, true);
                $settings = (array) $settings;
                $showAdultContent = isset($settings['show_adult_content']) && in_array($settings['show_adult_content'], [true, 'true', 1], true);
                
                if (!$showAdultContent) {
                    $additionalQuery->where('is_adult_content', false);
                }
            } else {
                $additionalQuery->where('is_adult_content', false);
            }

            if ($searchTerm) {
                $additionalQuery->where(function($q) use ($searchTerm) {
                    $q->where('text', 'like', '%' . $searchTerm . '%')
                      ->orWhereHas('user', function($userQuery) use ($searchTerm) {
                          $userQuery->where('username', 'like', '%' . $searchTerm . '%')
                                   ->orWhere('name', 'like', '%' . $searchTerm . '%');
                      });
                });
            }

            if ($category) {
                $additionalQuery->where('content_type', $category);
            }

            $additionalPosts = $additionalQuery->whereHas('attachments', function ($q) use ($imageExtensions) {
                    $q->whereIn('type', $imageExtensions);
                })
                ->withCount(['reactions', 'comments'])
                ->orderByDesc('reactions_count')
                ->orderByDesc('comments_count')
                ->limit(12 - $posts->count())
                ->get();
            
            $posts = $posts->merge($additionalPosts);
        }

        return $posts;
    }

    /**
     * Get live streams with search filtering
     */
    private function getLiveStreams($searchTerm = '')
    {
        if (getSetting('streams.allow_streams') === 'none') {
            return collect();
        }

        $query = Stream::with('user')
            ->where('status', Stream::IN_PROGRESS_STATUS)
            ->where('is_public', 1);

        if ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('user', function($userQuery) use ($searchTerm) {
                      $userQuery->where('username', 'like', '%' . $searchTerm . '%')
                               ->orWhere('name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        return $query->orderByDesc('created_at')
                    ->limit(8)
                    ->get();
    }

    /**
     * Get random users (not just popular ones) - like Fansly's approach
     */
    private function getRandomUsers($searchTerm = '')
    {
        $query = User::where('email_verified_at', '!=', null)
            ->whereHas('posts', function ($postQuery) {
                $postQuery->where('status', Post::APPROVED_STATUS);
            });

        if ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('username', 'like', '%' . $searchTerm . '%')
                  ->orWhere('name', 'like', '%' . $searchTerm . '%');
            });
        }

        // Get random users - this is key for discovery
        return $query->inRandomOrder()
                    ->limit(12)
                    ->get();
    }

    /**
     * Get trending videos with search and category filtering, respecting 18+ settings
     */
    private function getTrendingVideos($searchTerm = '', $category = '')
    {
        $videoExtensions = AttachmentServiceProvider::getTypeByExtension('video');
        
        $query = Post::with(['user', 'attachments'])
            ->where('status', Post::APPROVED_STATUS)
            ->where('created_at', '>=', now()->subDays(7)); // Last week

        // Apply 18+ content filter based on user settings
        if (Auth::check()) {
            $user = Auth::user();
            $settings = $user->settings;
            if (is_string($settings)) $settings = json_decode($settings, true);
            $settings = (array) $settings;
            $showAdultContent = isset($settings['show_adult_content']) && in_array($settings['show_adult_content'], [true, 'true', 1], true);
            
            if (!$showAdultContent) {
                $query->where('is_adult_content', false);
            }
        } else {
            $query->where('is_adult_content', false);
        }

        if ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('text', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('user', function($userQuery) use ($searchTerm) {
                      $userQuery->where('username', 'like', '%' . $searchTerm . '%')
                               ->orWhere('name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        if ($category) {
            $query->where('content_type', $category);
        }

        return $query->whereHas('attachments', function ($q) use ($videoExtensions) {
                $q->whereIn('type', $videoExtensions);
            })
            ->withCount(['reactions', 'comments'])
            ->orderByDesc('reactions_count')
            ->orderByDesc('comments_count')
            ->limit(8)
            ->get();
    }

    /**
     * AJAX endpoint for switching between Accounts and Live Streams
     */
    public function getFeaturedContent(Request $request)
    {
        $type = $request->get('type', 'accounts'); // 'accounts' or 'streams'
        $searchTerm = $request->get('query', '');

        if ($type === 'streams') {
            $content = $this->getLiveStreams($searchTerm);
        } else {
            $content = $this->getRandomUsers($searchTerm);
        }

        return response()->json([
            'success' => true,
            'data' => $content,
            'type' => $type
        ]);
    }

    // Keep existing AJAX methods for backward compatibility
    public function getSearchPosts(Request $request)
    {
        $filters = $this->processFilterParams($request);
        return response()->json(['success'=>true, 'data'=>PostsHelperServiceProvider::getFeedPosts(Auth::user()->id, true, false, $filters['mediaType'], $filters['sortOrder'], $filters['searchTerm'])]);
    }

    public function getUsersSearch(Request $request)
    {
        $filters = $this->processFilterParams($request);
        return response()->json(['success'=>true, 'data'=> MembersHelperServiceProvider::getSearchUsers(array_merge(
            ['encodePostsToHtml'=>true, 'searchTerm' => $filters['searchTerm']],
            [
                'gender' => $request->get('gender'),
                'min_age' => $request->get('min_age'),
                'max_age' => $request->get('max_age'),
                'location' => $request->get('location'),
            ]
        ))]);
    }

    public function getStreamsSearch(Request $request)
    {
        $filters = $this->processFilterParams($request);
        return response()->json(['success'=>true, 'data'=> StreamsServiceProvider::getPublicStreams(['searchTerm' => $filters['searchTerm'], 'encodePostsToHtml'=>true, 'status' => 'live'])]);
    }

    protected function processFilterParams($request) {
        $searchTerm = $request->get('query') ? $request->get('query') : false;
        $postsFilter = $request->get('filter') ? $request->get('filter') : false;

        $mediaType = 'image';
        if($postsFilter == 'videos'){
            $mediaType = 'video';
        }
        if($postsFilter == 'photos'){
            $mediaType = 'image';
        }
        $sortOrder = '';
        if($postsFilter == 'top'){
            $mediaType = false;
            $sortOrder = 'top';
        }
        if($postsFilter == 'latest'){
            $mediaType = false;
            $sortOrder = 'latest';
        }

        return [
            'searchTerm' => $searchTerm,
            'postsFilter' => $postsFilter,
            'mediaType' => $mediaType,
            'sortOrder' => $sortOrder,
        ];
    }
}