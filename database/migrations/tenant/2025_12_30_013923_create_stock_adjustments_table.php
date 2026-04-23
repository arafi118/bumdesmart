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
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('business_id')->constrained('businesses');
            $table->foreignId('user_id')->constrained('users');
            $table->string('no_penyesuaian')->unique();
            $table->dateTime('tanggal_penyesuaian');
            $table->string('jenis_penyesuaian');
            $table->string('status')->default('DRAFT');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
