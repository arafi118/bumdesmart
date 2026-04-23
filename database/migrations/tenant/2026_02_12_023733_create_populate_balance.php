<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Populate balances untuk DEBIT (rekening_debit)
        DB::statement("
            INSERT INTO balances (
                id,
                business_id,
                kode_akun,
                tahun,
                debit_01, kredit_01, debit_02, kredit_02, debit_03, kredit_03,
                debit_04, kredit_04, debit_05, kredit_05, debit_06, kredit_06,
                debit_07, kredit_07, debit_08, kredit_08, debit_09, kredit_09,
                debit_10, kredit_10, debit_11, kredit_11, debit_12, kredit_12,
                created_at,
                updated_at
            )
            SELECT 
                CAST(CONCAT(
                    REPLACE(p.rekening_debit, '.', ''), 
                    p.tahun_payment, 
                    p.business_id
                ) AS UNSIGNED) as id,
                p.business_id,
                p.rekening_debit as kode_akun,
                p.tahun_payment as tahun,
                SUM(CASE WHEN p.bulan_payment = 1 THEN p.total_harga ELSE 0 END) as debit_01,
                0 as kredit_01,
                SUM(CASE WHEN p.bulan_payment = 2 THEN p.total_harga ELSE 0 END) as debit_02,
                0 as kredit_02,
                SUM(CASE WHEN p.bulan_payment = 3 THEN p.total_harga ELSE 0 END) as debit_03,
                0 as kredit_03,
                SUM(CASE WHEN p.bulan_payment = 4 THEN p.total_harga ELSE 0 END) as debit_04,
                0 as kredit_04,
                SUM(CASE WHEN p.bulan_payment = 5 THEN p.total_harga ELSE 0 END) as debit_05,
                0 as kredit_05,
                SUM(CASE WHEN p.bulan_payment = 6 THEN p.total_harga ELSE 0 END) as debit_06,
                0 as kredit_06,
                SUM(CASE WHEN p.bulan_payment = 7 THEN p.total_harga ELSE 0 END) as debit_07,
                0 as kredit_07,
                SUM(CASE WHEN p.bulan_payment = 8 THEN p.total_harga ELSE 0 END) as debit_08,
                0 as kredit_08,
                SUM(CASE WHEN p.bulan_payment = 9 THEN p.total_harga ELSE 0 END) as debit_09,
                0 as kredit_09,
                SUM(CASE WHEN p.bulan_payment = 10 THEN p.total_harga ELSE 0 END) as debit_10,
                0 as kredit_10,
                SUM(CASE WHEN p.bulan_payment = 11 THEN p.total_harga ELSE 0 END) as debit_11,
                0 as kredit_11,
                SUM(CASE WHEN p.bulan_payment = 12 THEN p.total_harga ELSE 0 END) as debit_12,
                0 as kredit_12,
                NOW() as created_at,
                NOW() as updated_at
            FROM (
                SELECT 
                    business_id,
                    rekening_debit,
                    YEAR(tanggal_pembayaran) as tahun_payment,
                    MONTH(tanggal_pembayaran) as bulan_payment,
                    total_harga
                FROM payments
            ) as p
            GROUP BY p.business_id, p.rekening_debit, p.tahun_payment
            ON DUPLICATE KEY UPDATE
                debit_01 = debit_01 + VALUES(debit_01),
                debit_02 = debit_02 + VALUES(debit_02),
                debit_03 = debit_03 + VALUES(debit_03),
                debit_04 = debit_04 + VALUES(debit_04),
                debit_05 = debit_05 + VALUES(debit_05),
                debit_06 = debit_06 + VALUES(debit_06),
                debit_07 = debit_07 + VALUES(debit_07),
                debit_08 = debit_08 + VALUES(debit_08),
                debit_09 = debit_09 + VALUES(debit_09),
                debit_10 = debit_10 + VALUES(debit_10),
                debit_11 = debit_11 + VALUES(debit_11),
                debit_12 = debit_12 + VALUES(debit_12),
                updated_at = NOW()
        ");

        // Populate balances untuk KREDIT (rekening_kredit)
        DB::statement("
            INSERT INTO balances (
                id,
                business_id,
                kode_akun,
                tahun,
                debit_01, kredit_01, debit_02, kredit_02, debit_03, kredit_03,
                debit_04, kredit_04, debit_05, kredit_05, debit_06, kredit_06,
                debit_07, kredit_07, debit_08, kredit_08, debit_09, kredit_09,
                debit_10, kredit_10, debit_11, kredit_11, debit_12, kredit_12,
                created_at,
                updated_at
            )
            SELECT 
                CAST(CONCAT(
                    REPLACE(p.rekening_kredit, '.', ''), 
                    p.tahun_payment, 
                    p.business_id
                ) AS UNSIGNED) as id,
                p.business_id,
                p.rekening_kredit as kode_akun,
                p.tahun_payment as tahun,
                0 as debit_01,
                SUM(CASE WHEN p.bulan_payment = 1 THEN p.total_harga ELSE 0 END) as kredit_01,
                0 as debit_02,
                SUM(CASE WHEN p.bulan_payment = 2 THEN p.total_harga ELSE 0 END) as kredit_02,
                0 as debit_03,
                SUM(CASE WHEN p.bulan_payment = 3 THEN p.total_harga ELSE 0 END) as kredit_03,
                0 as debit_04,
                SUM(CASE WHEN p.bulan_payment = 4 THEN p.total_harga ELSE 0 END) as kredit_04,
                0 as debit_05,
                SUM(CASE WHEN p.bulan_payment = 5 THEN p.total_harga ELSE 0 END) as kredit_05,
                0 as debit_06,
                SUM(CASE WHEN p.bulan_payment = 6 THEN p.total_harga ELSE 0 END) as kredit_06,
                0 as debit_07,
                SUM(CASE WHEN p.bulan_payment = 7 THEN p.total_harga ELSE 0 END) as kredit_07,
                0 as debit_08,
                SUM(CASE WHEN p.bulan_payment = 8 THEN p.total_harga ELSE 0 END) as kredit_08,
                0 as debit_09,
                SUM(CASE WHEN p.bulan_payment = 9 THEN p.total_harga ELSE 0 END) as kredit_09,
                0 as debit_10,
                SUM(CASE WHEN p.bulan_payment = 10 THEN p.total_harga ELSE 0 END) as kredit_10,
                0 as debit_11,
                SUM(CASE WHEN p.bulan_payment = 11 THEN p.total_harga ELSE 0 END) as kredit_11,
                0 as debit_12,
                SUM(CASE WHEN p.bulan_payment = 12 THEN p.total_harga ELSE 0 END) as kredit_12,
                NOW() as created_at,
                NOW() as updated_at
            FROM (
                SELECT 
                    business_id,
                    rekening_kredit,
                    YEAR(tanggal_pembayaran) as tahun_payment,
                    MONTH(tanggal_pembayaran) as bulan_payment,
                    total_harga
                FROM payments
            ) as p
            GROUP BY p.business_id, p.rekening_kredit, p.tahun_payment
            ON DUPLICATE KEY UPDATE
                kredit_01 = kredit_01 + VALUES(kredit_01),
                kredit_02 = kredit_02 + VALUES(kredit_02),
                kredit_03 = kredit_03 + VALUES(kredit_03),
                kredit_04 = kredit_04 + VALUES(kredit_04),
                kredit_05 = kredit_05 + VALUES(kredit_05),
                kredit_06 = kredit_06 + VALUES(kredit_06),
                kredit_07 = kredit_07 + VALUES(kredit_07),
                kredit_08 = kredit_08 + VALUES(kredit_08),
                kredit_09 = kredit_09 + VALUES(kredit_09),
                kredit_10 = kredit_10 + VALUES(kredit_10),
                kredit_11 = kredit_11 + VALUES(kredit_11),
                kredit_12 = kredit_12 + VALUES(kredit_12),
                updated_at = NOW()
        ");
    }

    public function down(): void
    {
        DB::table('balances')->truncate();
    }
};
