<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request,Post $post){
        $comment = new Comment();
        $comment->post_id = $post->id;
        $comment->user_id = $request->user_id;
        $comment->text = $request->text;
        $comment->save();

        return response()->json([
            'message' => 'Comment created successfully',
            'comment' => $comment
        ], 201);
    }
}
