<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('ratings', function (Blueprint $table) {

            $table->unsignedBigInteger('transaction_transaction_id')->nullable()->after('user_user_id');
            $table->foreign('transaction_transaction_id')
                  ->references('transaction_id')
                  ->on('transactions')
                  ->onDelete('set null');


            $table->unique(['listing_listing_id', 'user_user_id'], 'ratings_listing_user_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->dropUnique('ratings_listing_user_unique');
            $table->dropForeign(['transaction_transaction_id']);
            $table->dropColumn('transaction_transaction_id');
        });
    }
};
