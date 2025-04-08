<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Validator;

class PostService
{
    protected $rules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string'
    ];

    public function getAllPosts()
    {
        return Post::with('Owner')->get();
    }

    public function getPostById($id)
    {
        return Post::with('Owner')->find($id);
    }

    public function createPost($data)
    {
        $validator = Validator::make($data, $this->rules);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()
            ];
        }

        $post = Post::create($data);
        return [
            'success' => true,
            'post' => $post
        ];
    }

    public function updatePost($id, $data, $userId)
    {
        $post = Post::find($id);

        if (!$post) {
            return [
                'success' => false,
                'message' => 'Post not found'
            ];
        }

        if ($post->user_id !== $userId) {
            return [
                'success' => false,
                'message' => 'Unauthorized'
            ];
        }

        $validator = Validator::make($data, $this->rules);
        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()
            ];
        }

        $post->update([
            'title' => $data['title'],
            'content' => $data['content']
        ]);

        return [
            'success' => true,
            'post' => $post
        ];
    }

    public function deletePost($id, $userId)
    {
        $post = Post::find($id);

        if (!$post) {
            return [
                'success' => false,
                'message' => 'Post not found'
            ];
        }

        if ($post->user_id !== $userId) {
            return [
                'success' => false,
                'message' => 'Unauthorized'
            ];
        }

        $post->delete();
        return ['success' => true];
    }
}
