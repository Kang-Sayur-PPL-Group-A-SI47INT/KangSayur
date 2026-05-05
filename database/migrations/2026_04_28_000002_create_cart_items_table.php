<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id('cart_item_id');
            $table->unsignedInteger('quantity')->default(1);
            $table->foreignId('cart_cart_id')->constrained('carts', 'cart_id')->onDelete('cascade');
            $table->foreignId('listing_listing_id')->constrained('listings', 'listing_id')->onDelete('cascade');
            $table->unsignedBigInteger('offer_offer_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
