<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Morilog\Jalali\Jalalian;

class BaseModel extends Model
{
    use HasFactory;

    public function getCreatedAtAttribute($value): string
    {

        return Jalalian::fromCarbon(Carbon::createFromTimestamp(strtotime($value))
            ->timezone('Asia/Tehran')
        )->toString();
    }

    public function getUpdatedAtAttribute($value): string
    {
        return Jalalian::fromCarbon(Carbon::createFromTimestamp(strtotime($value))
            ->timezone('Asia/Tehran')
        )->toString();
    }

    protected function hasColumn(): bool
    {
        return Schema::hasColumn($this->getTable(), 'status');
    }
}
