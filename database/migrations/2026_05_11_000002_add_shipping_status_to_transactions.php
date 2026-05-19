<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    public function up(): void
    {
        // Add 'shipping' to the transaction status enum
        DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('pending', 'paid', 'processing', 'shipping', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending'");
    }
    public function down(): void
    {
        DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('pending', 'paid', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending'");
    }
};
