<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\GoldRequest\StoreGoldRequestRequest;
use App\Http\Requests\GoldRequest\UpdateGoldRequestRequest;
use App\Services\GoldRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GoldRequestController extends Controller
{
    public function __construct(protected GoldRequestService $goldRequestService)
    {
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return success('',$this->goldRequestService->index($request));
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
            return success('',$this->goldRequestService->create($input));
        }catch (\Exception $exception){
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
        return success('',$this->goldRequestService->show($id));
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
            $input = $request->validated();
            return success('',$this->goldRequestService->updateAndFetch($id,$input));
        }catch (\Exception $exception){
            return failed($exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        return success('',$this->goldRequestService->delete($id));
    }
}
