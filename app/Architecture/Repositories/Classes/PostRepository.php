<?php

namespace App\Architecture\Repositories\Classes;

use App\Architecture\Repositories\AbstractRepository;
use App\Architecture\Repositories\Interfaces\IPostRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class PostRepository extends AbstractRepository implements IPostRepository
{
    public function listByUser(int $userId, array $filters = []): Collection
    {
        $postsQuery = $this->prepareQuery();
        $postsQuery->where('user_id', $userId);
        if(array_key_exists('status', $filters)) $postsQuery->where('status', $filters['status']);
        if(array_key_exists('start_date', $filters)) $postsQuery->where('scheduled_time', '>=', $filters['start_date']);
        if(array_key_exists('end_date', $filters)) $postsQuery->where('scheduled_time', '<=', $filters['end_date']);
        return $postsQuery->get();
    }

    /**
     * @param array $conditions
     * @return Collection|null
     */
    public function list(array $conditions): ?Collection
    {
        return $this->prepareQuery()->where($conditions)->get();
    }

    /**
     * List ready for publishing posts
     * @return Collection|null
     */
    public function listReadyForPublish(): ?Collection
    {
        return $this->list([
            ['status', 'scheduled'],
            ['scheduled_time', '<=', date('Y-m-d H:i:s')],
        ]);
    }
    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getScheduled(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->prepareQuery()->where('status', 'scheduled')->where('user_id', auth()->id())->get();
    }

    public function paginateByUser(int $userId, int $perPage = 10, array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = $this->prepareQuery()->where('user_id', $userId);
        if(array_key_exists('status', $filters) && $filters['status'] != 'all') $query->where('status', $filters['status']);
        if(array_key_exists('platform_id', $filters) && $filters['platform_id'] && $filters['platform_id'] != 'all') $query->whereHas('platforms', function ($q ) use ($filters) {
            $q->where('platform_id', $filters['platform_id']);
        });
        if(array_key_exists('from_date', $filters) && $filters['from_date']) $query->where('created_at', '>=', $filters['from_date']);
        return $query->paginate($perPage);
    }
}
