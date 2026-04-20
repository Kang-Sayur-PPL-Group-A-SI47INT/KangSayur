<?php

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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id('rating_id');
            $table->tinyInteger('score');
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('listing_listing_id');
            $table->unsignedBigInteger('user_user_id');
            $table->timestamps();

            $table->foreign('listing_listing_id')->references('listing_id')->on('listings')->onDelete('cascade');
            $table->foreign('user_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
