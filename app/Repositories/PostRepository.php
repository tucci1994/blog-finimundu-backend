<?php

namespace App\Repositories;

use App\Models\Post;
use App\Repositories\Contracts\PostRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PostRepository implements PostRepositoryInterface
{
    public function __construct(protected Post $model) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = $this->model->with('author:id,name,email,role');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        $allowed   = ['created_at', 'updated_at', 'published_at', 'title'];
        $sortBy    = in_array($filters['sort_by'] ?? '', $allowed) ? $filters['sort_by'] : 'created_at';
        $sortOrder = ($filters['sort_order'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate(min((int) ($filters['per_page'] ?? 15), 100));
    }

    public function findById(int $id): Post
    {
        return $this->model->with('author:id,name,email,role')->findOrFail($id);
    }

    public function create(array $data): Post
    {
        return $this->model->create($data);
    }

    public function update(Post $post, array $data): Post
    {
        $post->update($data);
        return $post->fresh(['author']);
    }

    public function delete(Post $post): bool
    {
        return (bool) $post->delete();
    }
}
