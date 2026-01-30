<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReturnDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_return_id',
        'sale_detail_id',
        'product_id',
        'jumlah',
        'harga_satuan',
        'sub_total',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
