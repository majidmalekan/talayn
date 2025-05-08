<?php

namespace App\Models;

use App\Enums\GoldRequestTypeEnum;
use App\Enums\StatusEnum;
use App\Enums\TradeStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoldRequest extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        "user_id",
        "status",
        "type",
        "amount",
        "price_fee",
        "remaining_amount"
    ];

    protected $casts = [
        "status" => StatusEnum::class,
        "type" => GoldRequestTypeEnum::class,
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function buyTrades(): HasMany
    {
        return $this->hasMany(Trade::class, 'buy_gold_request_id');
    }

    public function sellTrades(): HasMany
    {
        return $this->hasMany(Trade::class, 'sell_gold_request_id');
    }

}
