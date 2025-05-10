<?php

namespace App\Enums;

enum StatusEnum : string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case COMPLETED = 'completed';

    public function label(): string{
        return match ($this){
            self::ACTIVE => __('enums.gold_request_status.ACTIVE'),
            self::INACTIVE => __('enums.gold_request_status.INACTIVE'),
            self::COMPLETED => __('enums.gold_request_status.COMPLETED'),
        };
    }
}
