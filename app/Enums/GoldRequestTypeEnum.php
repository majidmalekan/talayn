<?php

namespace App\Enums;

enum GoldRequestTypeEnum : string
{
    case BUY='buy';
    case SELL='sell';

    public function label(): string
    {
        return match ($this) {
            self::BUY => __('Buy'),
            self::SELL => __('Sell'),
        };
    }
}
