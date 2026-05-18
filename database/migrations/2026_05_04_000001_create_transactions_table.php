<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id('transaction_id');
            $table->decimal('total_price', 12, 2);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->string('delivery_name', 100);
            $table->string('delivery_phone', 16);
            $table->text('delivery_address');
            $table->enum('status', [
                'pending',
                'paid',
                'processing',
                'shipped',
                'delivered',
                'cancelled',
            ])->default('pending');
            $table->string('midtrans_order_id', 100)->nullable()->unique();
            $table->string('snap_token', 255)->nullable();
            $table->string('payment_type', 50)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('user_user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignId('cart_cart_id')->constrained('carts', 'cart_id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
