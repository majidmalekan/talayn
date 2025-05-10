<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\GoldRequestTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\GoldRequest\IndexGoldRequestRequest;
use App\Http\Requests\GoldRequest\StoreGoldRequestRequest;
use App\Http\Requests\GoldRequest\UpdateGoldRequestRequest;
use App\Http\Resources\GoldRequest\GoldRequestCollection;
use App\Http\Resources\GoldRequest\GoldRequestResource;
use App\Services\GoldRequestService;
use App\Traits\TradeTrait;
use App\Traits\WalletTrait;
use Illuminate\Http\JsonResponse;

class GoldRequestController extends Controller
{
    use TradeTrait,WalletTrait;
    public function __construct(protected GoldRequestService $goldRequestService)
    {
    }

    /**
     * Display a listing of the resource.
     * @param IndexGoldRequestRequest $request
     * @return JsonResponse
     */
    public function index(IndexGoldRequestRequest $request): JsonResponse
    {
        return success('',new GoldRequestCollection($this->goldRequestService->index($request)));
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreGoldRequestRequest $request
     * @return JsonResponse
     */
    public function store(StoreGoldRequestRequest $request): JsonResponse
    {
        try {
            $input = $request->validated();
            $input["remaining_amount"] = $request->post('amount');
            $input["user_id"] = $request->user()->id;
            $input["price_fee"]=convertTomanToRial($input["price_fee"]);
            $goldRequest=$this->goldRequestService->create($input);
            $GoldRequests=$this->goldRequestService->findMatchingBuyGoldRequest($input);
            $this->trading($GoldRequests,$goldRequest);
            return success('', $goldRequest);
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
            return success('',new GoldRequestResource($this->goldRequestService->find($id,['user_id'=>auth('sanctum')->id()])));
        }catch (\Exception $exception){
            return failed($exception->getMessage(),$exception->getCode());
        }
    }

    /**
     * Update the specified resource in storage.
     * @param UpdateGoldRequestRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(UpdateGoldRequestRequest $request, string $id): JsonResponse
    {
        try {
            $inputs = $request->validated();
            return success('',new GoldRequestResource($this->goldRequestService->updateAndFetch($id, $inputs,['user_id'=>auth('sanctum')->id()])));
        } catch (\Exception $exception) {
            return failed($exception->getMessage(),$exception->getCode());
        }
    }
}
