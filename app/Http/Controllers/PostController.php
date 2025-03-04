<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Resources\EmptyResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostCollection;
use App\Services\PostService;
use Exception;

class PostController extends Controller
{
    protected PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function index()
    {
        try {
            $posts = $this->postService->all();
            if ($posts->isEmpty()) {
                return $this->okNoRecords();
            }
            return $this->okWithCollection(new PostCollection($posts));
        } catch (Exception $e) {
            return $this->respondError('Something went wrong', $e);
        }
    }

    public function store(PostRequest $request) // Changed to PostRequest
    {
        try {
            $post = $this->postService->save($request);
            return $this->created(new PostResource($post));
        } catch (Exception $e) {
            return $this->respondError('Something went wrong', $e);
        }
    }

    public function show($id)
    {
        try {
            $post = $this->postService->find($id);
            if ($post) {
                return $this->okWithResource(new PostResource($post));
            }
            return $this->notFound();
        } catch (Exception $e) {
            return $this->respondError('Something went wrong', $e);
        }
    }

    public function update(PostRequest $request, $id) // Optional: Use PostRequest here too
    {
        try {
            $post = $this->postService->find($id);
            if ($post) {
                $post = $this->postService->update($request, $id);
                return $this->okWithResource(new PostResource($post));
            }
            return $this->notFound();
        } catch (Exception $e) {
            return $this->respondError('Something went wrong', $e);
        }
    }

    public function destroy($id)
    {
        try {
            $post = $this->postService->find($id);
            if (!$post) {
                return $this->notFound();
            }

            // Check if the authenticated user owns the post
            if (auth()->user()->id !== $post->user_id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $this->postService->delete($id);
            return response()->json(null, 204); // Return 204 No Content
        } catch (Exception $e) {
            return $this->respondError('Something went wrong', $e);
        }
    }
}
