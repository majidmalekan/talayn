<?php

namespace App\Enums;

enum StatusEnum : string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case COMPLETED = 'completed';

    public function label(): string{
        return match ($this){
            self::ACTIVE => __('Active'),
            self::INACTIVE => __('Inactive'),
            self::COMPLETED => __('Completed'),
        };
    }
}
