<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('global_settings')->updateOrInsert(
            ['key' => 'daily_invoice_limit'],
            ['value' => '5']
        );

        if (!Schema::hasColumn('invoices', 'user_id')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->after('id');
                $table->string('ip_address', 45)->nullable()->after('user_id');
                $table->timestamp('scheduled_deletion_at')->nullable()->after('currency');
            });
        }
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'ip_address', 'scheduled_deletion_at']);
        });

        DB::table('global_settings')->where('key', 'daily_invoice_limit')->delete();
    }
};
