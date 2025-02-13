<?php

namespace App\Repositories;

use App\Models\Like;
use Illuminate\Database\Eloquent\Model;

class LikeRepository extends BaseRepository
{
    public function __construct(Like $like)
    {
        parent::__construct($like);
    }

    public function findLike($userId, $postId): ?Model
    {
        return $this->model->where('user_id', $userId)
            ->where('post_id', $postId)
            ->first();
    }
}
