<?php

namespace App\Rules;

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
        if ($value < $this->getWalletByUserId(auth('sanctum')->user()->id)) {
            $fail('مقدار طلای شما کمتر از میزان فروش شما می باشد.');
        }
    }
}
