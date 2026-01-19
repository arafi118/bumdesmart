<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_pembelian',
        'tanggal_pembelian',
        'business_id',
        'supplier_id',
        'user_id',
        'jenis_pembayaran',
        'subtotal',
        'jenis_diskon',
        'jumlah_diskon',
        'jenis_cashback',
        'jumlah_cashback',
        'jumlah_pajak',
        'total',
        'dibayar',
        'kembalian',
        'jumlah_utang',
        'status',
        'keterangan',
    ];

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class);
    }
}
