<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    protected $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function index()
    {
        $posts = $this->postService->getAllPosts();
        return response()->json($posts);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['user_id'] = Auth::id();

        $result = $this->postService->createPost($data);

        if (!$result['success']) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $result['errors']
            ], 422);
        }

        return response()->json([
            'message' => 'Post created successfully',
            'post' => $result['post']
        ], 201);
    }

    public function show($id)
    {
        $post = $this->postService->getPostById($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        return response()->json($post);
    }

    public function update(Request $request, $id)
    {
        $result = $this->postService->updatePost($id, $request->all(), Auth::id());

        if (!$result['success']) {
            if (isset($result['errors'])) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $result['errors']
                ], 422);
            }

            $status = $result['message'] === 'Unauthorized' ? 403 : 404;
            return response()->json(['message' => $result['message']], $status);
        }

        return response()->json([
            'message' => 'Post updated successfully',
            'post' => $result['post']
        ]);
    }

    public function destroy($id)
    {
        $result = $this->postService->deletePost($id, Auth::id());

        if (!$result['success']) {
            $status = $result['message'] === 'Unauthorized' ? 403 : 404;
            return response()->json(['message' => $result['message']], $status);
        }

        return response()->json(['message' => 'Post deleted successfully']);
    }
}
