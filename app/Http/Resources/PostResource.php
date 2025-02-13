<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PostResource extends AbstractJsonResource
{
    public function getModelName(){
        return "Post";
    }
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'title'=>$this->title,
            'user_id'=>$this->user_id,
            'content_id'=>$this->content_id,
            'comments'=>$this->comments,
            'likes'=>$this->likes,
        ];
    }
}
