<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->integer('category_id')->nullable()->change();
            $table->unsignedBigInteger('sub_category_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->integer('category_id')->nullable(false)->change();
            $table->unsignedBigInteger('sub_category_id')->default(0)->nullable(false)->change();
        });
    }
};
