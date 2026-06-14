<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->decimal('discount_percentage', 5, 2)->default(0)->after('price');
            $table->decimal('original_price', 12, 2)->nullable()->after('discount_percentage');
            $table->boolean('auto_discount')->default(false)->after('original_price');
            $table->timestamp('discount_expires_at')->nullable()->after('auto_discount');
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['discount_percentage', 'original_price', 'auto_discount', 'discount_expires_at']);
        });
    }
};
