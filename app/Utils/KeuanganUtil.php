<?php

namespace App\Utils;

use App\Models\Account;

class KeuanganUtil
{
    public static function sumSaldo($account, $bulan = '00'): string
    {
        $saldo = 0;
        if ($account->balance) {
            $bulan = intval($bulan);
            for ($i = 0; $i <= $bulan; $i++) {
                $kolomDebit = 'debit_'.str_pad($i, 2, '0', STR_PAD_LEFT);
                $kolomKredit = 'kredit_'.str_pad($i, 2, '0', STR_PAD_LEFT);

                $saldoAkun = $account->balance->$kolomDebit - $account->balance->$kolomKredit;
                if ($account->jenis_mutasi == 'kredit') {
                    $saldoAkun = $account->balance->$kolomKredit - $account->balance->$kolomDebit;
                }

                $saldo += $saldoAkun;
            }
        }

        return $saldo;
    }

    public static function sumLabaRugi($tahun, $bulan = '00'): string
    {
        $accounts = Account::where([
            ['business_id', auth()->user()->business_id],
            ['parent_id', '>=', '400'],
        ])->with([
            'balance' => function ($query) use ($tahun) {
                $query->where('tahun', $tahun);
            },
        ])->get();

        $pendapatan = 0;
        $hpp = 0;
        $beban = 0;
        $pendapatanDanBebanNonUsaha = 0;
        foreach ($accounts as $account) {
            if ($account->parent_id >= '400' && $account->parent_id < '500') {
                $pendapatan = 0;
            }

            if ($account->parent_id >= '500' && $account->parent_id < '600') {
                $beban = 0;
            }

            if ($account->parent_id >= '600' && $account->parent_id < '700') {
                $hpp = 0;
            }

            if ($account->parent_id >= '700') {
                $pendapatanDanBebanNonUsaha = 0;
            }
        }

        return '0';
    }
}
