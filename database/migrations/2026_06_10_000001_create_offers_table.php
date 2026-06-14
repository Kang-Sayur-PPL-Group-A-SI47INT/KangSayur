<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id('offer_id');
            $table->decimal('offered_price', 12, 2);
            $table->decimal('counter_price', 12, 2)->nullable();
            $table->enum('status', ['pending', 'countered', 'accepted', 'rejected'])->default('pending');
            $table->unsignedBigInteger('listing_listing_id');
            $table->unsignedBigInteger('user_user_id');
            $table->timestamps();

            $table->foreign('listing_listing_id')->references('listing_id')->on('listings')->onDelete('cascade');
            $table->foreign('user_user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
