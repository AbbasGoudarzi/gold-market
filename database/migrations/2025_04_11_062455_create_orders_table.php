<?php

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->enum('type', OrderType::values());
            $table->decimal('total_quantity', 8, 3);
            $table->decimal('remaining_quantity', 8, 3);
            $table->enum('status', OrderStatus::values())->default(OrderStatus::OPEN->value);
            $table->decimal('price', 20, 0); // price_per_gram
            $table->decimal('fee_percent', 4);
            $table->timestamps();

            $table->index(['status', 'type', 'price']); // Use for matching
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
