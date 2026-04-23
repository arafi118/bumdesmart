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
        Schema::create('sales_returns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('business_id')->constrained('businesses');
            $table->foreignId('sale_id')->constrained('sales');
            $table->foreignId('user_id')->constrained('users');
            $table->string('no_return')->unique();
            $table->dateTime('tanggal_return');
            $table->double('total_return', 20, 2);
            $table->text('alasan_return')->nullable();
            $table->string('status')->default('COMPLETED');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_returns');
    }
};
