<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->text('shipping_proof')->nullable()->after('status');
            $table->timestamp('shipping_proof_uploaded_at')->nullable()->after('shipping_proof');
            $table->timestamp('paid_status_at')->nullable()->after('paid_at');
            $table->timestamp('customer_cancel_deadline')->nullable()->after('paid_status_at');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_proof',
                'shipping_proof_uploaded_at',
                'paid_status_at',
                'customer_cancel_deadline',
            ]);
        });
    }
};
