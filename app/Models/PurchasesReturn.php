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
}
