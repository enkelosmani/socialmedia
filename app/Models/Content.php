<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $fillable=[
        'status',
        'image',
    ];

    public function post() {
        return $this->belongsTo(Post::class);
    }
}
