<?php

namespace App\Models;

use App\Enums\TradeStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trade extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        "buy_gold_request_id",
        "sell_gold_request_id",
        "amount",
        "price_fee",
        "total_price",
        "status",
        "commission",
    ];
    public array $defaultRelationsForFind=["buyGoldRequest","sellGoldRequest"];

    protected $casts = [
        "status" => TradeStatusEnum::class,
    ];

    public function buyGoldRequest(): BelongsTo
    {
        return $this->belongsTo(GoldRequest::class, 'buy_gold_request_id');
    }

    public function sellGoldRequest(): BelongsTo
    {
        return $this->belongsTo(GoldRequest::class, 'sell_gold_request_id');
    }
}
