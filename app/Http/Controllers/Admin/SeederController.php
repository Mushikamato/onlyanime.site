<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\User;
use App\Model\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SeederController extends Controller
{
    /**
     * Show the seeder tool page.
     */
    public function index()
    {
        // We only need to fetch users for the dropdown
        $users = User::all();
        return view('admin.seeder.index', ['users' => $users]);
    }

    /**
     * This method will create the posts and reactions with the new logic.
     */
    public function seedPosts(Request $request)
    {
        // 1. Validate the form input, including the new fields
        $request->validate([
            'attachments' => 'required|array',
            'attachments.*' => 'image|mimes:jpeg,png,jpg,gif,webp',
            'user_id' => 'required|exists:users,id',
            'post_type' => ['required', Rule::in(['cosplay_sfw', 'cosplay_nsfw', 'anime_sfw', 'anime_nsfw'])],
            'likes' => 'required|integer|min:0',
            'like_spread' => 'required|integer|min:0', // New validation for the spread
        ]);

        // --- NEW LOGIC FOR CATEGORIES ---
        $postType = $request->input('post_type');
        $contentType = (Str::startsWith($postType, 'cosplay')) ? 'cosplay' : 'anime';
        $isAdult = (Str::endsWith($postType, 'nsfw')) ? 1 : 0;
        // --- END NEW LOGIC ---

        $files = $request->file('attachments');
        $userId = $request->input('user_id');
        $targetLikes = (int)$request->input('likes');
        $likeSpread = (int)$request->input('like_spread'); // Get the new spread value
        $allUserIds = User::pluck('id')->toArray();

        foreach ($files as $file) {
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('posts/images', $filename, 'public');

            // Create the Post with the correct content_type and is_adult_content
            $post = Post::create([
                'user_id' => $userId,
                'text' => 'Artificial post - ' . Str::random(10),
                'status' => 1,
                'content_type' => $contentType,
                'is_adult_content' => $isAdult,
                'price' => 0,
            ]);

            $post->attachments()->create([
                'filename' => 'posts/images/' . $filename,
                'disk' => 'public',
                'type' => 'image',
            ]);

            // Generate "likes" using the new customizable spread
            if ($targetLikes > 0 && !empty($allUserIds)) {
                $likeCount = rand(max(0, $targetLikes - $likeSpread), $targetLikes + $likeSpread);
                $likeCount = min($likeCount, count($allUserIds));
                
                if($likeCount == 0) continue;

                $likingUserKeys = array_rand($allUserIds, $likeCount);
                $likingUserIds = is_array($likingUserKeys) ? array_map(fn($k) => $allUserIds[$k], $likingUserKeys) : [$allUserIds[$likingUserKeys]];

                $reactions = array_map(fn($id) => [
                    'user_id' => $id, 'post_id' => $post->id, 'reaction_type' => 'like',
                    'created_at' => now(), 'updated_at' => now(),
                ], $likingUserIds);

                DB::table('reactions')->insert($reactions);
            }
        }

        return back()->with('success_message', count($files) . ' posts have been seeded successfully!');
    }
}
