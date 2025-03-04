<?php

namespace App\Repositories;

use App\Models\Post;
use App\Repositories\IElequent\IPostRepository;

class PostRepository extends BaseRepository implements IPostRepository
{
    public function __construct(Post $model)
    {
        parent::__construct($model);
    }

}

