<?php
namespace App\Http\Resources;

class PostCollection extends AbstractJsonCollection
{
    public function getModelName()
    {
        return 'Post';
    }
}
