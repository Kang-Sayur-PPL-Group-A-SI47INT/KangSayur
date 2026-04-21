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
            $table->text('content')->nullable();
            $table->decimal('price', 12, 2);
            $table->integer('quantity')->default(0);
            $table->string('unit', 20)->default('kg');
            $table->enum('status', ['active', 'inactive', 'sold_out'])->default('active');
            $table->text('image')->nullable();
            $table->date('availability_date')->nullable();
            $table->unsignedBigInteger('produce_produce_id');
            $table->unsignedBigInteger('user_user_id');
            $table->timestamps();

            $table->foreign('produce_produce_id')->references('produce_id')->on('produces')->onDelete('cascade');
            $table->foreign('user_user_id')->references('user_id')->on('users')->onDelete('cascade');
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
