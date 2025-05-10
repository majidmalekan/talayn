<?php

namespace App\Enums;

enum GoldRequestTypeEnum : string
{
    case BUY='buy';
    case SELL='sell';

    public function label(): string
    {
        return match ($this) {
            self::BUY => __('enums.gold_request_type.Buy'),
            self::SELL => __('enums.gold_request_type.Sell'),
        };
    }
}
