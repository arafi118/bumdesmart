<?php

namespace App\Utils;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class StokUtil
{
    /**
     * Hitung posisi stok satu produk untuk periode [startDate, endDate].
     *
     * Aturan laporan (spec):
     * - Stok Awal  = Stok Awal Migrasi (saldo awal saat import/migrasi) + mutasi sistem sebelum periode.
     * - Masuk      = mutasi positif dalam periode (pembelian, retur penjualan, penyesuaian naik).
     * - Keluar     = mutasi negatif dalam periode (penjualan, retur pembelian, penyesuaian turun).
     * - Stok Akhir = Stok Awal + Masuk - Keluar.
     *
     * Perlakuan khusus data migrasi (reference_type = 'migration'):
     * - Movement migrasi TIDAK dihitung sebagai Masuk; ia adalah Stok Awal.
     * - Movement non-migrasi yang bertanggal SEBELUM movement migrasi pertama produk
     *   diabaikan (riwayat pra-sistem yang sudah "terbake" di dalam snapshot migrasi),
     *   sehingga tidak terjadi double-count antara snapshot dan riwayat historis.
     * - Untuk periode yang berakhir SEBELUM tanggal migrasi, stok migrasi tetap
     *   tampil sebagai Stok Awal/Stok Akhir (stok fisik memang sudah ada).
     *
     * Produk tanpa movement migrasi dihitung kumulatif normal dari seluruh mutasinya.
     *
     * @return array{stok_awal: int, masuk: int, keluar: int, stok_akhir: int}
     */
    public static function stokPeriode(Product $product, Carbon $startDate, Carbon $endDate): array
    {
        $mulai = $startDate->copy()->startOfDay();
        $selesai = $endDate->copy()->endOfDay();

        $movements = $product->stockMovements()
            ->orderBy('tanggal_perubahan_stok')
            ->get(['jumlah_perubahan', 'tanggal_perubahan_stok', 'reference_type']);

        $stokMigrasi = 0;
        $t0 = null; // tanggal movement migrasi pertama = epoch sistem produk ini

        foreach ($movements as $m) {
            if (self::isMigration($m)) {
                $t0 ??= Carbon::parse($m->tanggal_perubahan_stok);
                $stokMigrasi += (int) round((float) $m->jumlah_perubahan);
            }
        }

        $masuk = 0;
        $keluar = 0;
        $netSebelumPeriode = 0;

        foreach ($movements as $m) {
            if (self::isMigration($m)) {
                continue; // migrasi = Stok Awal, bukan mutasi
            }

            $tanggal = Carbon::parse($m->tanggal_perubahan_stok);

            // Riwayat pra-sistem: sudah tercermin di snapshot migrasi, jangan dihitung ulang.
            if ($t0 !== null && $tanggal->lt($t0)) {
                continue;
            }

            $jumlah = (int) round((float) $m->jumlah_perubahan);

            if ($tanggal->lt($mulai)) {
                $netSebelumPeriode += $jumlah;
            } elseif ($tanggal->lte($selesai)) {
                if ($jumlah > 0) {
                    $masuk += $jumlah;
                } else {
                    $keluar += abs($jumlah);
                }
            }
        }

        $stokAwal = $stokMigrasi + $netSebelumPeriode;

        return [
            'stok_awal' => $stokAwal,
            'masuk' => $masuk,
            'keluar' => $keluar,
            'stok_akhir' => $stokAwal + $masuk - $keluar,
        ];
    }

    private static function isMigration($movement): bool
    {
        return ($movement->reference_type ?? '') === 'migration';
    }
}
