<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class EmptyResourceCollection extends AbstractJsonCollection
{
    public function getModelName(){
        return "EmptyCollection";
    }
}
