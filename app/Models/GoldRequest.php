<?php

namespace App\Models;

use App\Enums\GoldRequestTypeEnum;
use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoldRequest extends BaseModel
{
    protected $fillable = [
        "user_id",
        "status",
        "type",
        "amount",
        "price_fee"
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
