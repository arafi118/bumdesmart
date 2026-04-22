<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

DB::transaction(function () {
    $profits = DB::table('payments')->where('jenis_transaksi', 'sale')->where('no_pembayaran', 'like', '%-PROFIT')->get();
    foreach ($profits as $profit) {
        $baseInvoice = str_replace('-PROFIT', '', $profit->no_pembayaran);
        $hpp = DB::table('payments')->where('no_pembayaran', $baseInvoice . '-HPP')->first();
        if ($hpp) {
            $totalSale = $profit->total_harga + $hpp->total_harga;
            DB::table('payments')->where('id', $profit->id)->update([
                'no_pembayaran' => $baseInvoice,
                'total_harga' => $totalSale,
                'catatan' => 'Penjualan POS ' . $baseInvoice,
                'rekening_kredit' => '4.1.01.01',
                'rekening_debit' => $profit->rekening_debit
            ]);
            DB::table('payments')->where('id', $hpp->id)->update([
                'catatan' => 'HPP Penjualan POS ' . $baseInvoice,
                'rekening_debit' => '5.1.01.01',
                'rekening_kredit' => '1.1.03.01'
            ]);
            echo "Fixed $baseInvoice to $totalSale\n";
        }
    }
});
