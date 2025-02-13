<?php
namespace App\Http\Resources;

class ContentCollection extends AbstractJsonCollection
{
    public function getModelName()
    {
        return 'Content';
    }
}
