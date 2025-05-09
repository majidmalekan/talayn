<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Trade\StoreTradeRequest;
use App\Http\Resources\Trade\TradeCollection;
use App\Http\Resources\Trade\TradeResource;
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
        return success('',new TradeCollection($this->tradeService->index($request)));
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
                $this->lockForUpdateWallet($inputs['seller_user_id'],$userIncrementSellPrice,$request->post('amount'));
                $this->lockForUpdateWallet($request->user()->id,$userDecrementBuyPrice,$request->post('amount'));
                $trade=$this->tradeService->create($inputs);
                $attributes["remaining_amount"]=$sellOrder->remaining_amount - $inputs["amount"];
                $attributes["status"] = $attributes["remaining_amount"] > 0 ? StatusEnum::ACTIVE->value
                    : StatusEnum::COMPLETED->value ;
                $this->updateGoldRequest($request->post('sell_gold_request_id'),$attributes);
                return success('Trade successfully created',$trade);
            });
        } catch (\Exception $exception) {
            return failed($exception->getMessage(),$exception->getCode());
        }
    }

    /**
     * Display the specified resource.
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        try {
            return success('',new TradeResource($this->tradeService->find($id,['user_id'=>auth('sanctum')->id()])));
        }catch (\Exception $exception){
            return failed($exception->getMessage(),$exception->getCode());
        }
    }

    protected function getGoldRequest(int $id): ?Model
    {
        return $this->goldRequestService->find($id);
    }

    protected function updateGoldRequest(int $id,array $attributes): int
    {
        return $this->goldRequestService->update($id,$attributes);
    }
}
