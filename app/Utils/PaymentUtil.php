<?php

namespace App\Utils;

class PaymentUtil
{
    public static function ambilRekening($metodeBayar)
    {
        $rekeningKredit = '1.1.01.01';
        if ($metodeBayar == 'transfer') {
            $rekeningKredit = '1.1.01.03';
        }

        $rekeningDebit = '1.1.03.01';

        return [
            'rekening_kredit' => $rekeningKredit,
            'rekening_debit' => $rekeningDebit,
        ];
    }
}
