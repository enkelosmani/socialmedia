<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LikeResource extends AbstractJsonResource
{
    public function getModelName(){
        return "Like";
    }

    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'user_id'   => $this->user_id,
            'post_id'   => $this->post_id,
        ];
    }
}
