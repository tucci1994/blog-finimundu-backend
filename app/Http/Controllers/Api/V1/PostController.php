<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StorePostRequest;
use App\Http\Requests\Api\V1\UpdatePostRequest;
use App\Http\Resources\Api\V1\PostCollection;
use App\Http\Resources\Api\V1\PostResource;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    public function __construct(protected PostService $postService) {}

    public function index(Request $request): PostCollection
    {
        Gate::authorize('viewAny', Post::class);
        return new PostCollection(
            $this->postService->list(
                $request->only(['status', 'search', 'sort_by', 'sort_order', 'per_page'])
            )
        );
    }

    public function show(Post $post): JsonResponse
    {
        Gate::authorize('view', $post);
        return response()->json(['data' => new PostResource($post->load('author'))]);
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        Gate::authorize('create', Post::class);
        $post = $this->postService->create($request->validated(), $request->user()->id);

        return response()->json([
            'message' => 'Articolo creato con successo.',
            'data'    => new PostResource($post->load('author')),
        ], 201);
    }

    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        Gate::authorize('update', $post);
        $updated = $this->postService->update($post, $request->validated());

        return response()->json([
            'message' => 'Articolo aggiornato con successo.',
            'data'    => new PostResource($updated),
        ]);
    }

    public function destroy(Post $post): JsonResponse
    {
        Gate::authorize('delete', $post);
        $this->postService->delete($post);
        return response()->json(['message' => 'Articolo eliminato con successo.']);
    }
}