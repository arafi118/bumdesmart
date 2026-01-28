<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatchMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'batch_id',
        'stock_movement_id',
        'tanggal_perubahan',
        'jenis_transaksi',
        'transaction_detail_id',
        'jumlah',
        'harga_satuan',
    ];

    public function productBatch()
    {
        return $this->belongsTo(ProductBatch::class, 'batch_id');
    }
}
