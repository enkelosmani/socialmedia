<?php

namespace App\Services;

use App\Repositories\LikeRepository;

class LikeService extends BaseService
{
    protected $likeRepository;

    public function __construct(LikeRepository $likeRepository)
    {
        parent::__construct($likeRepository);
        $this->likeRepository = $likeRepository;
    }

    public function likePost($userId, $postId)
    {
        $existingLike = $this->likeRepository->findLike($userId, $postId);

        if ($existingLike) {
            return null;
        }

        // Save the like
        return $this->likeRepository->create([
            'user_id' => $userId,
            'post_id' => $postId,
        ]);
    }

    public function unlikePost($userId, $postId)
    {
        $like = $this->likeRepository->findLike($userId, $postId);

        if (!$like) {
            return false;
        }

        return $this->likeRepository->delete($like->id);
    }
}
