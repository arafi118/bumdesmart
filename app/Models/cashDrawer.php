<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cashDrawer extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'user_id',
        'tanggal_buka',
        'tanggal_tutup',
        'saldo_awal',
        'saldo_akhir',
        'saldo_akhir_aplikasi',
        'selisih',
        'catatan',
        'status',
    ];

    protected $casts = [
        'tanggal_buka' => 'datetime',
        'tanggal_tutup' => 'datetime',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
