<?php

namespace App\Services;

use App\Repositories\LikeRepository;
use App\Models\Post;

class LikeService
{
    protected $likeRepository;

    public function __construct(LikeRepository $likeRepository)
    {
        $this->likeRepository = $likeRepository;
    }

    public function likePost($userId, $postId)
    {
        $existingLike = $this->likeRepository->findLike($userId, $postId);
        if ($existingLike) return null;

        $like = $this->likeRepository->create([
            'user_id' => $userId,
            'post_id' => $postId,
        ]);

        $post = Post::findOrFail($postId);
        $post->increment('likes_count'); // Single increment
        $post->save();

        return $like;
    }

    public function unlikePost($userId, $postId)
    {
        $like = $this->likeRepository->findLike($userId, $postId);

        if (!$like) {
            return false;
        }

        try {
            $this->likeRepository->delete($like->id);

            $post = Post::findOrFail($postId);
            if (!isset($post->likes_count)) {
                throw new \Exception("likes_count column missing on post ID $postId");
            }
            $post->decrement('likes_count');
            $post->save();

            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to unlike post $postId: " . $e->getMessage(), [
                'user_id' => $userId,
                'post_id' => $postId,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
