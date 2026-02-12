<?php

namespace App\Utils;

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
}
