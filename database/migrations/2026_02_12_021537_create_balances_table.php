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
        Schema::create('balances', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->string('kode_akun');
            $table->string('tahun');

            $table->decimal('debit_01', 20, 2);
            $table->decimal('kredit_01', 20, 2);

            $table->decimal('debit_02', 20, 2);
            $table->decimal('kredit_02', 20, 2);

            $table->decimal('debit_03', 20, 2);
            $table->decimal('kredit_03', 20, 2);

            $table->decimal('debit_04', 20, 2);
            $table->decimal('kredit_04', 20, 2);

            $table->decimal('debit_05', 20, 2);
            $table->decimal('kredit_05', 20, 2);

            $table->decimal('debit_06', 20, 2);
            $table->decimal('kredit_06', 20, 2);

            $table->decimal('debit_07', 20, 2);
            $table->decimal('kredit_07', 20, 2);

            $table->decimal('debit_08', 20, 2);
            $table->decimal('kredit_08', 20, 2);

            $table->decimal('debit_09', 20, 2);
            $table->decimal('kredit_09', 20, 2);

            $table->decimal('debit_10', 20, 2);
            $table->decimal('kredit_10', 20, 2);

            $table->decimal('debit_11', 20, 2);
            $table->decimal('kredit_11', 20, 2);

            $table->decimal('debit_12', 20, 2);
            $table->decimal('kredit_12', 20, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balances');
    }
};
