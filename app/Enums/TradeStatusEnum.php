<?php

namespace App\Enums;

enum TradeStatusEnum: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => __('enums.trade_status.Pending'),
            self::COMPLETED => __('enums.trade_status.Completed'),
            self::CANCELLED => __('enums.trade_status.Cancelled'),
        };
    }
}
