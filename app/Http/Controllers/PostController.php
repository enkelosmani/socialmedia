<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Resources\EmptyResource;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Http\Resources\PostCollection;
use App\Services\PostService;
use Exception;
use Illuminate\Http\Request;

class PostController extends Controller
{
    protected PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * Display a listing of the resource.
     */
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $post = $this->postService->save($request);
            return $this->created(new PostResource($post));
        } catch (Exception $e) {
            return $this->respondError('Something went wrong', $e);
        }
    }

    /**
     * Display the specified resource.
     */
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $post = $this->postService->find($id);
            if ($post) {
                $this->postService->delete($id);
                return $this->deleted(new EmptyResource());
            }
        } catch (Exception $e) {
            return $this->respondError('Something went wrong', $e);
        }
    }
}
