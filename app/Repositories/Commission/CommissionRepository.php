<?php

namespace App\Repositories\Commission;

use App\Models\Commission;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;

class CommissionRepository extends BaseRepository implements CommissionRepositoryInterface
{
    public function __construct(Commission $model)
    {
        parent::__construct($model);
    }

    /**
     * @param float $amountGram
     * @return null|Model
     */
    public function firstByRule(float $amountGram): ?Model
    {
        return $this->model->query()->where('from_gram', '<=', $amountGram)
            ->where(function ($q) use ($amountGram) {
                $q->where('to_gram', '>=', $amountGram)->orWhereNull('to_gram');
            })
            ->orderBy('from_gram', 'desc')
            ->first();
    }
}
