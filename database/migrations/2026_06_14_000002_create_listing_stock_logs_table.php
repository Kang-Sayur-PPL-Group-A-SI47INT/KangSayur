<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listing_stock_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('listing_id');
            $table->integer('quantity');
            $table->string('source'); // 'manual', 'harvest_schedule', 'sale'
            $table->timestamps();

            $table->foreign('listing_id')->references('listing_id')->on('listings')->onDelete('cascade');
            $table->index('listing_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_stock_logs');
    }
};
