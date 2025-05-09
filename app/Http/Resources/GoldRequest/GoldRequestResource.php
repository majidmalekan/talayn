<?php

namespace App\Http\Resources\GoldRequest;

use App\Enums\GoldRequestTypeEnum;
use App\Enums\StatusEnum;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GoldRequestResource extends JsonResource
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
            "user" => new UserResource($this->whenLoaded('user')),
            "status" => $this->status,
            "status_label" => StatusEnum::{strtoupper($this->status->value)}->label(),
            "type" => $this->type,
            "type_label" => GoldRequestTypeEnum::{strtoupper($this->type->value)}->label(),
            "amount" => $this->amount,
            "price_fee" => convertRialToToman($this->price_fee),
            "created_at" => $this->created_at,
        ];
    }
}
