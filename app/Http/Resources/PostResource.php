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
            'user'=>$this->user,
            'content'=>$this->content,
            'comments'=>$this->comments,
            'likes'=>$this->likes,
            'likes_count' => $this->likes_count,
            'created_at'=>$this->created_at
        ];
    }
}
