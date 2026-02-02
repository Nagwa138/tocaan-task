<?php

namespace App\Architecture\Repositories\Interfaces;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface IOrderRepository
{
    /**
     * Create new post
     *
     * @param array $data
     * @return Order|Model
     */
    public function create(array $data): Order|Model;

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
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function listBy(array $conditions = [], int $perPage = 10): LengthAwarePaginator;


    public function first(array $conditions);
}
