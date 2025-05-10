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
}
