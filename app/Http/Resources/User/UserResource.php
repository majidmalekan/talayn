<?php

namespace App\Http\Resources\User;

use App\Http\Resources\GoldRequest\GoldRequestResource;
use App\Http\Resources\Wallet\WalletResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "email" => $this->email,
            "phone" => $this->phone,
            "wallet" => new WalletResource($this->whenLoaded('wallet')),
            "created_at" => $this->created_at,
        ];
    }
}
