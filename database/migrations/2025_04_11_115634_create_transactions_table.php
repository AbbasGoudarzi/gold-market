<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sell_order_id');
            $table->foreignId('buy_order_id');
            $table->foreignId('seller_id'); // denormalization
            $table->foreignId('buyer_id'); // denormalization
            $table->decimal('trade_quantity', 8, 3);
            $table->decimal('price', 20, 0);
            $table->decimal('total_amount', 20, 0); // price * trade_quantity
            $table->decimal('seller_fee', 20, 0);
            $table->decimal('buyer_fee', 20, 0);
            $table->decimal('seller_final_amount', 20, 0); // total_amount + fee
            $table->decimal('buyer_final_amount', 20, 0); // total_amount + fee
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
