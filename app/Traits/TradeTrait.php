<?php

namespace App\Traits;

use App\Enums\StatusEnum;
use App\Enums\TradeStatusEnum;
use App\Repositories\Trade\TradeRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait TradeTrait
{
    /**
     * @param Collection $buyGoldRequests
     * @param Model|null $sellGoldRequest
     * @return mixed
     */
    public function trading(Collection $buyGoldRequests, Model|null $sellGoldRequest): mixed
    {
        $remainingToSell = $sellGoldRequest->remaining_amount;
        return DB::transaction(function () use ($buyGoldRequests, $sellGoldRequest, $remainingToSell) {
            foreach ($buyGoldRequests as $buyGoldRequest) {
                if ($remainingToSell <= 0) break;
                $matchedAmount = min($buyGoldRequest->remaining_amount, $remainingToSell);
                $fullPrice = $matchedAmount * $sellGoldRequest?->price_fee;
                $commission = calculateDynamicCommission($matchedAmount, $fullPrice);
                $userDecrementBuyPrice = $fullPrice + $commission;
                $userIncrementSellPrice = $fullPrice - $commission;
                $this->lockForUpdateWallet($sellGoldRequest->user_id, $userIncrementSellPrice, $matchedAmount);
                $this->lockForUpdateWallet($buyGoldRequest->user_id, $userDecrementBuyPrice, $matchedAmount);
                $inputs=$this->readyInputs($commission, $fullPrice, $buyGoldRequest, $matchedAmount, $sellGoldRequest);
                $trade = $this->createTrade($inputs);
                $this->updateGoldRequest($sellGoldRequest, $matchedAmount);
                return $trade;
            }
        });
    }

    protected function readyInputs(float $commission, float $fullPrice, Model $buyGoldRequest, float $matchedAmount, Model $sellGoldRequest): array
    {
        return [
            "commission" => $commission,
            "total_price" => $fullPrice,
            "price_fee" => $buyGoldRequest->price_fee,
            "status" => TradeStatusEnum::COMPLETED->value,
            "amount" => $matchedAmount,
            "sell_gold_request_id" => $sellGoldRequest->id,
            "buy_gold_request_id" => $buyGoldRequest->id
        ];
    }

    /**
     * @param array $inputs
     * @return Model
     * @throws BindingResolutionException
     */
    protected function createTrade(array $inputs): Model
    {
        return app()
            ->make(TradeRepositoryInterface::class)
            ->create($inputs);

    }

    protected function updateGoldRequest(Model|null $goldRequest, float $amount): bool
    {
        $attributes["remaining_amount"] = $goldRequest->remaining_amount - $amount;
        $attributes["status"] = $attributes["remaining_amount"] > 0 ? StatusEnum::ACTIVE->value
            : StatusEnum::COMPLETED->value;
        return $this->goldRequestService->update($goldRequest->id, $attributes);
    }
}
