<?php

namespace App\Utils;

use App\Models\Product;
use Carbon\Carbon;

class StokUtil
{
    /**
     * Hitung posisi stok satu produk untuk periode [startDate, endDate].
     *
     * Rumus laporan stok per periode:
     * - Stok Awal  = seluruh mutasi stok sebelum tanggal mulai periode (termasuk stok awal migrasi).
     * - Masuk      = mutasi positif dalam periode (pembelian, retur penjualan, penyesuaian/opname naik).
     * - Keluar     = mutasi negatif dalam periode (penjualan, retur pembelian, penyesuaian/opname turun).
     * - Stok Akhir = Stok Awal + Masuk - Keluar.
     *
     * @return array{stok_awal: int, masuk: int, keluar: int, stok_akhir: int}
     */
    public static function stokPeriode(Product $product, Carbon $startDate, Carbon $endDate): array
    {
        $mulai = $startDate->copy()->startOfDay();
        $selesai = $endDate->copy()->endOfDay();

        $masuk = (float) $product->stockMovements()
            ->whereBetween('tanggal_perubahan_stok', [$mulai, $selesai])
            ->where('jumlah_perubahan', '>', 0)
            ->sum('jumlah_perubahan');

        $keluar = (float) abs($product->stockMovements()
            ->whereBetween('tanggal_perubahan_stok', [$mulai, $selesai])
            ->where('jumlah_perubahan', '<', 0)
            ->sum('jumlah_perubahan'));

        $stokAwal = (float) $product->stockMovements()
            ->where('tanggal_perubahan_stok', '<', $mulai)
            ->sum('jumlah_perubahan');

        $stokAwal = (int) round($stokAwal);
        $masuk = (int) round($masuk);
        $keluar = (int) round($keluar);

        return [
            'stok_awal' => $stokAwal,
            'masuk' => $masuk,
            'keluar' => $keluar,
            'stok_akhir' => $stokAwal + $masuk - $keluar,
        ];
    }
}
