<?php

namespace App\Utils;

use App\Models\Account;
use App\Models\AkunLevel1;

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
        $labaRugi = self::labaRugi($tahun, $bulan);

        foreach ($labaRugi as $lr) {
            foreach ($lr['kode'] as $account) {
                dd($account);
            }
        }

        dd($labaRugi);

        return '';
    }

    public static function labaRugi($tahun, $bulan = '00'): array
    {
        $akunLevel1s = AkunLevel1::where([
            ['id', '>=', '4'],
        ])->with([
            'akunLevel2.akunLevel3.accounts' => function ($query) {
                $query->where('business_id', auth()->user()->business_id);
            },
            'akunLevel2.akunLevel3.accounts.balance' => function ($query) use ($tahun) {
                $query->where('tahun', $tahun);
            },
        ])->get();

        $akunPersediaan = Account::where([
            ['business_id', auth()->user()->business_id],
            ['kode', '1.1.03.01'],
        ])->with([
            'balance' => function ($query) use ($tahun) {
                $query->where('tahun', $tahun);
            },
        ])->first();

        $group = [
            '1' => [
                'nama' => 'Laba Kotor',
                'total' => 'Total Pembelian',
            ],
            '2' => [
                'nama' => 'Pendapatan Lain Lain',
                'total' => '',
            ],
            '3' => [
                'nama' => 'Beban Operasional',
                'total' => 'Jumlah Beban Operasional',
            ],
            '4' => [
                'nama' => 'Pendapatan Non Usaha',
                'total' => 'Jumlah Pendapatan Non Usaha',
            ],
            '5' => [
                'nama' => 'Beban Non Usaha',
                'total' => 'Jumlah Beban Non Usaha',
            ],
            '6' => [
                'nama' => 'Beban Pajak',
                'total' => 'Jumlah Beban Pajak',
            ],
        ];

        $group[$akunPersediaan->kode] = [
            'kode' => $akunPersediaan->kode,
            'nama' => $akunPersediaan->nama,
            'saldo_bulan_ini' => self::sumSaldo($akunPersediaan, $bulan),
            'saldo_bulan_lalu' => self::sumSaldo($akunPersediaan, $bulan - 1),
            'saldo_tahun_lalu' => self::sumSaldo($akunPersediaan, '00'),
        ];

        foreach ($akunLevel1s as $akunLevel1) {
            foreach ($akunLevel1->akunLevel2 as $akunLevel2) {
                foreach ($akunLevel2->akunLevel3 as $akunLevel3) {
                    foreach ($akunLevel3->accounts as $account) {
                        $kode = $account->kode;
                        $kode1 = explode('.', $account->kode)[0];
                        $kode2 = explode('.', $account->kode)[1];
                        $kode3 = explode('.', $account->kode)[2];
                        $kode4 = explode('.', $account->kode)[3];

                        $saldo_bulan_ini = self::sumSaldo($account, $bulan);
                        $saldo_bulan_lalu = self::sumSaldo($account, $bulan - 1);
                        $saldo_tahun_lalu = self::sumSaldo($account, '00');

                        $saldo = [
                            'kode' => $account->kode,
                            'nama' => $account->nama,
                            'saldo_bulan_ini' => $saldo_bulan_ini,
                            'saldo_bulan_lalu' => $saldo_bulan_lalu,
                            'saldo_tahun_lalu' => $saldo_tahun_lalu,
                        ];

                        if ($kode1 <= '5' && $kode != '4.1.01.05') {
                            if ($kode == '4.1.01.04' || $kode == '5.1.01.01') {
                                continue;
                            }

                            if ($kode == '5.1.01.02') {
                                $group['1']['kode'][] = $group['1.1.03.01'];
                                unset($group['1.1.03.01']);
                            }

                            $group['1']['kode'][] = $saldo;
                        }

                        if ($kode1 == '6') {
                            $group['3']['kode'][] = $saldo;
                        }

                        if ($kode1 == '7' && $kode2 <= '2') {
                            $group['4']['kode'][] = $saldo;
                        }

                        if ($kode1 == '7' && $kode2 == '3') {
                            $group['5']['kode'][] = $saldo;
                        }

                        if ($kode1 == '7' && $kode2 == '4') {
                            $group['6']['kode'][] = $saldo;
                        }

                        if ($kode == '4.1.01.05') {
                            $group['2']['kode'][] = $saldo;
                        }
                    }
                }
            }
        }

        $labaRugi = [];
        foreach ($group as $value) {
            foreach ($value['kode'] as $kode) {
                if ($kode['kode'] == '1.1.03.01') {
                    dd($kode);
                }

                $labaRugi[] = $kode;
            }
        }

        return $labaRugi;
    }
}
