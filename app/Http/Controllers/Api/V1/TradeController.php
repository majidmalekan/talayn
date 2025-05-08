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
                $fullPrice = $inputs["amount"] * $sellOrder->price_fee;
                $commission = calculateDynamicCommission($inputs["amount"], $fullPrice);
                $userDecrementBuyPrice = $fullPrice + $commission;
                $userIncrementSellPrice = $fullPrice - $commission;
                $this->lockForUpdateWallet($inputs['seller_ user_id'],$userIncrementSellPrice,$request->post('amount'));
                $this->lockForUpdateWallet($request->user()->id,$userDecrementBuyPrice,$request->post('amount'));
                $trade=$this->tradeService->create($inputs);
                $newRemain=$sellOrder->remaining_gram -= $inputs["amount"];
                $sellOrder->status = $newRemain > 0 ? 'active' : 'completed';
                return success('Trade successfully created',$trade);
            });
        } catch (\Exception $exception) {
            return failed($exception->getMessage());
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
