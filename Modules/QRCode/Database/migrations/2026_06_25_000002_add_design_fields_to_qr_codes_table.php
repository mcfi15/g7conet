<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->string('dot_style', 30)->default('square')->after('background_color');
            $table->string('eye_style', 30)->default('square')->after('dot_style');
            $table->string('eye_border_color', 7)->nullable()->after('eye_style');
            $table->string('eye_center_color', 7)->nullable()->after('eye_border_color');
            $table->boolean('gradient_enabled')->default(false)->after('eye_center_color');
            $table->string('gradient_start', 7)->nullable()->after('gradient_enabled');
            $table->string('gradient_end', 7)->nullable()->after('gradient_start');
            $table->string('frame_style', 30)->default('panel')->after('gradient_end');
            $table->string('frame_text', 30)->nullable()->after('frame_style');
            $table->string('frame_color', 7)->nullable()->after('frame_text');
            $table->string('frame_text_color', 7)->nullable()->after('frame_color');
            $table->integer('frame_margin')->default(2)->after('frame_text_color');
            $table->integer('frame_font_size')->default(14)->after('frame_margin');
        });
    }

    public function down(): void
    {
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->dropColumn([
                'dot_style', 'eye_style', 'eye_border_color', 'eye_center_color',
                'gradient_enabled', 'gradient_start', 'gradient_end',
                'frame_style', 'frame_text', 'frame_color', 'frame_text_color',
                'frame_margin', 'frame_font_size',
            ]);
        });
    }
};
