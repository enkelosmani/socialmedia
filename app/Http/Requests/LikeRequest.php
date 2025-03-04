<?php

// App/Http/Requests/LikeRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LikeRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Assuming authenticated users can like posts
    }

    public function rules()
    {
        return [
            'user_id' => [
                'required',
                'exists:users,id', // Ensure the user_id exists in the users table
                'unique:likes,user_id,NULL,id,post_id,' . $this->route('post'), // Unique per user and post
            ],
        ];
    }

    public function messages()
    {
        return [
            'user_id.unique' => 'You have already liked this post.',
        ];
    }
}
