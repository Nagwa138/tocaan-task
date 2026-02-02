<?php

namespace App\Architecture\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository
{
    public function __construct(
        public Model $model
    ){}

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function prepareQuery(): Builder
    {
        return $this->model->query();
    }

    public function first(array $conditions = [])
    {
        return $this->prepareQuery()->where($conditions)->first();
    }

    public function update(array $conditions = [], array $data = [])
    {
        $model = $this->model->where($conditions);
        $model->update($data);
        return $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function delete(int $id): void
    {
        $this->model->find($id)->delete();
    }

    public function paginate(array $conditions = [], int $perPage = 10)
    {
        return $this->model->where($conditions)->paginate($perPage);
    }
}
