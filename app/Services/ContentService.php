<?php

namespace App\Services;

use App\Repositories\contentRepository;

class ContentService extends BaseService{
    protected $contentRepository;

    public function __construct(ContentRepository $contentRepository) {
        parent::__construct($contentRepository);
        $this->contentRepository = $contentRepository;
    }
}
