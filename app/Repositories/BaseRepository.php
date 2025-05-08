<?php

namespace App\Repositories;

use App\Traits\CacheRepositoryTrait;
use App\Traits\DBTransactionLockedTrait;
use App\Traits\TableInformationTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class BaseRepository implements BaseEloquentRepositoryInterface
{
    use TableInformationTrait, DBTransactionLockedTrait, CacheRepositoryTrait;

    /**
     * @var Model
     */

    public Model $model;

    /**
     * @param $model
     */


    public function __construct($model)
    {
        $this->model = $model;
    }


    /**
     * @param int $id
     * @param array $attributes
     * @return bool
     */
    public function update(int $id, array $attributes): bool
    {
        return $this->model->query()
            ->where('id', $id)
            ->update($attributes);
    }


    /**
     * @param int $id
     * @return Model|null
     */
    public function find(int $id): ?Model
    {
        return $this->model
            ->query()
            ->findOrFail($id);
    }


    /**
     * @param array $attributes
     * @return Model
     */
    public function create(array $attributes): Model
    {
        return $this->model
            ->query()
            ->create($attributes);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function delete(int $id): mixed
    {
        return $this->model
            ->query()
            ->where('id', $id)
            ->delete();
    }


    /**
     * @param Request $request
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function index(Request $request, int $perPage): LengthAwarePaginator
    {
        return $this->model->query()
            ->when($request->user(), function ($query) use ($request) {
                $query->when($request->user()->is_admin, function ($query) use ($request) {
                    $query->where('user_id', $request->user()->id);
                });
            })
            ->orderBy($request->get('sort', 'id'), $request->get('direction', 'DESC'))
            ->paginate($perPage, '*', '', $request->get('page', 1));
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

    public function getAll(string|int $queryParam = null): array|Collection
    {
        return $this->model->query()
            ->when(auth()->check(), function ($query) {
                $query->when(!request()->user()->is_admin, function ($query) {
                    $query->where('user_id', request()->user()->id);
                });
            })
            ->get();
    }
}
