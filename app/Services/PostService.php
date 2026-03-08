<?php

namespace App\Services;

use App\Models\Post;
use App\Repositories\Contracts\PostRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class PostService
{
    public function __construct(protected PostRepositoryInterface $repository) {}

    public function list(array $filters): LengthAwarePaginator
    {
        return $this->repository->paginate($filters);
    }

    public function show(int $id): Post
    {
        return $this->repository->findById($id);
    }

    public function create(array $data, int $userId): Post
    {
        $data['user_id'] = $userId;
        $data['slug']    = Post::generateUniqueSlug($data['title']);
        $data            = $this->resolvePublishedAt($data);

        if (isset($data['featured_image'])) {
            $data['featured_image'] = $data['featured_image']->store('posts/images', 'public');
        }

        return $this->repository->create($data);
    }

    public function update(Post $post, array $data): Post
    {
        if (isset($data['title']) && $data['title'] !== $post->title) {
            $data['slug'] = Post::generateUniqueSlug($data['title'], $post->id);
        }

        $data = $this->resolvePublishedAt($data, $post);

        if (isset($data['featured_image'])) {
            $this->deleteImage($post->featured_image);
            $data['featured_image'] = $data['featured_image']->store('posts/images', 'public');
        }

        return $this->repository->update($post, $data);
    }

    public function delete(Post $post): bool
    {
        $this->deleteImage($post->featured_image);
        return $this->repository->delete($post);
    }

    private function resolvePublishedAt(array $data, ?Post $post = null): array
    {
        if (! isset($data['status'])) {
            return $data;
        }

        $data['published_at'] = $data['status'] === 'published'
            ? ($post?->published_at ?? now())
            : null;

        return $data;
    }

    private function deleteImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
