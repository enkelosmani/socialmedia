<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Content;
use App\Repositories\PostRepository;

class PostService extends BaseService
{
    protected $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        parent::__construct($postRepository);
        $this->postRepository = $postRepository;
    }

    public function save($request)
    {
        $postData = [
            'title' => $request->input('title'),
            'user_id' => $request->input('user_id'),
            'likes_count' => 0,
        ];
        $post = $this->postRepository->create($postData);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
            $content = new Content();
            $content->post_id = $post->id;
            $content->data = ['image' => $imagePath]; // Only image in data
            $content->save();
        }

        return $post->load('content', 'user', 'comments', 'likes');
    }
    // app/Services/PostService.php
    public function delete($id)
    {
        $post = $this->postRepository->find($id);
        if ($post) {
            // Delete associated content if it exists
            if ($post->content) {
                $post->content->delete();
            }
            $post->delete();
        }
    }
}
