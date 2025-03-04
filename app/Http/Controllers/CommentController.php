<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User; // Add User model
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, $postId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'text' => 'required|string|max:255',
        ]);

        $comment = Comment::create([
            'post_id' => $postId,
            'user_id' => $request->user_id,
            'text' => $request->text,
        ]);

        // Load the user relationship
        $comment->load('user');

        return response()->json(['comment' => $comment], 201);
    }

    public function index()
    {
        // Eager load comments and their users
        $posts = Post::with(['user', 'comments.user', 'likes', 'content'])->get();

        return response()->json([
            'status' => 200,
            'result' => ['data' => $posts]
        ]);
    }

    // If you have a show method for a single post
    public function show($id)
    {
        $post = Post::with(['user', 'comments.user', 'likes', 'content'])->findOrFail($id);
        return response()->json($post);
    }
}
