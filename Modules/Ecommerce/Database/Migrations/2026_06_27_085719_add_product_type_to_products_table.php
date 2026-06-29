<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('product_type', 20)->default('physical')->after('id');
        });

        DB::table('products')->where('is_digital', 1)->where('license_type', '!=', 'none')->update(['product_type' => 'script']);
        DB::table('products')->where('is_digital', 1)->where('license_type', 'none')->update(['product_type' => 'ebook']);
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('product_type');
        });
    }
};
