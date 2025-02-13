<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class UserResource extends AbstractJsonResource
{
    public function getModelName(){
        return "User";
    }
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'firstname'=>$this->firstname,
            'lastname'=>$this->lastname,
            'email'=>$this->email
        ];
    }
}
