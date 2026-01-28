<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasesReturnDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchases_return_id',
        'purchase_detail_id',
        'product_id',
        'jumlah',
        'harga_satuan',
        'sub_total',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stockMovement()
    {
        return $this->hasOne(StockMovement::class, 'reference_id', 'id')->where('reference_type', 'purchases_return');
    }
}
