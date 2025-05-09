<?php

namespace App\Http\Requests\Trade;

use App\Rules\EnsureAmountIsEnoughDueToRemainingAmount;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTradeRequest extends FormRequest
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
            'sell_gold_request_id' => ['required', 'integer', 'exists:gold_requests,id'],
            "seller_user_id" => ['required', 'integer', 'exists:users,id'],
            'buy_gold_request_id' => ['required', 'integer', 'exists:gold_requests,id'],
            "amount" => ['required', 'numeric', new EnsureAmountIsEnoughDueToRemainingAmount],
        ];
    }
}
