<?php

namespace App\Architecture\Repositories\Interfaces;

use App\Models\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface IPostRepository
{
    /**
     * Create new post
     *
     * @param array $data
     * @return Post|Model
     */
    public function create(array $data): Post|Model;

    /**
     * List user's posts with filters
     *
     * @param int $userId
     * @param array $filters
     * @return Collection
     */
    public function listByUser(int $userId, array $filters = []): Collection;

    /**
     * Update post
     *
     * @param array $conditions
     * @param array $data
     */
    public function update(array $conditions, array $data);

    /**
     * Delete a post
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void;

    /**
     * @param array $conditions
     * @return Collection|null
     */
    public function list(array $conditions): ?Collection;

    /**
     * List ready for publishing posts
     * @return Collection|null
     */
    public function listReadyForPublish(): ?Collection;

    public function paginate();

    /**
     * Get Auth Scheduled Posts
     *
     * @return Collection
     */
    public function getScheduled(): Collection;

    public function paginateByUser(int $userId, int $perPage = 10, array $filters = []): LengthAwarePaginator;
}
