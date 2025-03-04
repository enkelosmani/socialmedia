<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContentRequest;
use App\Http\Resources\ContentCollection;
use App\Http\Resources\ContentResource;
use App\Models\Content;
use App\Services\ContentService;
use Exception;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    protected ContentService $contentService;

    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $contents = $this->contentService->all();
            if ($contents->isEmpty()) {
                return $this->okNoRecords();
            }
            return $this->okWithCollection(new ContentCollection($contents));
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
    public function store(ContentRequest $contentRequest)
    {
        try {
            $content = $this->contentService->save($contentRequest);
            return $this->created(new ContentResource($content));
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
            $content = $this->contentService->find($id);
            if ($content) {
                return $this->okWithResource(new ContentResource($content));
            }
            return $this->notFound();
        } catch (Exception $e) {
            return $this->respondError('Something went wrong', $e);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Content $content)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$id)
    {
        try {
            $content = $this->contentService->find($id);
            if ($content) {
                $content = $this->contentService->update($request, $id);
                return $this->okWithResource(new ContentResource($content));
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
            $content = $this->contentService->find($id);
            if ($content) {
                $this->contentService->delete($id);
                return $this->deleted(new EmptyResource());
            }
        } catch (Exception $e) {
            return $this->respondError('Something went wrong', $e);
        }
    }
}
