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
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('total_amount'); // price * trade_quantity
            $table->decimal('commission_percent', 4);
            $table->unsignedBigInteger('commission_value');
            $table->unsignedBigInteger('final_amount'); // total_amount + commission_value
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
