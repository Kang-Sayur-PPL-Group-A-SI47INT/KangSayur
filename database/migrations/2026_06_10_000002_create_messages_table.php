<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id('message_id');
            $table->text('content');
            $table->unsignedBigInteger('sender_user_id');
            $table->unsignedBigInteger('receiver_user_id');
            $table->unsignedBigInteger('user_user_id');
            $table->unsignedBigInteger('offer_offer_id');
            $table->timestamps();

            $table->foreign('sender_user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('user_user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('offer_offer_id')->references('offer_id')->on('offers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
