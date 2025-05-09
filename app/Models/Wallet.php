<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Wallet extends BaseModel
{
    use HasFactory;
    protected $fillable = ["user_id", "balance", "wallet_number","gold_balance"];

    public function user(): BelongsTo
    {
      return $this->belongsTo(User::class);
    }
}
