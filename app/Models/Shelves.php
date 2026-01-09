<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shelves extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'kode_rak',
        'nama_rak',
        'lokasi',
        'kapasitas',
        'aktif',
    ];
}
