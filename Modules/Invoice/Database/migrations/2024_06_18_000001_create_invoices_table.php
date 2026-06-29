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
            ['key' => 'invoice_status'],
            ['value' => '1']
        );

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 100)->unique();
            $table->string('business_name')->nullable();
            $table->string('business_email')->nullable();
            $table->text('business_address')->nullable();
            $table->string('client_name');
            $table->string('client_email')->nullable();
            $table->text('client_address')->nullable();
            $table->json('items');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_percentage', 5, 2)->nullable()->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->nullable()->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('status')->default('enable');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
