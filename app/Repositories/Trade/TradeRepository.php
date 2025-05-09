<?php

namespace App\Repositories\Trade;

use App\Models\Trade;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TradeRepository extends BaseRepository implements TradeRepositoryInterface
{
    public function __construct(Trade $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request, int $perPage): LengthAwarePaginator
    {
        return $this->cache->remember(
            $this->getTableName() . '_index_' . ($request->user() ? $request->user()->id : '') . $request->get('page', 1),
            env('CACHE_EXPIRE_TIME'),
            function () use ($request, $perPage) {
                $this->model->query()
                    ->when($request->user(), function ($query) use ($request) {
                        $query->when(!$request->user()->is_admin, function (Builder $query) use ($request) {
                            $query->whereHas('buyGoldRequest', function ($query) use ($request) {
                                $query->where('user_id', $request->user()->id);
                            })->orWhereHas('sellGoldRequest', function ($query) use ($request) {
                                $query->where('user_id', $request->user()->id);
                            });
                        });
                    })
                    ->orderBy($request->get('sort', 'id'), $request->get('direction', 'DESC'))
                    ->paginate($perPage, '*', '', $request->get('page', 1));
            });
    }
}
