<?php

namespace App\Repositories;

use App\Enums\UserRoleEnum;
use App\Traits\DBTransactionLockedTrait;
use App\Traits\TableInformationTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class BaseRepository implements BaseEloquentRepositoryInterface
{
    use TableInformationTrait, DBTransactionLockedTrait;

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
                        $query->when($request->user()->role==UserRoleEnum::User()->value, function ($query) use ($request) {
                            $query->where('user_id', $request->user()->id);
                        });
                    })
                    ->when($request->has('filter'), function ($query) use ($request) {
                        $query->where($request->input('filter'), '=', $request->get('filter_value'));
                    })
                    ->orderBy($request->get('sort', 'id'), $request->get('direction', 'DESC'))
                    ->paginate($perPage, '*', '', $request->get('page', 1));
    }

    /**
     * @param int $id
     * @return Model|null
     */
    public function show(int $id): ?Model
    {
            return $this->model
                ->query()
                ->where('id', $id)
                ->firstOrFail();
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
                    $query->when(request()->user()->role==UserRoleEnum::User()->value, function ($query) {
                        $query->where('user_id', request()->user()->id);
                    });
                })
                ->get();
    }

    /**
     * @param string $attributeName
     * @param int $attributeId
     * @return Model|null
     */
    public function findByForeignId(string $attributeName, int $attributeId): ?Model
    {
        return $this->model
            ->query()
            ->where($attributeName . '_id', $attributeId)
            ->first();
    }

    /**
     * @param string $searchKey
     * @return mixed
     */
    public function search(string $searchKey): mixed
    {
        return $this->model
            ->query()
            ->where('title', 'LIKE', "%" . $searchKey . "%")
            ->orWhere('description', 'LIKE', "%" . $searchKey . "%")
            ->orWhere('slug', 'LIKE', "%" . $searchKey . "%")
            ->orWhereHas('categories', function (Builder $query) use ($searchKey) {
                $query->where('name', 'LIKE', "%" . $searchKey . "%")
                    ->orWhere('slug', 'LIKE', "%" . $searchKey . "%");
            })
            ->orWhereHas('tags', function (Builder $query) use ($searchKey) {
                $query->where('name', 'LIKE', "%" . $searchKey . "%")
                    ->orWhere('slug', 'LIKE', "%" . $searchKey . "%");
            });
    }


}
