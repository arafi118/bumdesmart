<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'sale_id',
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

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function salesReturnDetails()
    {
        return $this->hasMany(SalesReturnDetail::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'transaction_id', 'id')->where('jenis_transaksi', 'sales_return');
    }

    public function stockMovement()
    {
        return $this->hasMany(StockMovement::class, 'reference_id', 'id')->where('reference_type', 'sales_return');
    }
}
