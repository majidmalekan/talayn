<?php

namespace App\Jobs;

use App\Models\GoldRequest;
use App\Services\GoldRequestService;
use App\Traits\TradeTrait;
use App\Traits\WalletTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessGoldTradeJob implements ShouldQueue
{
    use TradeTrait,Dispatchable, InteractsWithQueue, Queueable, SerializesModels,WalletTrait;

    /**
     * Create a new job instance.
     */
    public function __construct(public GoldRequest $goldRequest,public GoldRequestService $goldRequestService)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $attributes = [
            'user_id' => $this->goldRequest->user_id,
            'price_fee' => $this->goldRequest->price_fee,
            'remaining_amount' => $this->goldRequest->remaining_amount,
            'type' => $this->goldRequest->type,
        ];
        $matches = $this->goldRequestService->findMatchingGoldRequests($attributes);
        $this->trading($matches, $this->goldRequest);
    }
}
