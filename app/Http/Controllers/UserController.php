<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmptyResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\Post;
use App\Http\Resources\PostCollection;
use App\Models\User;
use App\Services\UserService;
use Exception;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $users = $this->userService->all();
            if ($users->isEmpty()) {
                return $this->okNoRecords();
            }
            return $this->okWithCollection(new UserCollection($users));
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
            $user = $this->userService->save($request);
            return $this->created(new UserResource($user));
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
            $user = $this->userService->find($id);
            if ($user) {
                return $this->okWithResource(new UserResource($user));
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
            $user = $this->userService->find($id);
            if ($user) {
                $user = $this->userService->update($request, $id);
                return $this->okWithResource(new UserResource($user));
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
            $user = $this->userService->find($id);
            if ($user) {
                $this->userService->delete($id);
                return $this->deleted(new EmptyResource());
            }
        } catch (Exception $e) {
            return $this->respondError('Something went wrong', $e);
        }
    }
}
