<?php

namespace App\Repositories\GoldRequest;

use App\Enums\StatusEnum;
use App\Models\GoldRequest;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
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
        $buyerGoldRequest=null;
        if ($request->has('buyer_gold_request_id'))
            $buyerGoldRequest = $this->find($request->post('buyer_gold_request_id'));
        return $this->cache->remember(
            $this->getTableName() . '_index_' . ($request->user() ? $request->user()->id : '') . $request->get('page', 1),
            env('CACHE_EXPIRE_TIME'),
            function () use ($request, $perPage, $buyerGoldRequest) {
                $this->model->query()
                    ->when($request->user() && !$request->has('buyer_gold_request_id'), function ($query) use ($request, $buyerGoldRequest) {
                        $query->when(!$request->user()->is_admin, function ($query) use ($request) {
                            $query->where('user_id', $request->user()->id);
                        });
                    })
                    ->when($request->has('buyer_gold_request_id'), function (Builder $query) use ($buyerGoldRequest) {
                        $query->where('price_fee', $buyerGoldRequest?->price_fee)
                            ->where('amount', '>=', $buyerGoldRequest?->remaining_amount)
                            ->where('status', StatusEnum::ACTIVE->value);
                    })
                    ->orderBy($request->get('sort', 'id'), $request->get('direction', 'DESC'))
                    ->paginate($perPage, '*', '', $request->get('page', 1));
            });
    }
}
