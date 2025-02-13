<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class ContentResource extends AbstractJsonResource
{
    public function getModelName(){
        return "Content";
    }
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'status'=>$this->status,
            'image'=>$this->image,
        ];
    }
}
