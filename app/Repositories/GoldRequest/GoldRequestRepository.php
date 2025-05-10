<?php

namespace App\Repositories\GoldRequest;

use App\Enums\GoldRequestTypeEnum;
use App\Enums\StatusEnum;
use App\Models\GoldRequest;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class GoldRequestRepository extends BaseRepository implements GoldRequestRepositoryInterface
{
    public function __construct(GoldRequest $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request, int $perPage): LengthAwarePaginator
    {
        return $this->cache->remember(
            $this->getTableName() . '_index_' . ($request->user() ? $request->user()->id : '') . $request->get('page', 1),
            env('CACHE_EXPIRE_TIME'),
            function () use ($request, $perPage) {
                return $this->model->query()
                    ->when($request->user(), function ($query) use ($request) {
                        $query->when(!$request->user()->is_admin, function ($query) use ($request) {
                            $query->where('user_id', $request->user()->id);
                        });
                    })
                    ->orderBy($request->get('sort', 'id'), $request->get('direction', 'DESC'))
                    ->paginate($perPage, '*', '', $request->get('page', 1));
            });
    }

    /**
     * @param array $attributes
     * @return Collection
     */
    public function findMatchingGoldRequests(array $attributes): Collection
    {
        return $this->model->query()
            ->when($attributes["type"] == GoldRequestTypeEnum::SELL->value, function ($query) use ($attributes) {
                $query->where('remaining_amount', '<=', $attributes['remaining_amount'])
                    ->where('type', GoldRequestTypeEnum::BUY->value);

            })->when($attributes["type"] == GoldRequestTypeEnum::BUY->value, function ($query) use ($attributes) {
                $query->where('remaining_amount', '>=', $attributes['remaining_amount'])
                    ->where('type', GoldRequestTypeEnum::SELL->value)
                    ->join('wallets', 'wallets.user_id', '=', 'gold_requests.user_id')
                    ->whereRaw('wallets.balance > gold_requests.amount * gold_requests.price_fee')
                    ->select('gold_requests.*');

            })
            ->where('price_fee', $attributes['price_fee'])
            ->where('status', StatusEnum::ACTIVE->value)
            ->where('user_id', '!=', $attributes['user_id'])
            ->lockForUpdate()
            ->get();
    }
}
