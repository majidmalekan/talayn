<?php

namespace App\Models;

class Commission extends BaseModel
{
    protected $fillable = [
        "from_gram",
        "to_gram",
        "percent",
    ];
}
