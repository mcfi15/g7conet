<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('version', 30)->default('1.0.0');
            $table->string('file_path', 500);
            $table->string('file_name', 255);
            $table->bigInteger('file_size')->unsigned()->default(0);
            $table->string('file_hash', 64)->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->string('file_type', 30)->default('source');
            $table->text('changelog')->nullable();
            $table->integer('download_count')->unsigned()->default(0);
            $table->boolean('is_current')->default(true);
            $table->timestamps();
        });

        Schema::create('downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('order_detail_id')->constrained('order_details')->cascadeOnDelete();
            $table->foreignId('file_id')->nullable()->constrained('product_files')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('download_token', 64)->nullable()->index();
            $table->timestamp('downloaded_at')->useCurrent();
        });

        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('license_key', 64)->unique();
            $table->string('license_type', 30)->default('regular');
            $table->json('activated_domains')->nullable();
            $table->integer('activation_limit')->unsigned()->default(1);
            $table->integer('activations_count')->unsigned()->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });

        Schema::create('product_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('file_id')->constrained('product_files')->cascadeOnDelete();
            $table->string('version', 30);
            $table->text('changelog')->nullable();
            $table->timestamp('released_at')->useCurrent();
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_digital')->default(false)->after('status');
            $table->string('demo_url', 500)->nullable()->after('is_digital');
            $table->integer('download_limit')->unsigned()->default(5)->after('demo_url');
            $table->integer('update_support_months')->unsigned()->nullable()->after('download_limit');
            $table->string('license_type', 30)->default('none')->after('update_support_months');
            $table->decimal('regular_price', 28, 8)->nullable()->after('license_type');
            $table->decimal('extended_price', 28, 8)->nullable()->after('regular_price');
            $table->string('file_access', 30)->default('any_purchase')->after('extended_price');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('type', 30)->default('physical')->after('order_status');
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->string('download_token', 64)->nullable()->after('price');
            $table->foreignId('license_id')->nullable()->after('download_token');
        });
    }

    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn(['download_token', 'license_id']);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('type');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'is_digital', 'demo_url', 'download_limit', 'update_support_months',
                'license_type', 'regular_price', 'extended_price', 'file_access',
            ]);
        });
        Schema::dropIfExists('product_updates');
        Schema::dropIfExists('licenses');
        Schema::dropIfExists('downloads');
        Schema::dropIfExists('product_files');
    }
};
