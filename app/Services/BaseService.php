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
     * @param array|null $whereAttributes
     * @return bool
     */
    public function update(int $id, array $attributes,array $whereAttributes=null): bool
    {
        return $this->repository->update($id, $attributes,$whereAttributes);
    }

    /**
     * @param int $id
     * @param array $attributes
     * @param array|null $whereAttributes
     * @return Model|null
     * @throws \Exception
     */
    public function updateAndFetch(int $id, array $attributes,array $whereAttributes=null): ?Model
    {
        if ($this->update($id, $attributes,$whereAttributes)) {
            return $this->find($id);
        }
        throw new \Exception(__('serverError.model_not_found'),404);
    }

    /**
     * @param int $id
     * @param array|null $whereAttributes
     * @return Model|null
     * @throws \Exception
     */
    public function find(int $id,array $whereAttributes=null): ?Model
    {
        try {
            return $this->repository->find($id,$whereAttributes);
        }catch (\Exception $exception){
            throw new \Exception(__('serverError.model_not_found'),404);
        }
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
     * @param array|null $whereAttributes
     * @return bool
     * @throws \Exception
     */
    public function delete(int $id,array $whereAttributes=null): bool
    {
        try {
            return $this->repository->delete($id,$whereAttributes);
        }catch (\Exception $exception){
            throw new \Exception(__('serverError.model_not_found'),404);
        }
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
}
