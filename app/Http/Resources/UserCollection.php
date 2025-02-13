<?php
namespace App\Http\Resources;

class UserCollection extends AbstractJsonCollection
{
    public function getModelName()
    {
        return 'User';
    }
}
