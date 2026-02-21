<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurnal_umum extends Model
{
    use HasFactory;
    protected $table = 'jurnal_umums';
    protected $fillable = [
        'tanggal',
        'keterangan',
        'relasi',
        'jumlah',
        'urutan',
        'user_id',
    ];

    public function getPayment()
    {
        return $this->hasOne(Payment::class, 'transaction_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
