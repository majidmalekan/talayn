<?php

namespace App\Traits;

use App\Enums\GoldRequestTypeEnum;
use App\Enums\StatusEnum;
use App\Enums\TradeStatusEnum;
use App\Repositories\Trade\TradeRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

trait TradeTrait
{
    /**
     * @param Collection $matchingGoldRequests
     * @param Model $goldRequest
     * @return mixed
     */
    public function trading(Collection $matchingGoldRequests, Model $goldRequest): mixed
    {
        $isSell = $goldRequest->type === GoldRequestTypeEnum::SELL->value;

        return DB::transaction(function () use ($matchingGoldRequests, $goldRequest, $isSell) {
            $trades = [];

            foreach ($matchingGoldRequests as $match) {
                if ($goldRequest->remaining_amount <= 0) break;

                $matchedAmount = min($match->remaining_amount, $goldRequest->remaining_amount);
                $fullPrice = $matchedAmount * $goldRequest->price_fee;
                $commission = calculateDynamicCommission($matchedAmount, $fullPrice);

                $buyerRequest = $isSell ? $match : $goldRequest;
                $sellerRequest = $isSell ? $goldRequest : $match;

                $buyerId = $buyerRequest->user_id;
                $sellerId = $sellerRequest->user_id;

                $this->lockForUpdateWallet($buyerId, $fullPrice + $commission, $matchedAmount, true);
                $this->lockForUpdateWallet($sellerId, $fullPrice - $commission, $matchedAmount, false);

                $inputs = $this->readyInputs(
                    $commission,
                    $fullPrice,
                    $buyerRequest,
                    $sellerRequest,
                    $matchedAmount
                );

                $trade = $this->createTrade($inputs);
                $this->updateGoldRequest($goldRequest, $matchedAmount);
                $this->updateGoldRequest($match, $matchedAmount);

                $trades[] = $trade;
            }

            return $trades;
        });
    }

    protected function readyInputs(float $commission, float $fullPrice, Model $buyGoldRequest, Model $sellGoldRequest,float $matchedAmount): array
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
