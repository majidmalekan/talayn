<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Trade\StoreTradeRequest;
use App\Http\Requests\Trade\UpdateTradeRequest;
use App\Models\GoldRequest;
use App\Services\TradeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TradeController extends Controller
{
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
            DB::transaction(function () use ($request) {
                $matchingBuyOrders = GoldRequest::query()->where('type', 'buy')
                    ->where('price_per_gram', '>=', $sellOrder->price_per_gram)
                    ->whereIn('status', ['open', 'partial'])
                    ->orderBy('price_per_gram', 'desc')
                    ->orderBy('created_at')
                    ->lockForUpdate() // 🔐 جلوگیری از تغییر هم‌زمان توسط کاربران دیگر
                    ->get();

                $remainingToSell = $sellOrder->remaining_gram;
                $sellUser = $sellOrder->user()->lockForUpdate()->first(); // قفل روی کاربر فروشنده

                foreach ($matchingBuyOrders as $buyOrder) {
                    if ($remainingToSell <= 0) break;

                    $buyUser = $buyOrder->user()->lockForUpdate()->first(); // قفل روی کاربر خریدار

                    $matchedAmount = min($buyOrder->remaining_gram, $remainingToSell);
                    $totalPrice = $matchedAmount * $sellOrder->price_per_gram;

                    $commission = calculateDynamicCommission($matchedAmount, $totalPrice);

                    // بررسی موجودی خریدار
                    if ($buyUser->balance_rial < ($totalPrice + $commission)) {
                        continue;
                    }

                    // بروزرسانی کیف پول‌ها
                    $buyUser->balance_rial -= ($totalPrice + $commission);
                    $buyUser->balance_gram += $matchedAmount;

                    $sellUser->balance_rial += ($totalPrice - $commission);
                    $sellUser->balance_gram -= $matchedAmount;

                    $buyUser->save();
                    $sellUser->save();

                    Trade::create([
                        'buy_order_id' => $buyOrder->id,
                        'sell_order_id' => $sellOrder->id,
                        'amount_gram' => $matchedAmount,
                        'price_per_gram' => $sellOrder->price_per_gram,
                        'total_price' => $totalPrice,
                        'commission' => $commission,
                    ]);

                    $buyOrder->remaining_gram -= $matchedAmount;
                    $sellOrder->remaining_gram -= $matchedAmount;

                    $buyOrder->status = $buyOrder->remaining_gram > 0 ? 'partial' : 'completed';
                    $sellOrder->status = $sellOrder->remaining_gram > 0 ? 'partial' : 'completed';

                    $buyOrder->save();
                    $sellOrder->save();

                    $remainingToSell = $sellOrder->remaining_gram;
                }
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
}
