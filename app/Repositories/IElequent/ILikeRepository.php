<?php

namespace App\Repositories\IElequent;

use Illuminate\Database\Eloquent\Model;

interface ILikeRepository{
    public function findLike($userId, $postId): ?Model ;

}
