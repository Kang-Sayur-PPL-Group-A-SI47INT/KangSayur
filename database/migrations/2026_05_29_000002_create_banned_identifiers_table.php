<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banned_identifiers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('type', ['email', 'phone', 'ktp']);
            $table->string('value', 255);
            $table->unsignedBigInteger('user_user_id');
            $table->unsignedBigInteger('banned_by');
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->unique(['type', 'value']);
            $table->foreign('user_user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('banned_by')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banned_identifiers');
    }
};
