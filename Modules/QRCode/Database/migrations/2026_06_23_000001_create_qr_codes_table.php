<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('global_settings')->updateOrInsert(['key' => 'qr_code_status'], ['value' => '1']);
        DB::table('global_settings')->updateOrInsert(['key' => 'daily_qr_code_limit'], ['value' => '5']);

        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('content');
            $table->string('foreground_color', 7)->default('#000000');
            $table->string('background_color', 7)->default('#FFFFFF');
            $table->integer('size')->default(300);
            $table->string('format', 10)->default('png');
            $table->string('status', 20)->default('enable');
            $table->timestamp('scheduled_deletion_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_codes');
        DB::table('global_settings')->whereIn('key', ['qr_code_status', 'daily_qr_code_limit'])->delete();
    }
};
