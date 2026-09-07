<?php

namespace App\Utils;

use App\Models\Product;
use Carbon\Carbon;

class StokUtil
{
    /**
     * Hitung posisi stok satu produk untuk periode [startDate, endDate].
     *
     * Prinsip: products.stok_aktual adalah KENYATAAN (stok master/fisik sistem).
     * Riwayat mutasi bisa tidak sempurna (import dijalankan ulang, data pra-sistem),
     * sehingga menjumlahkan mutasi saja bisa meleset dari stok master.
     *
     * Karena itu perhitungan DI-ANCHOR ke stok master dan di-unwind mundur
     * menggunakan mutasi yang tercatat:
     *
     * - Stok Akhir  = stok_aktual - (seluruh mutasi BERDATED SETELAH endDate)
     *               -> untuk periode berjalan (endDate >= hari ini) = stok_aktual persis.
     * - Masuk       = mutasi positif dalam periode (pembelian, retur penjualan, penyesuaian/opname naik).
     * - Keluar      = mutasi negatif dalam periode (penjualan, retur pembelian, penyesuaian/opname turun).
     * - Stok Awal   = Stok Akhir - Masuk + Keluar
     *               -> untuk periode pertama setelah migrasi = nilai Stok Awal Migrasi.
     *
     * Identitas Stok Akhir = Stok Awal + Masuk - Keluar selalu terpenuhi.
     *
     * @return array{stok_awal: int, masuk: int, keluar: int, stok_akhir: int}
     */
    public static function stokPeriode(Product $product, Carbon $startDate, Carbon $endDate): array
    {
        $mulai = $startDate->copy()->startOfDay();
        $selesai = $endDate->copy()->endOfDay();

        $stokSekarang = (int) round((float) $product->stok_aktual);

        // Unwind: kembalikan mutasi yang terjadi SETELAH akhir periode
        $mutasiSetelahPeriode = (float) $product->stockMovements()
            ->where('tanggal_perubahan_stok', '>', $selesai)
            ->sum('jumlah_perubahan');

        $stokAkhir = $stokSekarang - (int) round($mutasiSetelahPeriode);

        $masuk = (float) $product->stockMovements()
            ->whereBetween('tanggal_perubahan_stok', [$mulai, $selesai])
            ->where('jumlah_perubahan', '>', 0)
            ->sum('jumlah_perubahan');

        $keluar = (float) abs($product->stockMovements()
            ->whereBetween('tanggal_perubahan_stok', [$mulai, $selesai])
            ->where('jumlah_perubahan', '<', 0)
            ->sum('jumlah_perubahan'));

        $masuk = (int) round($masuk);
        $keluar = (int) round($keluar);

        $stokAwal = $stokAkhir - $masuk + $keluar;

        return [
            'stok_awal' => $stokAwal,
            'masuk' => $masuk,
            'keluar' => $keluar,
            'stok_akhir' => $stokAkhir,
        ];
    }
}
