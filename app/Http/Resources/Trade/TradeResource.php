<?php

namespace App\Http\Resources\Trade;

use App\Enums\TradeStatusEnum;
use App\Http\Resources\GoldRequest\GoldRequestResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TradeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            "amount"=>$this->amount,
            "price_fee"=>convertRialToToman($this->price_fee),
            "total_price"=>convertRialToToman($this->total_price),
            "commission"=>convertRialToToman($this->commission),
            "status"=>$this->status,
            "buyGoldRequest"=>new GoldRequestResource($this->whenLoaded('buyGoldRequest')),
            "sellGoldRequest"=>new GoldRequestResource($this->whenLoaded('sellGoldRequest')),
            "status_label"=>TradeStatusEnum::{strtoupper($this->status)}->label(),
            "created_at"=>$this->created_at
        ];
    }
}
