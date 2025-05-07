<?php

namespace App\Rules;

use App\Enums\GoldRequestTypeEnum;
use App\Models\Wallet;
use App\Repositories\Wallet\WalletRepositoryInterface;
use App\Traits\WalletTrait;
use Closure;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class EnsureUserHasEnoughGold implements ValidationRule
{
    use WalletTrait;

    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param \Closure(string, ?string=): PotentiallyTranslatedString $fail
     * @throws BindingResolutionException
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (request()->post('type') == GoldRequestTypeEnum::SELL->value &&
            $value < $this->getWalletByUserId(auth('sanctum')->user()->id)->gold_balance) {
            $fail('مقدار طلای شما کمتر از میزان درخواستی شما برای  فروش می باشد.');
        }
    }
}
