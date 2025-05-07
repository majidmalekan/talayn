<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Trade\StoreTradeRequest;
use App\Http\Requests\Trade\UpdateTradeRequest;
use App\Models\GoldRequest;
use App\Services\GoldRequestService;
use App\Services\TradeService;
use App\Traits\WalletTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TradeController extends Controller
{
    use WalletTrait;

    public function __construct(protected TradeService $tradeService, protected GoldRequestService $goldRequestService)
    {
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return success('', $this->tradeService->index($request));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTradeRequest $request)
    {

        try {
            $inputs = $request->validated();
            DB::transaction(function () use ($request, $inputs) {
                $sellOrder = $this->getGoldRequest($request->post('sell_gold_request_id'));
                $sellUser = $this->lockForUpdateWallet($inputs['seller_ user_id']);
                $buyUser = $this->lockForUpdateWallet($request->user()->id);
                $fullPrice = $inputs["amount"] * $sellOrder->price_per_gram;
                $commission = calculateDynamicCommission($inputs["amount"], $fullPrice);
                $UserDecrementBuyPrice = $fullPrice + $commission;
                $userIncrementSellPrice = $fullPrice - $commission;
                $this->decrementBalanceOfWallet($request->user()->id, $UserDecrementBuyPrice);
                $this->incrementBalanceByRelation($request->user()->id, $inputs["amount"]);
                $sellUser->balance += ($fullPrice - $commission);
                $this->incrementBalanceOfWallet($request->post('seller_ user_id'), $userIncrementSellPrice);
                $this->decrementBalanceByRelation($request->post('seller_ user_id'),$inputs['amount']);
                $sellUser->balance_gram -= $inputs["amount"];
                $buyUser->save();
                $sellUser->save();
                $this->tradeService->create($inputs);
                $sellOrder->remaining_gram -= $inputs["amount"];
                $sellOrder->status = $sellOrder->remaining_gram > 0 ? 'active' : 'inactive';
            });
        } catch (\Exception $exception) {

        }
    }

    /**
     * Display the specified resource.
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        return success('', $this->tradeService->show($id));
    }

    protected function getGoldRequest(int $id): ?Model
    {
        return $this->goldRequestService->show($id);
    }
}
