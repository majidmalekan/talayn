<?php

namespace App\Rules;

use App\Repositories\GoldRequest\GoldRequestRepositoryInterface;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class EnsureAmountIsEnoughDueToRemainingAmount implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value > app()->make(GoldRequestRepositoryInterface::class)->find(request()->post('sell_gold_request_id'))?->remaining_amount) {
            $fail('مقدار بافی مانده طلای کاربر از خرید شما کمتر است.');
        }
    }
}
