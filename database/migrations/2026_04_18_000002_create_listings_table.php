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
        Schema::create('listings', function (Blueprint $table) {
            $table->id('listing_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->string('unit')->nullable()->default('kg');
            $table->string('status')->default('active');
            $table->string('image')->nullable();
            $table->unsignedBigInteger('user_user_id');
            $table->unsignedBigInteger('produce_produce_id');
            $table->timestamps();

            $table->foreign('user_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('produce_produce_id')->references('produce_id')->on('produces')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
