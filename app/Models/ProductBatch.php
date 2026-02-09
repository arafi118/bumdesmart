<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBatch extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_kadaluarsa' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
