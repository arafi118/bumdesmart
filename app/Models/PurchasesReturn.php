<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasesReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'purchase_id',
        'user_id',
        'no_return',
        'tanggal_return',
        'total_return',
        'alasan_return',
        'status',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function purchasesReturnDetails()
    {
        return $this->hasMany(PurchasesReturnDetail::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'transaction_id', 'id')->where('jenis_transaksi', 'purchase_return');
    }

    public function stockMovement()
    {
        return $this->hasMany(StockMovement::class, 'reference_id', 'id')->where('reference_type', 'purchases_return');
    }
}
