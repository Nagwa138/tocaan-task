<?php

namespace App\Architecture\Services\Classes;

use App\Architecture\Repositories\Interfaces\IPostRepository;
use App\Architecture\Responder\IApiHttpResponder;
use App\Architecture\Services\Interfaces\IPostService;
use App\Http\Resources\PostCalenderResource;
use App\Http\Resources\PostResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PostService implements IPostService
{
    /**
     * @param IPostRepository $postRepository
     * @param IApiHttpResponder $apiHttpResponder
     */
    public function __construct(
        public IPostRepository $postRepository,
        public IApiHttpResponder $apiHttpResponder
    )
    {}

    /**
     * Create post with platforms
     *
     * @param array $data
     * @return JsonResponse
     */
    public function create(array $data): JsonResponse
    {
        try {
            $post = $this->postRepository->create($data);
            $post->platforms()->attach($data['platforms'], [
                'platform_status' => 'pending',
            ]);
            return $this->apiHttpResponder->sendSuccess(
                (new PostResource($post->load('platforms', 'user'))
                )->toArray(request()), Response::HTTP_CREATED);
        } catch (\Throwable $exception) {
            return $this->apiHttpResponder->sendError($exception->getMessage(), Response::HTTP_BAD_GATEWAY);
        }
    }

    public function listByUser(int $userId, array $filters = []): JsonResponse
    {
        try {
            $posts = $this->postRepository->listByUser($userId, $filters);
            return $this->apiHttpResponder->sendSuccess(PostResource::collection($posts)->toArray(request()), Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->apiHttpResponder->sendError($exception->getMessage(), Response::HTTP_BAD_GATEWAY);
        }
    }

    public function listByUserCalender(): JsonResponse
    {
        try {
            $posts = $this->postRepository->getScheduled();
            return $this->apiHttpResponder->sendSuccess(PostCalenderResource::collection($posts)->toArray(request()), Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->apiHttpResponder->sendError($exception->getMessage(), Response::HTTP_BAD_GATEWAY);
        }
    }

    public function update(int $id, array $data): JsonResponse
    {
        try {
            $post = $this->postRepository->update(['id' => $id], $data);
            return $this->apiHttpResponder->sendSuccess((new PostResource($post->load('platforms', 'user')))->toArray(request()));
        } catch (\Throwable $exception) {
            return $this->apiHttpResponder->sendError($exception->getMessage(), Response::HTTP_BAD_GATEWAY);
        }
    }

    public function delete(int $id): JsonResponse
    {
        try {
            $this->postRepository->delete($id);
            return $this->apiHttpResponder->sendSuccess(['message' => 'Post deletedsuccessfully!']);
        } catch (\Throwable $exception) {
            return $this->apiHttpResponder->sendError($exception->getMessage(), $exception->getCode());
        }
    }
}
