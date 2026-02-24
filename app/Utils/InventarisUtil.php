<?php

namespace App\Utils;

use App\Models\Balance;
use App\Models\Inventory as ModelsInventory;

class InventarisUtil
{
    public static function nilaiBuku($tgl, $inv)
    {
        $tgl_beli = $inv->tanggal_beli;
        $unit = $inv->jumlah;
        $harga_satuan = $inv->harga_satuan * $unit;
        $umur = $inv->umur_ekonomis;

        if ($inv->kategori == 1 && $inv->jenis == 1) {
            return $harga_satuan;
        }

        $penyusutan = $inv->harga_satuan <= 0 ? 0 : round($harga_satuan / $inv->umur_ekonomis, 2);
        $ak_umur = self::bulan($inv->tanggal_beli, $tgl);
        $ak_susut = $penyusutan * $ak_umur;
        $nilai = $harga_satuan - $ak_susut;

        if ($nilai < 0) {
            return 1;
        }

        return $nilai;
    }

    public static function bulan($start, $end, $periode = 'bulan')
    {
        $startDate = \Carbon\Carbon::parse($start);
        $endDate = \Carbon\Carbon::parse($end);

        $months = ($endDate->year - $startDate->year) * 12 + ($endDate->month - $startDate->month) + 1;

        if ($months < 0) {
            $months = 0;
        }

        switch ($periode) {
            case 'hari':
                return $startDate->diffInDays($endDate);
            case 'bulan':
                return $months;
            case 'tahun':
                return (float) ($months / 12);
        }

        return 0;
    }

    public static function penyusutan($tgl_kondisi, $kategori)
    {
        $ymd = explode('-', $tgl_kondisi);
        $tahun = $ymd[0];
        $bulan = $ymd[1];
        $hari = $ymd[2];
        $th_lalu = $tahun - 1;

        $t_unit = 0;
        $t_harga = 0;
        $t_penyusutan = 0;
        $t_akum_susut = 0;
        $t_nilai_buku = 0;

        $j_unit = 0;
        $j_harga = 0;
        $j_penyusutan = 0;
        $j_akum_susut = 0;
        $j_nilai_buku = 0;

        $inventaris = ModelsInventory::where([
            ['jenis', '1'],
            ['status', '!=', '0'],
            ['tanggal_beli', '<=', $tgl_kondisi],
            ['harga_satuan', '>', '0'],
            ['kategori', $kategori],
        ])->whereNotNull('tanggal_beli')->orderBy('tanggal_beli', 'ASC')->get();

        foreach ($inventaris as $inv) {
            if ($kategori == '1') {
                $t_unit += $inv->jumlah;
                $t_harga += $inv->harga_satuan * $inv->jumlah;
                $t_nilai_buku += $inv->harga_satuan * $inv->jumlah;

                $nilai_buku = $inv->harga_satuan * $inv->jumlah;
                if ($inv->status == 'Dijual' || $inv->status == 'Hapus' || $inv->status == 'jual' || $inv->status == 'hapus') {
                    $nilai_buku = '0';
                }

                if (in_array(strtolower($inv->status), ['dijual', 'jual', 'hilang', 'dihapus', 'hapus'])) {
                    $j_unit += $inv->jumlah;
                    $j_harga += $inv->harga_satuan * $inv->jumlah;
                    $j_nilai_buku += $inv->harga_satuan * $inv->jumlah;
                }
            } else {
                $satuan_susut =
                    $inv->harga_satuan <= 0 ? 0 : round(($inv->harga_satuan * $inv->jumlah) / $inv->umur_ekonomis, 2);
                $pakai_lalu = self::bulan($inv->tanggal_beli, $tahun - 1 .'-12-31');
                $nilai_buku = self::nilaiBuku($tgl_kondisi, $inv);

                if (strtolower($inv->status) != 'baik' && $tgl_kondisi >= $inv->tanggal_validasi) {
                    $umur = self::bulan($inv->tanggal_beli, $inv->tanggal_validasi);
                } else {
                    $umur = self::bulan($inv->tanggal_beli, $tgl_kondisi);
                }

                $_satuan_susut = $satuan_susut;
                if ($umur >= $inv->umur_ekonomis) {
                    $harga = $inv->harga_satuan * $inv->jumlah;
                    $_susut = $satuan_susut * ($inv->umur_ekonomis - 1);
                    $satuan_susut = $harga - $_susut - 1;
                }

                $susut = $satuan_susut * $umur;
                if ($umur >= $inv->umur_ekonomis && $inv->harga_satuan * $inv->jumlah > 0) {
                    $akum_umur = $inv->umur_ekonomis;
                    $_akum_susut = $inv->harga_satuan * $inv->jumlah;
                    $akum_susut = $_akum_susut - 1;
                    $nilai_buku = 1;
                } else {
                    $akum_umur = $umur;
                    $akum_susut = $susut;

                    if ($nilai_buku < 0) {
                        $nilai_buku = 1;
                    }
                }

                $umur_pakai = $akum_umur - $pakai_lalu;
                $penyusutan = $satuan_susut * $umur_pakai;

                if (
                    (in_array(strtolower($inv->status), ['hilang', 'jual', 'dijual', 'hapus', 'dihapus']) && $tgl_kondisi >= $inv->tanggal_validasi)
                ) {
                    $akum_susut = $inv->harga_satuan * $inv->jumlah;
                    $nilai_buku = 0;
                    $penyusutan = 0;
                    $umur_pakai = 0;
                }

                if (strtolower($inv->status) == 'rusak' && $tgl_kondisi >= $inv->tanggal_validasi) {
                    $akum_susut = $inv->harga_satuan * $inv->jumlah - 1;
                    $nilai_buku = 1;
                    $penyusutan = 0;
                    $umur_pakai = 0;
                }

                if (! ($umur_pakai >= 0 && $inv->harga_satuan * $inv->jumlah > 0)) {
                    $umur_pakai = 0;
                    $penyusutan = 0;
                }

                if ($akum_umur == $inv->umur_ekonomis && $umur_pakai > '0') {
                    $penyusutan = $_satuan_susut * ($umur_pakai - 1) + $satuan_susut;
                }

                $t_unit += $inv->jumlah;
                $t_harga += $inv->harga_satuan * $inv->jumlah;
                $t_penyusutan += $penyusutan;
                $t_akum_susut += $akum_susut;
                $t_nilai_buku += $nilai_buku;

                $tahun_validasi = $inv->tanggal_validasi ? substr($inv->tanggal_validasi, 0, 4) : 0;
                if ($nilai_buku == 0 && $tahun_validasi < $tahun && $tahun_validasi > 0) {
                    $j_unit += $inv->jumlah;
                    $j_harga += $inv->harga_satuan * $inv->jumlah;
                    $j_penyusutan += $penyusutan;
                    $j_akum_susut += $akum_susut;
                    $j_nilai_buku += $nilai_buku;
                }
            }
        }

        return $t_akum_susut;
    }

    public static function saldoSusut($tanggal, $kode_akun)
    {
        $ymd = explode('-', $tanggal);
        $y = $ymd[0];
        $m = (int) $ymd[1];

        $balance = Balance::where('kode_akun', $kode_akun)
            ->where('tahun', $y)
            ->first();

        $saldo = 0;
        if ($balance) {
            $saldo += floatval($balance->kredit_00);
            for ($i = 1; $i <= $m; $i++) {
                $col = 'kredit_'.str_pad($i, 2, '0', STR_PAD_LEFT);
                $saldo += floatval($balance->$col);
            }
        }

        return $saldo;
    }
}
