<?php

namespace App\Http\Resources\Wallet;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=>$this->id,
            "balance"=>convertRialToToman($this->balance),
            "wallet_number"=>$this->wallet_number,
            "gold_balance"=>$this->gold_balance,
            "created_at"=>$this->created_at
        ];
    }
}
