<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User; // Add User model
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'text' => 'required|string|max:1000',
        ]);

        $comment = new Comment();
        $comment->post_id = $post->id;
        $comment->user_id = $request->user_id;
        $comment->text = $request->text;
        $comment->save();

        $comment->load('user');

        return response()->json([
            'message' => 'Comment created successfully',
            'comment' => [
                'id' => $comment->id,
                'user_id' => $comment->user_id,
                'user' => [
                    'id' => $comment->user->id,
                    'firstname' => $comment->user->firstname,
                    'lastname' => $comment->user->lastname,
                    'avatar' => $comment->user->avatar ?? null,
                ],
                'text' => $comment->text,
                'created_at' => $comment->created_at->toISOString(),
            ]
        ], 201);
    }
}
