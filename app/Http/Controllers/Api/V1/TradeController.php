<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\StatusEnum;
use App\Enums\TradeStatusEnum;
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
        return success('', new TradeCollection($this->tradeService->index($request)));
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreTradeRequest $request
     * @return JsonResponse
     */
    public function store(StoreTradeRequest $request): JsonResponse
    {
        try {

            $inputs = $request->validated();
            $sellOrder = $this->getGoldRequest($request->post('sell_gold_request_id'));
            $fullPrice = $inputs["amount"] * $sellOrder->price_fee;
            $commission = calculateDynamicCommission($inputs["amount"], $fullPrice);
            $userDecrementBuyPrice = $fullPrice + $commission;
            if ($request->user()->wallet->balance < $userDecrementBuyPrice) {
                return failed('موجودی شما کمتر از مقدار مورد نیاز برای پرداخت این معامله می باشد.', 403);
            }
            $inputs["commission"] = $commission;
            $inputs["total_price"] = $fullPrice;
            $inputs["price_fee"] = $sellOrder->price_fee;
            $inputs["status"] = TradeStatusEnum::COMPLETED->value;
            $userIncrementSellPrice = $fullPrice - $commission;
            $trade=DB::transaction(function () use ($request, $inputs, $userIncrementSellPrice, $userDecrementBuyPrice, $sellOrder) {
                $this->lockForUpdateWallet($inputs['seller_user_id'], $userIncrementSellPrice, $request->post('amount'));
                $this->lockForUpdateWallet($request->user()->id, $userDecrementBuyPrice, $request->post('amount'));
                $trade = $this->tradeService->create($inputs);
                $this->updateGoldRequest($request->post('sell_gold_request_id'),$sellOrder->remaining_amount,$request->post('amount'));
                return $trade;
            });
            return success('Trade successfully created', $trade);
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
        try {
            return success('', new TradeResource($this->tradeService->find($id, ['user_id' => auth('sanctum')->id()])));
        } catch (\Exception $exception) {
            return failed($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * @param int $id
     * @return Model|null
     * @throws \Exception
     */
    protected function getGoldRequest(int $id): ?Model
    {
        return $this->goldRequestService->find($id);
    }

    /**
     * @param int $id
     * @param float $remaining_amount
     * @param float $amount
     * @return int
     */
    protected function updateGoldRequest(int $id, float $remaining_amount,float $amount): int
    {
        $attributes["remaining_amount"] = $remaining_amount - $amount;
        $attributes["status"] = $attributes["remaining_amount"] > 0 ? StatusEnum::ACTIVE->value
            : StatusEnum::COMPLETED->value;
        return $this->goldRequestService->update($id, $attributes);
    }
}
