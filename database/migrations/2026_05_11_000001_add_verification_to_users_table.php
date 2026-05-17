<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('verification_status', ['unverified', 'pending', 'verified', 'rejected'])
                  ->default('unverified')
                  ->after('is_public_profile');
            $table->text('rejection_note')->nullable()->after('verification_status');
            $table->string('doc_skp', 255)->nullable()->after('rejection_note');
            $table->string('doc_nib', 255)->nullable()->after('doc_skp');
            $table->string('doc_ktp', 255)->nullable()->after('doc_nib');
            $table->string('doc_skt', 255)->nullable()->after('doc_ktp');
            $table->string('doc_land_cert', 255)->nullable()->after('doc_skt');
            $table->timestamp('verified_at')->nullable()->after('doc_land_cert');
        });
    }
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'verification_status',
                'rejection_note',
                'doc_skp',
                'doc_nib',
                'doc_ktp',
                'doc_skt',
                'doc_land_cert',
                'verified_at',
            ]);
        });
    }
};
