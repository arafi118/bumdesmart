<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        'business_id',
        'user_id',
        'no_penyesuaian',
        'tanggal_penyesuaian',
        'jenis_penyesuaian',
        'status',
        'catatan',
    ];

    public function details()
    {
        return $this->hasMany(StockAdjustmentDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
