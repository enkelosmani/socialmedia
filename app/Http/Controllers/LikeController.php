<?php
namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $user = $request->user(); // Get authenticated user

        $like = Like::firstOrCreate([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        $post->increment('likes_count');

        return response()->json([
            'message' => 'Post liked successfully',
            'like' => $like
        ], 201);
    }

    public function destroy(Request $request, Post $post)
    {
        $user = $request->user();
        $like = Like::where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->first();

        if ($like) {
            $like->delete();
            $post->decrement('likes_count');
            return response()->json(['message' => 'Post unliked successfully'], 200);
        }
        return response()->json(['message' => 'Like not found'], 404);
    }
}
