<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpnameDetail extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $fillable = [
        'stock_opname_id',
        'product_id',
        'stok_sistem',
        'stok_fisik',
        'selisih',
        'jenis_selisih',
        'harga_satuan',
        'total_harga',
        'alasan',
        'catatan',
    ];
}
