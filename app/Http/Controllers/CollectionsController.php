<?php

namespace App\Http\Controllers;

use App\Providers\AttachmentServiceProvider;
use App\Providers\PostsHelperServiceProvider;
use App\Providers\ListsHelperServiceProvider;
use App\Model\UserList;
use Cookie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JavaScript;

class CollectionsController extends Controller
{
    /**
     * Available bookmark types (reused from BookmarksController).
     * @var array
     */
    public $bookmarkTypes = [
        'all' => ['heading' => 'All Bookmarks', 'icon' => 'bookmarks'],
        'photos' => ['heading' => 'Photos', 'icon' => 'image'],
        'videos' => ['heading' => 'Videos', 'icon' => 'videocam'],
        'audio' => ['heading' => 'Audio', 'icon' => 'musical-notes'],
        'locked' => ['heading' => 'Locked', 'icon' => 'lock-closed'],
    ];

    /**
     * Main collections page - displays unified bookmarks and lists interface.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Default to bookmarks tab
        $activeTab = $request->get('tab', 'bookmarks');
        
        // Avoid browser caching
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache'); 
        header('Expires: 0');

        $data = [
            'activeTab' => $activeTab,
            'bookmarkTypes' => $this->bookmarkTypes,
        ];

        if ($activeTab === 'bookmarks') {
            // Get bookmarks data (reuse existing logic)
            $type = AttachmentServiceProvider::getActualTypeByBookmarkCategory($request->get('type', 'all'));
            $startPage = PostsHelperServiceProvider::getFeedStartPage(PostsHelperServiceProvider::getPrevPage($request));
            $posts = PostsHelperServiceProvider::getUserBookmarks(Auth::user()->id, false, $startPage, $type);
            
            $data['posts'] = $posts;
            $data['activeBookmarkType'] = $request->get('type', 'all');
            
            JavaScript::put([
                'paginatorConfig' => [
                    'next_page_url' => $posts->nextPageUrl(),
                    'prev_page_url' => $posts->previousPageUrl(),
                    'current_page' => $posts->currentPage(),
                    'total' => $posts->total(),
                    'per_page' => $posts->perPage(),
                    'hasMore' => $posts->hasMorePages(),
                ],
                'initialPostIDs' => $posts->pluck('id')->toArray(),
                'collectionTab' => 'bookmarks'
            ]);

        } elseif ($activeTab === 'lists') {
            // Get lists data (reuse existing logic)
            $lists = ListsHelperServiceProvider::getUserLists();
            $followersList = ListsHelperServiceProvider::getUserFollowersList();
            $lists->splice(1, 0, [$followersList]);
            
            $data['lists'] = $lists;
            
            // If a specific list is requested, get its contents
            $listId = $request->get('list_id');
            if ($listId) {
                $selectedList = $lists->firstWhere('id', $listId);
                if ($selectedList) {
                    $data['selectedList'] = $selectedList;
                    $data['listMembers'] = $selectedList->members ?? [];
                    
                    // Get posts from list members (similar to existing lists functionality)
                    if (isset($selectedList->members) && count($selectedList->members) > 0) {
                        $memberIds = collect($selectedList->members)->pluck('id')->toArray();
                        $listPosts = PostsHelperServiceProvider::getPostsFromUsers($memberIds);
                        $data['listPosts'] = $listPosts;
                    }
                }
            }
            
            JavaScript::put([
                'collectionTab' => 'lists',
                'selectedListId' => $listId
            ]);
        }

        return view('pages.collections', $data);
    }

    /**
     * AJAX handler for bookmarks filtering within collections.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBookmarks(Request $request)
    {
        $type = AttachmentServiceProvider::getActualTypeByBookmarkCategory($request->get('type', 'all'));
        $startPage = $request->get('page', 1);
        
        $posts = PostsHelperServiceProvider::getUserBookmarks(Auth::user()->id, true, $startPage, $type);
        
        return response()->json([
            'success' => true,
            'data' => $posts,
            'hasMore' => $posts->hasMorePages(),
            'nextPage' => $posts->currentPage() + 1
        ]);
    }

    /**
     * Search within collections (both bookmarks and lists).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $tab = $request->get('tab', 'bookmarks');
        
        if (empty($query)) {
            return response()->json(['success' => false, 'message' => 'Search query required']);
        }

        $results = [];

        if ($tab === 'bookmarks') {
            // Search through bookmarked posts
            $posts = PostsHelperServiceProvider::getUserBookmarks(Auth::user()->id, true, false);
            // Filter posts by search query (you can enhance this logic)
            $results = $posts->filter(function($post) use ($query) {
                return stripos($post->message, $query) !== false || 
                       stripos($post->user->username, $query) !== false;
            });
        } elseif ($tab === 'lists') {
            // Search through user lists
            $lists = ListsHelperServiceProvider::getUserLists();
            $results = $lists->filter(function($list) use ($query) {
                return stripos($list->name, $query) !== false;
            });
        }

        return response()->json([
            'success' => true,
            'data' => $results,
            'query' => $query,
            'tab' => $tab
        ]);
    }
}