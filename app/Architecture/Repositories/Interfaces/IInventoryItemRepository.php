<?php

namespace App\Architecture\Repositories\Interfaces;

interface IInventoryItemRepository
{
    /**
     * @param array $filters
     * @param int $perPage
     */
    public function list(array $filters, int $perPage);
}
