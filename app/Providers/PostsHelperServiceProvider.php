<?php

namespace App\Providers;

use App\Model\Attachment;
use App\Model\Poll;
use App\Model\PollAnswer;
use App\Model\PollUserAnswer;
use App\Model\Post;
use App\Model\PostComment;
use App\Model\Stream;
use App\Model\Subscription;
use App\Model\Transaction;
use App\Model\UserList;
use App\User;
use Carbon\Carbon;
use Cookie;
use DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use View;

class PostsHelperServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        //
    }

    public static function getLatestUserAttachments($userID = false, $type = false)
    {
        if (!$userID) {
            if (Auth::check()) {
                $userID = Auth::user()->id;
            } else {
                throw new \Exception(__('Can not fetch latest post attachments for this profile.'));
            }
        }
        $attachments = Attachment::with(['post'])->where('attachments.post_id', '<>', null)->where('attachments.user_id', $userID);
        if ($type) {
            $extensions = AttachmentServiceProvider::getTypeByExtension('image');
            $attachments->whereIn('attachments.type', $extensions);
        }
        if(Auth::check() && Auth::user()->role_id !== 1 && Auth::user()->id !== $userID) {
            $attachments->leftJoin('posts', 'posts.id', '=', 'attachments.post_id')
                ->leftJoin('transactions', 'transactions.post_id', '=', 'posts.id')
                ->where(function ($query) {
                    $query->where('posts.price', '=', floatval(0))
                        ->orWhere(function ($query) {
                            $query->where('transactions.id', '<>', null)
                                ->where('transactions.type', '=', Transaction::POST_UNLOCK)
                                ->where('transactions.status', '=', Transaction::APPROVED_STATUS)
                                ->where('transactions.sender_user_id', '=', Auth::user()->id);
                        });
                })
                ->where(function ($query) {
                    $query->where('posts.expire_date', '>', Carbon::now());
                    $query->orWhere('posts.expire_date', null);
                })
                ->where(function ($query) {
                    $query->where('posts.release_date', '<', Carbon::now());
                    $query->orWhere('posts.release_date', null);
                })
                ->where('posts.status', 1);
        }
        $attachments = $attachments->limit(3)->orderByDesc('attachments.created_at')->get();
        return $attachments;
    }

    public static function getUserByUsername($username)
    {
        return User::where('username', $username)->first();
    }

    public static function getUserActiveSubs($userID)
    {
        $activeSubs = Subscription::where('sender_user_id', $userID)
            ->where(function ($query) {
                $query->where('status', 'completed')
                    ->orwhere([
                        ['status', '=', 'canceled'],
                        ['expires_at', '>', Carbon::now()->toDateTimeString()],
                    ]);
            })
            ->get()
            ->pluck('recipient_user_id')->toArray();
        return $activeSubs;
    }

    public static function getFreeFollowingProfiles($userId) {
        $followingList = UserList::where('user_id', $userId)->where('type', 'following')->with(['members', 'members.user'])->first();
        $followingUserIds = [];
        if ($followingList) {
            foreach($followingList->members as $member){
                if(isset($member->user) && (!$member->user->paid_profile || (getSetting('profiles.allow_users_enabling_open_profiles') && $member->user->open_profile))){
                    $followingUserIds[] = $member->user->id;
                }
            }
        }
        return $followingUserIds;
    }

    public static function hasActiveSub($sender_id, $recipient_id)
    {
        $hasSub = Subscription::where('sender_user_id', $sender_id)
            ->where('recipient_user_id', $recipient_id)
            ->where(function ($query) {
                $query->where('status', 'completed')
                    ->orwhere([
                        ['status', '=', 'canceled'],
                        ['expires_at', '>', Carbon::now()->toDateTimeString()],
                    ]);
            })
            ->count();
        if ($hasSub > 0) {
            return true;
        }
        return false;
    }

    public static function getFeedPosts($userID, $encodePostsToHtml = false, $pageNumber = false, $mediaType = false, $sortOrder = false, $searchTerm = '')
    {
        return self::getFilteredPosts($userID, $encodePostsToHtml, $pageNumber, $mediaType, false, false, false, $sortOrder, $searchTerm);
    }

    public static function getUserPosts($userID, $encodePostsToHtml = false, $pageNumber = false, $mediaType = false, $hasSub = false)
    {
        return self::getFilteredPosts($userID, $encodePostsToHtml, $pageNumber, $mediaType, true, $hasSub, false);
    }

    public static function getUserBookmarks($userID, $encodePostsToHtml = false, $pageNumber = false, $mediaType = false, $hasSub = false)
    {
        return self::getFilteredPosts($userID, $encodePostsToHtml, $pageNumber, $mediaType, false, $hasSub, true);
    }

    public static function getFilteredPosts($userID, $encodePostsToHtml, $pageNumber, $mediaType, $ownPosts, $hasSub, $bookMarksOnly, $sortOrder = false, $searchTerm = '')
    {
        // *** THIS IS THE FIX ***
        // Removed 'categories' from the relations to prevent the crash
        $relations = ['user', 'reactions', 'attachments', 'bookmarks', 'postPurchases'];
        $posts = Post::withCount('tips')->with($relations);

        // ================= START: REWRITTEN LOGIC =================

        // Step 1: Determine the initial set of users whose posts we should look at (the "user scope").
        if ($mediaType === 'cosplay' || $mediaType === 'anime') {
            // For category pages, we start with ALL posts from ALL users.
            // No user filter is applied at this stage.
        }
        elseif ($ownPosts) {
            // This is for viewing a specific user's profile page.
            $posts->where('user_id', $userID);
        }
        elseif ($bookMarksOnly) {
            // This is for the user's bookmarks page.
            $posts = self::filterPosts($posts, $userID, 'bookmarks');
        }
        else {
            // This is for the default main feed (Home). It gets posts from followed/subscribed users, plus the user's own posts.
            $posts = self::filterPosts($posts, $userID, 'all');
        }

        // Step 2: Apply content filters to the user scope we just defined.

        // Apply 18+ filter. When ON, it shows SFW + NSFW. When OFF, it shows SFW only.
        if (Auth::check()) {
            $user = Auth::user();
            $settings = $user->settings;
            if (is_string($settings)) $settings = json_decode($settings, true);
            $settings = (array) $settings;
            $showAdultContent = isset($settings['show_adult_content']) && in_array($settings['show_adult_content'], [true, 'true', 1], true);
            if (!$showAdultContent) {
                $posts->where('is_adult_content', false);
            }
        } else {
            $posts->where('is_adult_content', false); // Guests never see adult content.
        }

        // *** THIS IS THE FINAL FIX ***
        // Apply category filter using the correct 'content_type' column
        if ($mediaType === 'cosplay' || $mediaType === 'anime') {
            $posts->where('content_type', $mediaType);
        }
        // *** END OF FIX ***
        
        // Apply other media filters (e.g., 'image', 'video') for other pages
        else if ($mediaType) {
            $posts = self::filterPosts($posts, $userID, 'media', $mediaType);
        }

        // Step 3: Apply universal filters that apply to all queries.

        // This is the new, corrected status filter.
        // If the user is NOT an admin, they only see approved posts. Admins see everything.
        if (!(Auth::check() && Auth::user()->role_id === 1)) {
            $posts->where('status', Post::APPROVED_STATUS);
        }

        // Always remove posts from users you have blocked.
        if (Auth::check()) {
            $posts = self::filterPosts($posts, $userID, 'blocked');
        }

        // Hide scheduled posts unless you are viewing your own profile.
        if (!$ownPosts) {
            $posts = self::filterPosts($posts, $userID, 'scheduled');
        }
        
        // Add pinned post logic for user profiles.
        if ($ownPosts) {
             $posts = self::filterPosts($posts, $userID, 'pinned');
        }

        if($searchTerm){
            $posts = self::filterPosts($posts, $userID, 'search', false, false, $searchTerm);
        }

        $posts = self::filterPosts($posts, $userID, 'order', false, $sortOrder);

        // ================= END: REWRITTEN LOGIC =================

        if ($pageNumber) {
            $posts = $posts->paginate(getSetting('feed.feed_posts_per_page'), ['*'], 'page', $pageNumber)->appends(request()->query());
        } else {
            $posts = $posts->paginate(getSetting('feed.feed_posts_per_page'))->appends(request()->query());
        }

        if(Auth::check() && Auth::user()->role_id === 1){
            $hasSub = true;
        }

        if ($encodePostsToHtml) {
            $data = [
                'total' => $posts->total(),
                'currentPage' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'prev_page_url' => $posts->previousPageUrl(),
                'next_page_url' => $posts->nextPageUrl(),
                'first_page_url' => $posts->nextPageUrl(),
                'hasMore' => $posts->hasMorePages(),
            ];
            $postsData = $posts->map(function ($post) use ($hasSub, $ownPosts, $data) {
                if ($ownPosts) {
                    $post->setAttribute('isSubbed', $hasSub);
                } else {
                    $post->setAttribute('isSubbed', true);
                }
                $post->setAttribute('postPage', $data['currentPage']);
                $post = ['id' => $post->id, 'html' => View::make('elements.feed.post-box')->with('post', $post)->render()];
                return $post;
            });
            $data['posts'] = $postsData;
        } else {
            $postsCurrentPage = $posts->currentPage();
            $posts->map(function ($post) use ($hasSub, $ownPosts, $postsCurrentPage) {
                if ($ownPosts) {
                    $post->hasSub = $hasSub;
                    $post->setAttribute('isSubbed', $hasSub);
                } else {
                    $post->setAttribute('isSubbed', true);
                }
                $post->setAttribute('postPage', $postsCurrentPage);
                return $post;
            });
            $data = $posts;
        }
        return $data;
    }

    public static function filterPosts($posts, $userID, $filterType, $mediaType = false, $sortOrder = false, $searchTerm = '')
    {
        if ($filterType == 'blocked') {
             if(Auth::check() && Auth::user()->lists){
                $blockedList = Auth::user()->lists->firstWhere('type', 'blocked');
                if ($blockedList) {
                    $blockedUsers = ListsHelperServiceProvider::getListMembers($blockedList->id);
                    if(count($blockedUsers) > 0){
                        $posts->whereNotIn('posts.user_id', $blockedUsers);
                    }
                }
            }
        }
        if ($filterType == 'all') { // This is now ONLY for the main feed
            // Also include the user's own posts in their main feed
            $userIds = array_merge(self::getUserActiveSubs($userID), self::getFreeFollowingProfiles($userID), [$userID]);
            if(count($userIds) > 0){
                 $posts->whereIn('posts.user_id', $userIds);
            } else {
                // If user follows no one, just show their own posts
                $posts->where('user_id', $userID);
            }
        }
        if ($filterType == 'bookmarks') {
            $posts->join('user_bookmarks', function ($join) use ($userID) {
                $join->on('user_bookmarks.post_id', '=', 'posts.id');
                $join->on('user_bookmarks.user_id', '=', DB::raw($userID));
            });
            $posts->orderBy('user_bookmarks.created_at', 'DESC');
            $userIds = array_merge(self::getUserActiveSubs($userID), self::getFreeFollowingProfiles($userID), [$userID]);
            $posts->whereIn('posts.user_id', $userIds);
        }
        if ($filterType == 'media') {
            $mediaTypes = AttachmentServiceProvider::getTypeByExtension($mediaType);
            $posts->whereHas('attachments', function ($query) use ($mediaTypes) {
                $query->whereIn('type', $mediaTypes);
            });
        }
        if ($filterType == 'search'){
            $posts->where(
                function ($query) use ($searchTerm) {
                    $query->where('text', 'like', '%'.$searchTerm.'%')
                        ->orWhereHas('user', function ($q) use ($searchTerm) {
                            $q->where('username', 'like', '%'.$searchTerm.'%');
                            $q->orWhere('name', 'like', '%'.$searchTerm.'%');
                        });
                }
            );
        }
        if ($filterType == 'pinned'){
            $posts->orderBy('is_pinned', 'DESC');
        }
        if ($filterType == 'order'){
            if($sortOrder){
                if($sortOrder == 'top'){
                    $relationsCount = ['reactions', 'comments'];
                    $posts->withCount($relationsCount);
                    $posts->orderBy('comments_count', 'DESC');
                    $posts->orderBy('reactions_count', 'DESC');
                }
                elseif($sortOrder == 'latest'){
                    $posts->orderBy('created_at', 'DESC');
                }
            }
            else{
                $posts->orderBy('created_at', 'DESC');
            }
        }
        if ($filterType == 'scheduled') {
            $posts->notExpiredAndReleased();
        }
        // NOTE: The 'approvedPostsOnly' filter has been moved to the main function for clarity.
        return $posts;
    }

    // ... The rest of the file remains the same ...
    public static function getPostComments($post_id, $limit = 9, $order = 'DESC', $encodePostsToHtml = false)
    {
        $comments = PostComment::with(['author', 'reactions'])->orderBy('created_at', $order)->where('post_id', $post_id)->paginate($limit);
        if ($encodePostsToHtml) {
            $data = [
                'total' => $comments->total(),
                'currentPage' => $comments->currentPage(),
                'last_page' => $comments->lastPage(),
                'prev_page_url' => $comments->previousPageUrl(),
                'next_page_url' => $comments->nextPageUrl(),
                'first_page_url' => $comments->nextPageUrl(),
                'hasMore' => $comments->hasMorePages(),
            ];
            $commentsData = $comments->map(function ($comment) {
                $post = ['id' => $comment->id, 'post_id' => $comment->post->id, 'html' => View::make('elements.feed.post-comment')->with('comment', $comment)->render()];
                return $post;
            });
            $data['comments'] = $commentsData;
        } else {
            $data = $comments;
        }
        return $data;
    }

    public static function hasUserUnlockedPost($transactions)
    {
        if (Auth::check()) {
            if(Auth::user()->role_id === 1) {
                return true;
            }
            foreach ($transactions as $transaction) {
                if (Auth::user()->id == $transaction->sender_user_id) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function didUserReact($reactions)
    {
        if (Auth::check()) {
            foreach ($reactions as $reaction) {
                if (Auth::user()->id == $reaction->user_id) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function isPostBookmarked($bookmarks)
    {
        if (Auth::check()) {
            foreach ($bookmarks as $bookmark) {
                if (Auth::user()->id == $bookmark->user_id) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function isComingFromPostPage($page)
    {
        if (isset($page) && isset($page['url']) && is_int(strpos($page['url'], '/posts')) && !is_int(strpos($page['url'], '/posts/create'))) {
            return true;
        }
        return false;
    }

    public static function getFeedStartPage($prevPage)
    {
        return Cookie::get('app_feed_prev_page') && self::isComingFromPostPage($prevPage) ? Cookie::get('app_feed_prev_page') : 1;
    }

    public static function getPrevPage($request)
    {
        return $request->session()->get('_previous');
    }

    public static function shouldDeletePaginationCookie($request)
    {
        if (!self::isComingFromPostPage(self::getPrevPage($request))) {
            Cookie::queue(Cookie::forget('app_feed_prev_page'));
            Cookie::queue(Cookie::forget('app_prev_post'));
            return true;
        }
        return false;
    }

    public static function getUserMediaTypesCount($userID)
    {
        $attachments = Attachment::
        leftJoin('posts', 'posts.id', '=', 'attachments.post_id')
            ->where('attachments.user_id', $userID)->where('post_id', '<>', null)
            ->where(function ($query) {
                $query->where('posts.expire_date', '>', Carbon::now());
                $query->orWhere('posts.expire_date', null);
            })
            ->where(function ($query) {
                $query->where('posts.release_date', '<', Carbon::now());
                $query->orWhere('posts.release_date', null);
            })
            ->get();
        $typeCounts = [
            'video' => 0,
            'audio' => 0,
            'image' => 0,
        ];
        foreach ($attachments as $attachment) {
            $typeCounts[AttachmentServiceProvider::getAttachmentType($attachment->type)] += 1;
        }
        $streams = Stream::where('user_id', $userID)->where('is_public', 1)->whereIn('status', [Stream::ENDED_STATUS, Stream::IN_PROGRESS_STATUS])->count();
        $typeCounts['streams'] = $streams;
        return $typeCounts;
    }

    public static function userPaidForPost($userId, $postId) {
        return Transaction::query()->where(
            [
                    'post_id' => $postId,
                    'sender_user_id' => $userId,
                    'type' => Transaction::POST_UNLOCK,
                    'status' => Transaction::APPROVED_STATUS,
                ]
        )->first() != null;
    }

    public static function userPaidForStream($userId, $streamId) {
        return Transaction::query()->where(
            [
                    'stream_id' => $streamId,
                    'sender_user_id' => $userId,
                    'type' => Transaction::STREAM_ACCESS,
                    'status' => Transaction::APPROVED_STATUS,
                ]
        )->first() != null;
    }

    public static function userPaidForMessage($userId, $messageId) {
        return Transaction::query()->where(
            [
                    'user_message_id' => $messageId,
                    'sender_user_id' => $userId,
                    'type' => Transaction::MESSAGE_UNLOCK,
                    'status' => Transaction::APPROVED_STATUS,
                ]
        )->first() != null;
    }

    public static function getUserApprovedPostsCount($userID) {
        return $postsCount = Post::where([
            'user_id' =>  $userID,
            'status' => Post::APPROVED_STATUS,
        ])->count();
    }

    public static function getPostsCountLeftTillAutoApprove($userID) {
        return (int)getSetting('compliance.admin_approved_posts_limit') - self::getUserApprovedPostsCount(Auth::user()->id);
    }

    public static function getDefaultPostStatus($userID) {
        $postStatus = Post::APPROVED_STATUS;
        if(getSetting('compliance.admin_approved_posts_limit')){
            $postsCount = self::getUserApprovedPostsCount($userID);
            if((int)getSetting('compliance.admin_approved_posts_limit') > $postsCount){
                $postStatus = Post::PENDING_STATUS;
            }
        }
        return $postStatus;
    }

    public static function getAttachmentsTypesCount($attachments) {
        $counts = [
            'image' => 0,
            'video' => 0,
            'audio' => 0,
        ];
        foreach($attachments as $attachment){
            if(isset($counts[AttachmentServiceProvider::getAttachmentType($attachment->type)])){
                $counts[AttachmentServiceProvider::getAttachmentType($attachment->type)] += 1;
            }
        }
        return $counts;
    }

    public static function hasNoMedia($attachments): bool {
        $counts = self::getAttachmentsTypesCount($attachments);
        return array_sum($counts) === 0;
    }

    public static function sendPostNotifications()
    {
        $followers = ListsHelperServiceProvider::getUserFollowers(Auth::user()->id);
        foreach($followers as $follower){
            $serializedSettings = json_decode($follower['settings']);
            if(isset($serializedSettings->notification_email_new_post_created) && $serializedSettings->notification_email_new_post_created == 'true'){
                App::setLocale($serializedSettings->locale);
                EmailsServiceProvider::sendGenericEmail(
                    [
                        'email' => $follower['email'],
                        'subject' => __('New content from @:username', ['username' => Auth::user()->username]),
                        'title' => __('Hello, :name,', ['name'=>$follower['name']]),
                        'content' => __('New content from people you follow is available', ['siteName'=>getSetting('site.name')]),
                        'button' => [
                            'text' => __('View your feed'),
                            'url' => route('feed'),
                        ],
                    ]
                );
                App::setLocale(Auth::user()->settings['locale']);
            }
        }
    }

    public static function sendAdminPostsApprovalNotifications()
    {
        $adminEmails = User::where('role_id', 1)->select(['email', 'name'])->get();
        foreach ($adminEmails as $user) {
            EmailsServiceProvider::sendGenericEmail(
                [
                    'email' => $user->email,
                    'subject' => __('Action required | New post pending approval'),
                    'title' => __('Hello, :name,', ['name' => $user->name]),
                    'content' => __('There is a new post pending your approval on :siteName.', ['siteName' => getSetting('site.name')]),
                    'button' => [
                        'text' => __('Go to admin'),
                        'url' => route('voyager.dashboard').'/user-posts?key=status&filter=equals&s=0',
                    ],
                ]
            );
        }
    }

    public static function createNewPoll($postID, $pollAnswers)
    {
        $pollID = Poll::create([
            'user_id' => Auth::user()->id,
            'post_id' => $postID,
            'ends_at' => null,
        ])->id;
        foreach($pollAnswers as $pollAnswer){
            PollAnswer::create([
                'poll_id' => $pollID,
                'answer' => $pollAnswer['value'],
            ]);
        }
        return true;
    }

    public static function updatePoll($post, $pollAnswers)
    {
        $existingAnswers = $post->poll->answers->keyBy('id');
        foreach ($pollAnswers as $answerData) {
            $answerId = $answerData['id'] ?? null;
            $answerValue = $answerData['value'];
            if ($answerId && $existingAnswers->has($answerId)) {
                $existingAnswers[$answerId]->update([
                    'answer' => $answerValue,
                ]);
                $existingAnswers->forget($answerId);
            } else {
                PollAnswer::create([
                    'poll_id' => $post->poll->id,
                    'answer'  => $answerValue,
                ]);
            }
        }
        foreach ($existingAnswers as $toDelete) {
            $toDelete->delete();
        }
        return true;
    }

    public static function hasUserVotedInPoll($pollID)
    {
        if(Auth::check()) {
            $pollAnswer = PollUserAnswer::where('user_id', Auth::user()->id)->where('poll_id', $pollID)->first();
            if ($pollAnswer) {
                return $pollAnswer->answer->id;
            }
        }
        return null;
    }

    public static function getPollResults($poll)
    {
        $totalVotes = $poll->answers->reduce(function ($carry, $answer) {
            return $carry + $answer->votes->count();
        }, 0);
        $results = $poll->answers->map(function ($answer) use ($totalVotes) {
            $votesCount = $answer->votes->count();
            return [
                'id'        => $answer->id,
                'answer'    => $answer->answer,
                'votes'     => $votesCount,
                'percentage'=> $totalVotes > 0
                    ? round(($votesCount / $totalVotes) * 100, 2)
                    : 0,
            ];
        });
        return collect([
            'totalVotes' => $totalVotes,
            'answers' => $results,
        ]);
    }

    public static function isPostSubscriptionUnlocked($post) {
        return $post->isSubbed || (getSetting('profiles.allow_users_enabling_open_profiles') && $post->user->open_profile);
    }

    public static function shouldHidePostText($post) {
        $isLoggedIn = Auth::check();
        $isOwner = $isLoggedIn && Auth::id() === $post->user_id;
        $isSubscriptionUnlocked = self::isPostSubscriptionUnlocked($post);
        $isPPVLocked = (!$isOwner && $post->price > 0 && (!$isLoggedIn || !self::hasUserUnlockedPost($post->postPurchases)));
        $isTextPreviewDisabled = getSetting('feed.disable_posts_text_preview');
        $shouldHideText = $isTextPreviewDisabled && ($isPPVLocked || !$isSubscriptionUnlocked);
        return $shouldHideText;
    }
}
