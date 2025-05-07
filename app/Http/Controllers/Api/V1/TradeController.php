<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Trade\StoreTradeRequest;
use App\Http\Requests\Trade\UpdateTradeRequest;
use App\Models\GoldRequest;
use App\Services\TradeService;
use App\Traits\WalletTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TradeController extends Controller
{
    use WalletTrait;
    public function __construct(protected TradeService $tradeService)
    {
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return success('',$this->tradeService->index($request));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTradeRequest $request)
    {

        try {
            $inputs = $request->validated();
            DB::transaction(function () use ($request,$inputs) {
                $sellUser = $sellOrder->user()->lockForUpdate()->first(); // قفل روی کاربر فروشنده
                    $buyUser = $buyOrder->user()->lockForUpdate()->first(); // قفل روی کاربر خریدار
                    $fullPrice = $inputs["amount"] * $sellOrder->price_per_gram;
                    $commission = calculateDynamicCommission($inputs["amount"], $fullPrice);
                    $UserDecrementBuyPrice= $fullPrice + $commission;
                    $userIncrementSellPrice=$fullPrice - $commission;
                    $this->decrementBalanceOfWallet($this->getWalletEntities($request->user()->id),$UserDecrementBuyPrice);
                    $this->incrementBalanceByRelation(,$inputs["amount"]);
                    $sellUser->balance_rial += ($totalPrice - $commission);
                    $this->incrementBalanceOfWallet(,$userIncrementSellPrice);
                    $this->decrementBalanceByRelation();
                    $sellUser->balance_gram -= $matchedAmount;
                    $buyUser->save();
                    $sellUser->save();
                    $this->tradeService->create($inputs);

                    $buyOrder->remaining_gram -= $matchedAmount;
                    $sellOrder->remaining_gram -= $matchedAmount;

                    $buyOrder->status = $buyOrder->remaining_gram > 0 ? 'partial' : 'completed';
                    $sellOrder->status = $sellOrder->remaining_gram > 0 ? 'partial' : 'completed';

                    $buyOrder->save();
                    $sellOrder->save();
                    $remainingToSell = $sellOrder->remaining_gram;
            });
        }catch (\Exception $exception){

        }
    }

    /**
     * Display the specified resource.
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        return success('',$this->tradeService->show($id));
    }

    protected function getWalletEntities($userId)
    {
        $wallet=$this->getWalletByUserId($userId);

    }
}
