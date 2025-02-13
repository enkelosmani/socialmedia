<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LikeRequest;
use App\Http\Resources\LikeResource;
use App\Http\Traits\Helpers\ApiResponseTrait;
use App\Services\LikeService;
use Exception;
use App\Models\Post;

class LikeController extends Controller
{
    use ApiResponseTrait;

    protected $likeService;

    public function __construct(LikeService $likeService)
    {
        $this->likeService = $likeService;
    }

    /**
     * Like a post.
     */
    public function store(LikeRequest $request, Post $post)
    {
        try {
            $like = $this->likeService->likePost($request->user_id, $post->id);

            if (!$like) {
                return $this->respondError('You have already liked this post.', null, 409);
            }

            return $this->created(new LikeResource($like));
        } catch (Exception $e) {
            return $this->respondError('Something went wrong', $e);
        }
    }

    /**
     * Unlike a post.
     */
    public function destroy(LikeRequest $request, Post $post)
    {
        try {
            $unliked = $this->likeService->unlikePost($request->user_id, $post->id);

            if (!$unliked) {
                return $this->notFound('Like not found');
            }

            return $this->deleted(new EmptyResource());
        } catch (Exception $e) {
            return $this->respondError('Something went wrong', $e);
        }
    }
}
