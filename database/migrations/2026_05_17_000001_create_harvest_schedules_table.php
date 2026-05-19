<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('harvest_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('listing_id');
            $table->date('availability_date');
            $table->integer('estimated_stock');
            $table->timestamps();

            $table->unique(['listing_id', 'availability_date']);
            $table->foreign('listing_id')->references('listing_id')->on('listings')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('harvest_schedules');
    }
};
