<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class BaseService
{
    /**
     * @var mixed
     */
    public mixed $repository;

    /**
     * @var int
     */
    protected int $perPageLimit = 20;

    /**
     * BaseService constructor.
     *
     * @param $repository
     */
    public function __construct($repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get pagination per-page according to $perPageLimit.
     *
     * @param int $perPage
     *
     * @return int
     */
    private function getPerPage(int $perPage): int
    {
        return ($perPage > $this->perPageLimit) ? $this->perPageLimit : $perPage;
    }

    /**
     * @param int $id
     * @param array $attributes
     * @return bool
     */
    public function update(int $id, array $attributes): bool
    {
        return $this->repository->update($id, $attributes);
    }

    /**
     * @param int $id
     * @param array $attributes
     * @return Model|null
     */
    public function updateAndFetch(int $id, array $attributes): ?Model
    {
        if ($this->update($id, $attributes)) {
            return $this->find($id);
        }
        return null;
    }

    /**
     * @param int $id
     * @return Model|null
     */
    public function find(int $id): ?Model
    {
        return $this->repository->find($id);
    }

    /**
     * @param array $inputs
     * @return Model
     */
    public function create(array $inputs): Model
    {
        return $this->repository->create($inputs);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }


    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function index(Request $request): LengthAwarePaginator
    {
        $perPage = $this->getPerPage((int)$request->query('perPage', $this->perPageLimit));
        return $this->repository->index($request, $perPage);
    }

    /**
     * @param int $id
     * @return Model|null
     */
    public function show(int $id): ?Model
    {
        return $this->repository->show($id);
    }

    /**
     * @param string|int|null $queryParam
     * @return mixed
     */
    public function getAll(string|int $queryParam = null): mixed
    {
        return $this->repository->getAll($queryParam);
    }




}
