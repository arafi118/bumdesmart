<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'user_id',
        'no_pembayaran',
        'tanggal_pembayaran',
        'jenis_transaksi',
        'transaction_id',
        'total_harga',
        'metode_pembayaran',
        'no_referensi',
        'catatan',
        'rekening_debit',
        'rekening_kredit',
    ];
}
