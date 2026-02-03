<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustmentDetail extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        'stock_adjustment_id',
        'product_id',
        'jumlah',
        'jenis',
        'harga_satuan',
        'total_harga',
        'alasan',
        'catatan',
    ];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function adjustment()
    {
        return $this->belongsTo(StockAdjustment::class);
    }
}
