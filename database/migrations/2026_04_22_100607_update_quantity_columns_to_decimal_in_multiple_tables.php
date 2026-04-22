<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('stok_minimal', 15, 2)->default(0)->change();
            $table->decimal('stok_aktual', 15, 2)->default(0)->change();
        });

        Schema::table('purchase_details', function (Blueprint $table) {
            $table->decimal('jumlah', 15, 2)->change();
        });

        Schema::table('sale_details', function (Blueprint $table) {
            $table->decimal('jumlah', 15, 2)->change();
        });

        Schema::table('stock_opname_details', function (Blueprint $table) {
            $table->decimal('stok_sistem', 15, 2)->change();
            $table->decimal('stok_fisik', 15, 2)->change();
            $table->decimal('selisih', 15, 2)->change();
        });

        Schema::table('stock_adjustment_details', function (Blueprint $table) {
            $table->decimal('jumlah', 15, 2)->change();
        });

        Schema::table('purchases_return_details', function (Blueprint $table) {
            $table->decimal('jumlah', 15, 2)->change();
        });

        Schema::table('sales_return_details', function (Blueprint $table) {
            $table->decimal('jumlah', 15, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('stok_minimal')->default(0)->change();
            $table->integer('stok_aktual')->default(0)->change();
        });

        Schema::table('purchase_details', function (Blueprint $table) {
            $table->integer('jumlah')->change();
        });

        Schema::table('sale_details', function (Blueprint $table) {
            $table->integer('jumlah')->change();
        });

        Schema::table('stock_opname_details', function (Blueprint $table) {
            $table->integer('stok_sistem')->change();
            $table->integer('stok_fisik')->change();
            $table->integer('selisih')->change();
        });

        Schema::table('stock_adjustment_details', function (Blueprint $table) {
            $table->integer('jumlah')->change();
        });

        Schema::table('purchases_return_details', function (Blueprint $table) {
            $table->integer('jumlah')->change();
        });

        Schema::table('sales_return_details', function (Blueprint $table) {
            $table->integer('jumlah')->change();
        });
    }
};
