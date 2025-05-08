<?php

use App\Models\GoldRequest;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(GoldRequest::class,'buy_gold_request_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(GoldRequest::class,'sell_gold_request_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount',10,3);
            $table->float('price_fee');
            $table->float('total_price');
            $table->float('commission');
            $table->enum('status',['pending','completed','cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};
