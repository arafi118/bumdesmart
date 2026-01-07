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
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('business_id')->constrained('businesses');
            $table->foreignId('customer_group_id')->nullable()->constrained('customer_groups');
            $table->string('kode_pelanggan');
            $table->string('nama_pelanggan');
            $table->string('no_hp');
            $table->text('alamat')->nullable();
            $table->string('username')->unique();
            $table->string('password');
            $table->decimal('limit_hutang', 20, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
