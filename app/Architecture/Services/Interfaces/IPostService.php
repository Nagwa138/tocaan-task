<?php

namespace App\Architecture\Services\Interfaces;

use Illuminate\Http\JsonResponse;

interface IPostService
{
    /**
     * Create new post
     *
     * @param array $data
     * @return JsonResponse
     */
    public function create(array $data): JsonResponse;

    /**
     * List user's posts with filters
     *
     * @param int $userId
     * @param array $filters
     * @return JsonResponse
     */
    public function listByUser(int $userId, array $filters = []): JsonResponse;

    /**
     * List user's posts with filters for calendar
     *
     * @return JsonResponse
     */
    public function listByUserCalender(): JsonResponse;

    /**
     * Update post
     *
     * @param int $id
     * @param array $data
     * @return JsonResponse
     */
    public function update(int $id, array $data): JsonResponse;

    /**
     * Delete a post
     *
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse;
}
