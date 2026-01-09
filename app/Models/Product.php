<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'category_id',
        'brand_id',
        'unit_id',
        'sku',
        'nama_produk',
        'harga_beli',
        'harga_jual',
        'stok_minimal',
        'stok_aktual',
        'metode_biaya',
        'biaya_rata_rata',
        'gambar',
        'is_active',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
