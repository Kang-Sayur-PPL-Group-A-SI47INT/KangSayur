<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produce', function (Blueprint $table) {
            $table->id('produce_id');
            $table->string('name', 45);
            $table->string('description', 500)->nullable();
            $table->string('price', 45);
            $table->string('category', 45);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produce');
    }
};
