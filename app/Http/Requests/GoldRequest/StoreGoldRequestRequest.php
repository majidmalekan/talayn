<?php

namespace App\Http\Requests\GoldRequest;

use App\Enums\GoldRequestTypeEnum;
use App\Enums\StatusEnum;
use App\Rules\EnsureUserHasEnoughGold;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreGoldRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'type'=>['required','string',new Enum(GoldRequestTypeEnum::class)],
            'amount'=>['required',new EnsureUserHasEnoughGold],
            'price_fee'=>['required'],
            'status'=>['sometimes','string',new Enum(StatusEnum::class)],
        ];
    }
}
