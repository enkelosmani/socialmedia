<?php
namespace App\Services;

use App\Repositories\ContentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContentService extends BaseService
{
    protected ContentRepository $contentRepository;

    public function __construct(ContentRepository $contentRepository)
    {
        parent::__construct($contentRepository);
        $this->contentRepository = $contentRepository;
    }

    public function save($request)
    {
        $attributes = $request->except('image'); // Exclude image temporarily

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('content_images', 'public');
            $attributes['image'] = $imagePath;
        }

        return $this->repository->create($attributes);
    }
}
