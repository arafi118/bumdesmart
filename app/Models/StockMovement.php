<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'product_id',
        'tanggal_perubahan_stok',
        'jenis_perubahan',
        'jumlah_perubahan',
        'reference_id',
        'reference_type',
        'catatan',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
